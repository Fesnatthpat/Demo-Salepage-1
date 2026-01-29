<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionAction;
use App\Models\PromotionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->guard('admin')->user()->role !== 'superadmin') {
                return redirect()->route('admin.products.index')->with('info', 'You do not have permission to access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $promotions = Promotion::with(['rules', 'actions.giftableProducts'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $products = ProductSalepage::pluck('pd_sp_name', 'pd_sp_id');

        return view('admin.promotions.index', compact('promotions', 'products'));
    }

    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();
        $buy_items = [['product_id' => '', 'quantity' => 1]];
        $get_items = [['product_id' => '', 'quantity' => 1]];

        return view('admin.promotions.create', compact('products', 'buy_items', 'get_items'));
    }

    public function store(Request $request)
    {
        $this->validatePromotion($request);

        DB::transaction(function () use ($request) {
            // ✅ เพิ่ม condition_type ในการ create
            $promotion = Promotion::create($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type'));

            foreach ($request->buy_items as $item) {
                PromotionRule::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'rules' => [
                        'product_id' => $item['product_id'],
                        'quantity_to_buy' => $item['quantity'],
                    ],
                ]);
            }

            foreach ($request->get_items as $item) {
                $action = PromotionAction::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'actions' => [
                        'product_id_to_get' => $item['product_id'] ?? null,
                        'quantity_to_get' => $item['quantity'],
                    ],
                ]);

                if ($request->has('giftable_product_ids')) {
                    $action->giftableProducts()->attach($request->giftable_product_ids);
                }
            }
        });

        return redirect()->route('admin.promotions.index')->with('success', 'สร้างโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $promotion = Promotion::with(['rules', 'actions.giftableProducts'])->findOrFail($id);
        $products = ProductSalepage::orderBy('pd_sp_name')->get();

        $buy_items = $promotion->rules->map(function ($rule) {
            return [
                'product_id' => $rule->rules['product_id'] ?? '', // Access from JSON rules
                'quantity' => $rule->rules['quantity_to_buy'] ?? 1,
            ];
        })->values()->toArray();

        $get_items = $promotion->actions->map(function ($action) {
            return [
                'product_id' => $action->actions['product_id_to_get'] ?? '',
                'quantity' => $action->actions['quantity_to_get'] ?? 1,
            ];
        })->values()->toArray();

        if (empty($buy_items)) {
            $buy_items = [['product_id' => '', 'quantity' => 1]];
        }
        if (empty($get_items)) {
            $get_items = [['product_id' => '', 'quantity' => 1]];
        }

        return view('admin.promotions.edit', compact('promotion', 'products', 'buy_items', 'get_items'));
    }

    public function update(Request $request, $id)
    {
        $this->validatePromotion($request);

        DB::transaction(function () use ($request, $id) {
            $promotion = Promotion::findOrFail($id);
            // ✅ เพิ่ม condition_type ในการ update
            $promotion->update($request->only('name', 'description', 'start_date', 'end_date', 'is_active', 'condition_type'));

            $promotion->rules()->delete();
            $promotion->actions()->delete();

            foreach ($request->buy_items as $item) {
                PromotionRule::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'rules' => [
                        'product_id' => $item['product_id'],
                        'quantity_to_buy' => $item['quantity'],
                    ],
                ]);
            }

            foreach ($request->get_items as $item) {
                $action = PromotionAction::create([
                    'promotion_id' => $promotion->id,
                    'type' => 'buy_x_get_y',
                    'actions' => [
                        'product_id_to_get' => $item['product_id'] ?? null,
                        'quantity_to_get' => $item['quantity'],
                    ],
                ]);

                if ($request->has('giftable_product_ids')) {
                    $action->giftableProducts()->attach($request->giftable_product_ids);
                }
            }
        });

        return redirect()->route('admin.promotions.index')->with('success', 'อัปเดตโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->rules()->delete();
        $promotion->actions()->delete();
        $promotion->delete();

        return redirect()->back()->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }

    private function validatePromotion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'condition_type' => 'required|in:any,all', // ✅ เพิ่ม Validation
            'buy_items' => 'required|array|min:1',
            'buy_items.*.product_id' => 'required',
            'buy_items.*.quantity' => 'required|integer|min:1',
            'get_items' => 'required|array|min:1',
            'get_items.*.product_id' => 'nullable',
            'get_items.*.quantity' => 'required|integer|min:1',
            'giftable_product_ids' => 'nullable|array',
        ]);
    }
}
