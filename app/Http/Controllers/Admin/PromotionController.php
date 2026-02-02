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

        return view('admin.promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->validatePromotion($request);

        $promotion = DB::transaction(function () use ($request) {
            $promotion = Promotion::create($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type'));
            $this->logActivity($promotion, 'created');

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

                // Find the first action that is a 'selectable gift' type

                $selectableGiftAction = collect($createdActions)->first(function ($action) {

                    return empty($action->actions['product_id_to_get']);

                });

                if ($selectableGiftAction) {

                    $selectableGiftAction->giftableProducts()->sync($request->giftable_product_ids);

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

        return view('admin.promotions.edit', compact('promotion', 'products', 'buy_items', 'get_items'));
    }

    public function update(Request $request, $id)
    {
        $this->validatePromotion($request);

        DB::transaction(function () use ($request, $id) {
            $promotion = Promotion::findOrFail($id);

            // 1. Get original state
            $originalData = $promotion->toArray();

            $promotion->fill($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type'));

            if ($promotion->isDirty()) {
                // 2. Log the full original and new states
                $this->logActivity($promotion, 'updated', $originalData, $promotion->toArray());
            }

            $promotion->save();

            $promotion->rules()->delete();
            $promotion->actions()->delete();

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
                // Find the first action that is a 'selectable gift' type
                $selectableGiftAction = collect($createdActions)->first(function ($action) {
                    return empty($action->actions['product_id_to_get']);
                });

                if ($selectableGiftAction) {
                    $selectableGiftAction->giftableProducts()->sync($request->giftable_product_ids);
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

    private function validatePromotion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'condition_type' => 'required|in:any,all',
            'buy_items' => 'required|array|min:1',
            // more validation
        ]);
    }
}
