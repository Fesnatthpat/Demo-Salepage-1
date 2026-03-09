<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\LogsActivity;
use App\Http\Controllers\Controller;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionAction;
use App\Models\PromotionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $promotions = Promotion::with(['rules', 'actions.giftableProducts', 'usageLogs'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $products = ProductSalepage::get()->keyBy('pd_sp_id');

        return view('admin.promotions.index', compact('promotions', 'products'));
    }

    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();
        $discountTypes = ['fixed' => 'ลดราคาคงที่ (บาท)', 'percentage' => 'ลดเป็นเปอร์เซ็นต์ (%)'];

        return view('admin.promotions.create', compact('products', 'discountTypes'));
    }

    public function store(Request $request)
    {
        $this->validatePromotion($request);

        $promotion = DB::transaction(function () use ($request) {
            $data = $request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type', 'discount_type', 'discount_value', 'min_order_value', 'usage_limit');
            
            $promoType = $request->input('promo_type_selector');
            $data['is_discount_code'] = ($promoType === 'code');
            $data['code'] = ($promoType === 'code') ? $request->input('code') : null;

            if ($promoType === 'bxgy') {
                $data['discount_type'] = null;
                $data['discount_value'] = null;
            }

            $promotion = Promotion::create($data);
            $this->logActivity($promotion, 'created');

            if ($promoType === 'bxgy') {
                if ($request->has('buy_items')) {
                    foreach ($request->buy_items as $item) {
                        PromotionRule::create([
                            'promotion_id' => $promotion->id,
                            'type' => 'buy_x_get_y',
                            'rules' => ['product_id' => $item['product_id'], 'quantity_to_buy' => $item['quantity']],
                        ]);
                    }
                }

                $createdActions = [];
                if ($request->has('get_items')) {
                    foreach ($request->get_items as $item) {
                        $createdActions[] = PromotionAction::create([
                            'promotion_id' => $promotion->id,
                            'type' => 'buy_x_get_y',
                            'actions' => ['product_id_to_get' => $item['product_id'] ?? null, 'quantity_to_get' => $item['quantity']],
                        ]);
                    }
                }

                if ($request->has('giftable_product_ids') && ! empty($request->giftable_product_ids)) {
                    $selectableGiftAction = collect($createdActions)->first(function ($action) {
                        return empty($action->actions['product_id_to_get']);
                    });
                    if ($selectableGiftAction) {
                        $selectableGiftAction->giftableProducts()->sync($request->giftable_product_ids);
                    }
                }
            }

            return $promotion;
        });

        return redirect()->route('admin.promotions.index')->with('success', 'สร้างโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $promotion = Promotion::with(['rules', 'actions.giftableProducts'])->findOrFail($id);
        $products = ProductSalepage::orderBy('pd_sp_name')->get();
        $discountTypes = ['fixed' => 'ลดราคาคงที่ (บาท)', 'percentage' => 'ลดเป็นเปอร์เซ็นต์ (%)'];

        $buy_items = $promotion->rules->map(function ($rule) {
            return [
                'product_id' => $rule->rules['product_id'] ?? '',
                'quantity' => $rule->rules['quantity_to_buy'] ?? 1,
            ];
        });

        $get_items = $promotion->actions->map(function ($action) {
            return [
                'product_id' => $action->actions['product_id_to_get'] ?? '',
                'quantity' => $action->actions['quantity_to_get'] ?? 1,
            ];
        });

        return view('admin.promotions.edit', compact('promotion', 'products', 'buy_items', 'get_items', 'discountTypes'));
    }

    public function update(Request $request, $id)
    {
        $this->validatePromotion($request, $id);

        DB::transaction(function () use ($request, $id) {
            $promotion = Promotion::findOrFail($id);
            $originalData = $promotion->toArray();

            $data = $request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type', 'discount_type', 'discount_value', 'min_order_value', 'usage_limit');
            
            $promoType = $request->input('promo_type_selector');
            $data['is_discount_code'] = ($promoType === 'code');
            $data['code'] = ($promoType === 'code') ? $request->input('code') : null;

            if ($promoType === 'bxgy') {
                $data['discount_type'] = null;
                $data['discount_value'] = null;
            }

            $promotion->fill($data);
            if ($promotion->isDirty()) {
                $this->logActivity($promotion, 'updated', $originalData, $promotion->toArray());
            }
            $promotion->save();

            // Clear old data
            $promotion->rules()->delete();
            $promotion->actions()->delete();

            if ($promoType === 'bxgy') {
                if ($request->has('buy_items')) {
                    foreach ($request->buy_items as $item) {
                        PromotionRule::create([
                            'promotion_id' => $promotion->id,
                            'type' => 'buy_x_get_y',
                            // รองรับทั้ง product_id เดียว หรือหลายตัว (Array)
                            'rules' => [
                                'product_id' => is_array($item['product_id']) ? $item['product_id'] : [$item['product_id']], 
                                'quantity_to_buy' => $item['quantity']
                            ],
                        ]);
                    }
                }

                $createdActions = [];
                if ($request->has('get_items')) {
                    foreach ($request->get_items as $item) {
                        $createdActions[] = PromotionAction::create([
                            'promotion_id' => $promotion->id,
                            'type' => 'buy_x_get_y',
                            'actions' => [
                                'product_id_to_get' => $item['product_id'] ?? null, 
                                'quantity_to_get' => $item['quantity']
                            ],
                        ]);
                    }
                }

                if ($request->has('giftable_product_ids') && ! empty($request->giftable_product_ids)) {
                    $selectableGiftAction = collect($createdActions)->first(function ($action) {
                        return empty($action->actions['product_id_to_get']);
                    });
                    if ($selectableGiftAction) {
                        $selectableGiftAction->giftableProducts()->sync($request->giftable_product_ids);
                    }
                }
            }
        });

        return redirect()->route('admin.promotions.index')->with('success', 'อัปเดตโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $this->logActivity($promotion, 'deleted');

        DB::transaction(function () use ($promotion) {
            $promotion->rules()->delete();
            $promotion->actions()->delete();
            $promotion->delete();
        });

        return redirect()->back()->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }

    private function validatePromotion(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'condition_type' => 'required|in:any,all',
            'is_discount_code' => 'boolean',
            'code' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:promotions,code'.($id ? ','.$id : '')],
            'discount_type' => ['nullable', 'string', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
        ];

        $promoType = $request->input('promo_type_selector');

        if ($promoType === 'bxgy') {
            $rules['buy_items'] = 'required|array|min:1';
            $rules['buy_items.*.product_id'] = 'required|exists:product_salepage,pd_sp_id';
            $rules['buy_items.*.quantity'] = 'required|integer|min:1';
            
            $rules['get_items'] = 'required|array|min:1';
            $rules['get_items.*.product_id'] = 'nullable|exists:product_salepage,pd_sp_id';
            $rules['get_items.*.quantity'] = 'required|integer|min:1';
            $rules['giftable_product_ids'] = 'nullable|array';
            $rules['giftable_product_ids.*'] = 'exists:product_salepage,pd_sp_id';
        } else {
            $request->offsetUnset('buy_items');
            $request->offsetUnset('get_items');
            $request->offsetUnset('giftable_product_ids');

            if ($promoType === 'code') {
                $rules['code'][] = 'required';
            }
            $rules['discount_type'][] = 'required';
            $rules['discount_value'][] = 'required';
        }

        $request->validate($rules);
    }
}
