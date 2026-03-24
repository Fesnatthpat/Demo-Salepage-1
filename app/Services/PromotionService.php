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
    /**
     * Get promotions applicable to a specific product.
     */
    public function getPromotionsForProduct(int $productId): Collection
    {
        $now = now();
        $promotionIds = PromotionRule::where(function ($q) use ($productId) {
            $q->where('rules->product_id', (string) $productId)
                ->orWhere('rules->product_id', (int) $productId)
                ->orWhereJsonContains('rules->product_id', (string) $productId)
                ->orWhereJsonContains('rules->product_id', (int) $productId);
        })->pluck('promotion_id')->unique();

        if ($promotionIds->isEmpty()) {
            return collect();
        }

        return Promotion::with(['rules', 'actions.giftableProducts'])
            ->whereIn('id', $promotionIds)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get();
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
        if ($cartItems->isEmpty()) {
            return 0;
        }

        $promos = $this->getApplicablePromotions($cartItems);
        $maxDiscount = 0;
        $appliedCode = $this->getAppliedPromoCode(); 

        foreach ($promos as $promo) {
            $isAutoDiscount = !$promo->is_discount_code;
            $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

            if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                $currentPromoDiscount = 0;
                if ($promo->discount_type === 'fixed') {
                    $currentPromoDiscount = (float) $promo->discount_value;
                } elseif ($promo->discount_type === 'percentage') {
                    $currentPromoDiscount = ($subTotal * ((float) $promo->discount_value / 100));
                }
                
                if ($currentPromoDiscount > $maxDiscount) {
                    $maxDiscount = $currentPromoDiscount;
                }
            }
        }

        return $maxDiscount;
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
        
        $validPromotionIds = Promotion::whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
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

        $noRulePromoIds = Promotion::where('is_active', true)
            ->whereDoesntHave('rules')
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

        $allPromoIds = $validPromotionIds->merge($noRulePromoIds)->unique();

        return Promotion::with(['rules', 'actions.giftableProducts'])
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
                if ($promo->min_order_value > 0 && $subTotal < (float) $promo->min_order_value) {
                    return false;
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

    private function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_' . session()->getId();
    }
}
