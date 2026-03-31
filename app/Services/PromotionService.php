<?php

namespace App\Services;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Models\PromotionUsageLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Exception;

class PromotionService
{
    protected static ?Collection $activePromotionsCache = null;

    /**
     * Get promotions applicable to a specific product.
     */
    public function getPromotionsForProduct(int $productId): Collection
    {
        $now = now();

        // 💡 ใช้ Internal Cache เพื่อดึงโปรโมชั่นที่ Active ทั้งหมดมาทีเดียวในรอบการทำงานนี้
        if (is_null(self::$activePromotionsCache)) {
            self::$activePromotionsCache = Promotion::with(['rules', 'actions.giftableProducts'])
                ->where('is_active', true)
                ->whereDoesntHave('birthdayPromotion') // ❌ ไม่รวมโปรวันเกิดในหน้ารายการสินค้า
                ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
                ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
                ->get();
        }

        return self::$activePromotionsCache->filter(function ($promo) use ($productId) {
            // ถ้าไม่มีกฎเลย (No Rules) ให้ถือว่าใช้กับสินค้าทุกตัวได้
            if ($promo->rules->isEmpty()) {
                return true;
            }

            // ตรวจสอบกฎของแต่ละโปรโมชั่นว่ามี Product ID นี้หรือไม่
            return $promo->rules->contains(function ($rule) use ($productId) {
                $ruleData = $rule->rules;
                $pids = $ruleData['product_id'] ?? [];
                
                // รองรับทั้งแบบ ID เดี่ยว และแบบ Array
                $pidsArray = is_array($pids) ? $pids : [$pids];
                
                return in_array((string) $productId, array_map('strval', $pidsArray));
            });
        });
    }

    /**
     * Calculate the maximum number of freebies allowed based on applicable promotions.
     */
    public function calculateFreebieLimit(Collection $cartItems, ?Collection $applicablePromotions = null): int
    {
        $promos = $applicablePromotions ?? $this->getApplicablePromotions($cartItems);
        if ($promos->isEmpty()) {
            return 0;
        }

        return $promos->sum(function ($promo) {
            $multiplier = $promo->multiplier ?? 1;
            return $promo->actions->sum(fn ($action) => (int) ($action->actions['quantity_to_get'] ?? 0) * $multiplier);
        });
    }

    /**
     * Calculate the total discount amount for the cart.
     */
    public function calculateTotalDiscount(float $subTotal, Collection $cartItems): float
    {
        if ($cartItems->isEmpty() || $subTotal <= 0) {
            return 0;
        }

        // ดึงโปรโมชั่นที่ผ่านเงื่อนไขพื้นฐานมาทั้งหมด และเรียงตาม Priority (น้อยไปมาก)
        $promos = $this->getApplicablePromotions($cartItems)->sortBy('priority');
        
        $totalDiscount = 0;
        $remainingSubTotal = $subTotal;
        $appliedCode = $this->getAppliedPromoCode(); 

        foreach ($promos as $promo) {
            $isAutoDiscount = !$promo->is_discount_code;
            $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

            if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                $currentPromoDiscount = 0;
                $multiplier = $promo->multiplier ?? 1;

                // 💡 คำนวณส่วนลดจากยอดคงเหลือ (Compound Discount)
                if ($promo->discount_type === 'fixed') {
                    $currentPromoDiscount = (float) $promo->discount_value * $multiplier;
                } elseif ($promo->discount_type === 'percentage') {
                    $currentPromoDiscount = ($remainingSubTotal * ((float) $promo->discount_value / 100));
                }
                
                // ตรวจสอบไม่ให้ส่วนลดเกินราคาสินค้าที่เหลือ
                $currentPromoDiscount = min($currentPromoDiscount, $remainingSubTotal);
                
                $totalDiscount += $currentPromoDiscount;
                $remainingSubTotal -= $currentPromoDiscount;

                // 🛑 ถ้าโปรโมชั่นนี้ไม่อนุญาตให้ใช้ร่วมกับโปรอื่น (Exclusive) ให้หยุดคำนวณทันที
                if (!$promo->is_stackable) {
                    break;
                }
                
                // ถ้าหักจนยอดเหลือ 0 แล้วก็หยุด
                if ($remainingSubTotal <= 0) {
                    break;
                }
            }
        }

