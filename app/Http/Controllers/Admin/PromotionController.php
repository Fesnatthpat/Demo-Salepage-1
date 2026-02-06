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
        $promotions = Promotion::with(['rules', 'actions.giftableProducts'])
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
            $promotion = Promotion::create($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type', 'code', 'discount_type', 'discount_value', 'is_discount_code'));
            $this->logActivity($promotion, 'created');

            // Conditional processing for buy_items and get_items
            if (!$request->input('is_discount_code')) {
                foreach ($request->buy_items as $item) {
                    PromotionRule::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'rules' => ['product_id' => $item['product_id'], 'quantity_to_buy' => $item['quantity']],
                    ]);
                }

                $createdActions = [];
                foreach ($request->get_items as $item) {
                    $createdActions[] = PromotionAction::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'actions' => ['product_id_to_get' => $item['product_id'] ?? null, 'quantity_to_get' => $item['quantity']],
                    ]);
                }

                // Sync giftable products if they exist in the request
                if ($request->has('giftable_product_ids') && ! empty($request->giftable_product_ids)) {
                    $selectableGiftAction = collect($createdActions)->first(function ($action) {
                        return empty($action->actions['product_id_to_get']);
                    });

                    if ($selectableGiftAction) {
                        $selectableGiftAction->giftableProducts()->sync($request->giftable_product_ids);
                    }
                }
            } else {
                // If it's a discount code promotion, ensure no rules/actions are saved
                $promotion->rules()->delete();
                $promotion->actions()->delete();
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
                // Note: This only handles specific gifts, not selectable ones for the row quantity.
                // The selectable gifts are handled separately by the TomSelect input.
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

            // 1. Get original state
            $originalData = $promotion->toArray();

            $promotion->fill($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type', 'code', 'discount_type', 'discount_value', 'is_discount_code'));

            if ($promotion->isDirty()) {
                // 2. Log the full original and new states
                $this->logActivity($promotion, 'updated', $originalData, $promotion->toArray());
            }

            $promotion->save();

            // Always delete existing rules/actions first for simplicity in update
            $promotion->rules()->delete();
            $promotion->actions()->delete();

            // Conditional processing for buy_items and get_items
            if (!$request->input('is_discount_code')) {
                foreach ($request->buy_items as $item) {
                    PromotionRule::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'rules' => ['product_id' => $item['product_id'], 'quantity_to_buy' => $item['quantity']],
                    ]);
                }

                $createdActions = [];
                foreach ($request->get_items as $item) {
                    $createdActions[] = PromotionAction::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'actions' => ['product_id_to_get' => $item['product_id'] ?? null, 'quantity_to_get' => $item['quantity']],
                    ]);
                }

                // Sync giftable products if they exist in the request
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
        // Log activity BEFORE deleting the model within the transaction
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
            'condition_type' => 'required|in:any,all', // condition_type is still relevant for some BxGy
            'is_discount_code' => 'boolean',
            'code' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:promotions,code'.($id ? ','.$id : '')],
            'discount_type' => ['nullable', 'string', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
        ];

        // Conditional validation for 'buy X get Y' specific fields
        if (!$request->input('is_discount_code')) {
            $rules['buy_items'] = 'required|array|min:1';
            $rules['buy_items.*.product_id'] = 'required|exists:product_salepages,pd_sp_id';
            $rules['buy_items.*.quantity'] = 'required|integer|min:1';
            
            // Only require get_items if condition_type is 'all'. If 'any', it might not need get_items on its own
            // Re-evaluating based on original: get_items was always required if condition_type is 'all'
            // Let's make it simpler: if not discount code, then buy/get items are required.
            $rules['get_items'] = 'required|array|min:1';
            $rules['get_items.*.product_id'] = 'nullable|exists:product_salepages,pd_sp_id';
            $rules['get_items.*.quantity'] = 'required|integer|min:1';
            $rules['giftable_product_ids'] = 'nullable|array';
            $rules['giftable_product_ids.*'] = 'exists:product_salepages,pd_sp_id';
        } else {
            // If it's a discount code promotion, these should not be present or validated
            // Ensure they are not in the request before validation if still present
            $request->offsetUnset('buy_items');
            $request->offsetUnset('get_items');
            $request->offsetUnset('giftable_product_ids');

            // If it's a discount code, then 'code', 'discount_type', 'discount_value' are required
            $rules['code'][] = 'required';
            $rules['discount_type'][] = 'required';
            $rules['discount_value'][] = 'required';
        }

        $request->validate($rules);
    }
}
