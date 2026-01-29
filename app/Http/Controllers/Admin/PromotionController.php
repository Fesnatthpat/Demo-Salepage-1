<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\LogsActivity;
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
        return view('admin.promotions.index', compact('promotions'));
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

            foreach ($request->get_items as $item) {
                PromotionAction::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'actions' => ['product_id_to_get' => $item['product_id'] ?? null, 'quantity_to_get' => $item['quantity']],
                ]);
            }
            return $promotion;
        });

        return redirect()->route('admin.promotions.index')->with('success', 'สร้างโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $promotion = Promotion::with(['rules', 'actions.giftableProducts'])->findOrFail($id);
        $products = ProductSalepage::orderBy('pd_sp_name')->get();

        return view('admin.promotions.edit', compact('promotion', 'products'));
    }

    public function update(Request $request, $id)
    {
        $this->validatePromotion($request);

        DB::transaction(function () use ($request, $id) {
            $promotion = Promotion::findOrFail($id);
            
            $promotion->fill($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type'));
            
            if ($promotion->isDirty()) {
                $newChanges = $promotion->getDirty();
                $originalData = [];
                foreach ($newChanges as $key => $value) {
                    $originalData[$key] = $promotion->getOriginal($key);
                }
                $this->logActivity($promotion, 'updated', $originalData, $newChanges);
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

            foreach ($request->get_items as $item) {
                PromotionAction::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'actions' => ['product_id_to_get' => $item['product_id'] ?? null, 'quantity_to_get' => $item['quantity']],
                ]);
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