        return $totalDiscount;
    }

    /**
     * Apply a promotion code to the current session.
     */
    public function applyPromoCode(string $code): void
    {
        $promo = Promotion::where('code', $code)
            ->where('is_active', true)
            ->where('is_discount_code', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->first();

        // 💡 ถ้าไม่เจอใน Promotion ปกติ ให้ลองหาใน BirthdayPromotion เผื่อว่าใช้รหัสตรงๆ
        if (! $promo) {
            $birthdayPromo = \App\Models\BirthdayPromotion::where('discount_code', $code)->where('is_active', true)->first();
            if ($birthdayPromo && $birthdayPromo->promotion) {
                $promo = $birthdayPromo->promotion;
            }
        }

        if (! $promo) {
            throw new Exception('รหัสส่วนลดไม่ถูกต้อง หรือหมดอายุแล้ว');
        }

        if (Auth::check()) {
            $alreadyUsed = PromotionUsageLog::where('promotion_id', $promo->id)
                ->where('user_id', Auth::id())
                ->whereHas('order', function($q) {
                    $q->where('status_id', '!=', 5); // 5 = ยกเลิก
                })
                ->exists();
            
            if ($alreadyUsed) {
                throw new Exception('คุณเคยใช้รหัสส่วนลดนี้ไปแล้ว ไม่สามารถใช้ซ้ำได้');
            }
        }

        if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
            throw new Exception('ขออภัย! รหัสส่วนลดนี้ถูกใช้ครบจำนวนสิทธิ์แล้ว');
        }

        session(["cart_" . $this->getUserId() . "_promo_code" => $code]);
    }

    /**
     * Remove the applied promotion code from the session.
     */
    public function removePromoCode(): void
    {
        session()->forget("cart_" . $this->getUserId() . "_promo_code");
    }

    /**
     * Get the currently applied promotion code.
     */
    public function getAppliedPromoCode(): ?string
    {
        return session("cart_" . $this->getUserId() . "_promo_code");
    }

    /**
     * Determine all promotions applicable to the given cart items.
     */
    public function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }

        $now = now();
        $subTotal = 0;
        foreach ($cartItems as $item) {
            if (! ($item->attributes['is_freebie'] ?? false)) {
                $subTotal += ($item->price * $item->quantity);
            }
        }

        $cartQuantities = [];
        foreach ($cartItems as $item) {
            if ($item->attributes['is_freebie'] ?? false) {
                continue;
            }

            $realPid = $item->attributes['product_id'] ?? $item->id;
            if (is_string($realPid) && str_contains($realPid, '_')) {
                $realPid = explode('_', $realPid)[0];
            }
            $realPid = (int) $realPid;
            $cartQuantities[$realPid] = ($cartQuantities[$realPid] ?? 0) + $item->quantity;
        }
        $cartQuantities = collect($cartQuantities);
        $cartProductIds = $cartQuantities->keys()->toArray();

        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                $q->orWhereJsonContains('rules->product_id', (int) $id)
                    ->orWhereJsonContains('rules->product_id', (string) $id);
            }
        })->pluck('promotion_id')->unique();

        $appliedCode = $this->getAppliedPromoCode();
        
        // 1. ดึงโปรโมชั่นทั่วไปที่มีเงื่อนไขสินค้า
        $validPromotionIds = Promotion::whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
            ->whereDoesntHave('birthdayPromotion') // ❌ ไม่รวมโปรวันเกิดที่นี่
            ->where(function ($q) use ($appliedCode) {
                $q->where('is_discount_code', false);
                if (!empty($appliedCode)) {
                    $q->orWhere(function($sub) use ($appliedCode) {
                        $sub->where('is_discount_code', true)
                            ->where('code', $appliedCode);
                    });
                }
            })
            ->pluck('id');

        // 2. ดึงโปรโมชั่นทั่วไปที่ไม่มีเงื่อนไขสินค้า (เช่น ลดทั้งตะกร้า)
        $noRulePromoIds = Promotion::where('is_active', true)
            ->whereDoesntHave('rules')
            ->whereDoesntHave('birthdayPromotion') // ❌ ไม่รวมโปรวันเกิดที่นี่
            ->where(function ($q) use ($appliedCode) {
                $q->where('is_discount_code', false);
                if (!empty($appliedCode)) {
                    $q->orWhere(function($sub) use ($appliedCode) {
                        $sub->where('is_discount_code', true)
                            ->where('code', $appliedCode);
                    });
                }
            })
            ->pluck('id');

        // 3. ดึงโปรโมชั่นวันเกิดเฉพาะกรณีที่มีการใช้โค้ดวันเกิด
        $birthdayPromoIds = collect();
        if (!empty($appliedCode)) {
            $isBirthdayCode = \App\Models\BirthdayPromotion::where('discount_code', $appliedCode)->exists();
            if ($isBirthdayCode) {
                $birthdayPromoIds = Promotion::where('is_active', true)
                    ->whereHas('birthdayPromotion', function($q) use ($appliedCode) {
                        $q->where('discount_code', $appliedCode);
                    })
                    ->pluck('id');
            }
        }

        $allPromoIds = $validPromotionIds->merge($noRulePromoIds)->merge($birthdayPromoIds)->unique();

        return Promotion::with(['rules', 'actions.giftableProducts.stock', 'actions.productToGet.stock'])
            ->whereIn('id', $allPromoIds)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get()
            ->filter(function ($promo) use ($cartQuantities, $subTotal) {
                // 1. Check Min Order Value
                if ($promo->min_order_value > 0 && $subTotal < (float) $promo->min_order_value) {
                    return false;
                }

                // 2. 📦 New: Check if at least one gift is in stock (if it's a freebie promo)
                $hasGifts = $promo->actions->contains(fn($a) => isset($a->actions['product_id_to_get']) || $a->giftableProducts->isNotEmpty());
                if ($hasGifts) {
                    $anyGiftInStock = $promo->actions->contains(function($action) {
                        if ($action->productToGet && ($action->productToGet->pd_sp_stock ?? 0) > 0) return true;
                        return $action->giftableProducts->contains(fn($g) => ($g->pd_sp_stock ?? 0) > 0);
                    });
                    if (!$anyGiftInStock) return false;
                }

                if ($promo->rules->isEmpty()) {
                    return true;
                }

                $promoMultipliers = [];
                foreach ($promo->rules as $rule) {
                    $pids = (array) ($rule->rules['product_id'] ?? []);
                    $reqQty = (int) ($rule->rules['quantity_to_buy'] ?? 1);

                    $totalMatched = 0;
                    foreach ($pids as $pid) {
                        $totalMatched += $cartQuantities->get((int) $pid, 0);
                    }

                    $promoMultipliers[] = $reqQty > 0 ? floor($totalMatched / $reqQty) : 0;
                }

                $finalMultiplier = ($promo->condition_type === 'all')
                    ? (empty($promoMultipliers) ? 0 : min($promoMultipliers))
                    : array_sum($promoMultipliers);

                if ($finalMultiplier > 0) {
                    $promo->multiplier = $finalMultiplier;
                    return true;
                }

                return false;
            });
    }

    /**
     * Check if free shipping is applicable to the current cart based on applied promotions.
     */
    public function isFreeShippingApplicable(Collection $cartItems): bool
    {
        $promos = $this->getApplicablePromotions($cartItems);
        $appliedCode = $this->getAppliedPromoCode();

        foreach ($promos as $promo) {
            $isAuto = !$promo->is_discount_code;
            $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

            if ($promo->is_free_shipping && ($isAuto || $isMatchingCode)) {
                return true;
            }
        }

        return false;
    }

    private function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_' . session()->getId();
    }
}
