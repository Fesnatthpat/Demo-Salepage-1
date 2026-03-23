<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BirthdayPromotion;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionAction;
use App\Http\Controllers\Admin\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BirthdayPromotionController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $birthdayPromotions = BirthdayPromotion::with('promotion')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.birthday-promotion.index', compact('birthdayPromotions'));
    }

    public function create()
    {
        $promotions = Promotion::where('is_active', true)->get();
        $products = ProductSalepage::where('pd_sp_active', 1)->get();

        return view('admin.birthday-promotion.create', compact('promotions', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'message' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_url' => 'nullable|url',
            'discount_code' => 'nullable|string|max:20',
            'gift_product_id' => 'nullable|exists:product_salepage,pd_sp_id',
            'discount_value' => 'nullable|numeric|min:0',
            'promotion_id' => 'nullable|exists:promotions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();
        try {
            $originalData = isset($birthdayPromotion) ? $birthdayPromotion->toArray() : null;
            $data = $request->only(['title', 'message', 'link_url', 'discount_code', 'gift_product_id', 'discount_value', 'promotion_id', 'start_date', 'end_date']);

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('birthday_promotions', 'public');
            }

            if ($request->hasFile('card_image')) {
                $data['card_image_path'] = $request->file('card_image')->store('birthday_cards', 'public');
            }

            // จัดการสร้าง/ผูกโปรโมชั่นอัตโนมัติ
            if ($request->discount_code || $request->gift_product_id) {
                $promotion = Promotion::create([
                    'name' => 'Birthday Promo: ' . $request->title,
                    'code' => $request->discount_code,
                    'is_discount_code' => true,
                    'discount_type' => 'fixed',
                    'discount_value' => $request->discount_value ?? 0,
                    'is_active' => true,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'description' => 'โปรโมชั่นวันเกิดสำหรับ ' . $request->title,
                ]);

                if ($request->gift_product_id) {
                    PromotionAction::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'actions' => [
                            'product_id_to_get' => $request->gift_product_id,
                            'quantity_to_get' => 1
                        ],
                    ]);
                }

                $data['promotion_id'] = $promotion->id;
            }

            $birthdayPromotion = BirthdayPromotion::create($data);
            $this->logActivity($birthdayPromotion, 'created');
            DB::commit();

            return redirect()->route('admin.birthday-promotion.index')->with('success', 'สร้างโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);
        $promotions = Promotion::where('is_active', true)->get();
        $products = ProductSalepage::where('pd_sp_active', 1)->get();

        return view('admin.birthday-promotion.edit', compact('birthdayPromotion', 'promotions', 'products'));
    }

    public function update(Request $request, $id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:40',
            'message' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_url' => 'nullable|url',
            'discount_code' => 'nullable|string|max:20',
            'gift_product_id' => 'nullable|exists:product_salepage,pd_sp_id',
            'discount_value' => 'nullable|numeric|min:0',
            'promotion_id' => 'nullable|exists:promotions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();
        try {
            $originalData = isset($birthdayPromotion) ? $birthdayPromotion->toArray() : null;
            $data = $request->only(['title', 'message', 'link_url', 'discount_code', 'gift_product_id', 'discount_value', 'promotion_id', 'start_date', 'end_date']);

            if ($request->hasFile('image')) {
                if ($birthdayPromotion->image_path) {
                    Storage::disk('public')->delete($birthdayPromotion->image_path);
                }
                $data['image_path'] = $request->file('image')->store('birthday_promotions', 'public');
            }

            if ($request->hasFile('card_image')) {
                if ($birthdayPromotion->card_image_path) {
                    Storage::disk('public')->delete($birthdayPromotion->card_image_path);
                }
                $data['card_image_path'] = $request->file('card_image')->store('birthday_cards', 'public');
            }

            // อัปเดตโปรโมชั่นที่ผูกไว้
            if ($birthdayPromotion->promotion_id) {
                $promotion = Promotion::find($birthdayPromotion->promotion_id);
                if ($promotion) {
                    $promotion->update([
                        'code' => $request->discount_code,
                        'discount_value' => $request->discount_value ?? 0,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ]);

                    // อัปเดตของแถม
                    if ($request->gift_product_id) {
                        PromotionAction::updateOrCreate(
                            ['promotion_id' => $promotion->id, 'type' => 'buy_x_get_y'],
                            ['actions' => [
                                'product_id_to_get' => $request->gift_product_id,
                                'quantity_to_get' => 1
                            ]]
                        );
                    } else {
                        PromotionAction::where('promotion_id', $promotion->id)->where('type', 'buy_x_get_y')->delete();
                    }
                }
            } elseif ($request->discount_code || $request->gift_product_id) {
                // สร้างโปรโมชั่นใหม่ถ้ายังไม่มีแต่มีการกรอกข้อมูลมา
                $promotion = Promotion::create([
                    'name' => 'Birthday Promo: ' . $request->title,
                    'code' => $request->discount_code,
                    'is_discount_code' => true,
                    'discount_type' => 'fixed',
                    'discount_value' => $request->discount_value ?? 0,
                    'is_active' => true,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'description' => 'โปรโมชั่นวันเกิดสำหรับ ' . $request->title,
                ]);

                if ($request->gift_product_id) {
                    PromotionAction::create([
                        'promotion_id' => $promotion->id,
                        'type' => 'buy_x_get_y',
                        'actions' => [
                            'product_id_to_get' => $request->gift_product_id,
                            'quantity_to_get' => 1
                        ],
                    ]);
                }

                $data['promotion_id'] = $promotion->id;
            }

            $birthdayPromotion->update($data);
            $this->logActivity($birthdayPromotion, 'updated', $originalData, $birthdayPromotion->toArray());
            DB::commit();

            return redirect()->route('admin.birthday-promotion.index')->with('success', 'อัปเดตโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);

        $this->logActivity($birthdayPromotion, 'deleted');
        if ($birthdayPromotion->image_path) {
            Storage::disk('public')->delete($birthdayPromotion->image_path);
        }
        
        if ($birthdayPromotion->card_image_path) {
            Storage::disk('public')->delete($birthdayPromotion->card_image_path);
        }

        // ลบโปรโมชั่นที่ผูกไว้ (เลือกได้ว่าจะลบหรือไม่ แต่ปกติควรลบถ้าสร้างมาเฉพาะ)
        if ($birthdayPromotion->promotion_id) {
            Promotion::where('id', $birthdayPromotion->promotion_id)->delete();
        }

        $birthdayPromotion->delete();

        return redirect()->route('admin.birthday-promotion.index')->with('success', 'ลบโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
    }

    public function toggleStatus(BirthdayPromotion $birthdayPromotion)
    {
        $originalData = $birthdayPromotion->toArray();
        // สลับสถานะแค่ตัวที่กดเท่านั้น (อนุญาตให้เปิดพร้อมกันได้)
        $birthdayPromotion->is_active = ! $birthdayPromotion->is_active;
        $birthdayPromotion->save();

        $this->logActivity($birthdayPromotion, 'updated', $originalData, $birthdayPromotion->toArray());

        return response()->json([
            'success' => true,
            'is_active' => $birthdayPromotion->is_active,
            'message' => 'อัปเดตสถานะสำเร็จ',
        ]);
    }
}
