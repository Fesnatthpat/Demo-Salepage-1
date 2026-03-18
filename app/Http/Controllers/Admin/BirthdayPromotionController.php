<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BirthdayPromotion;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BirthdayPromotionController extends Controller
{
    public function index()
    {
        $birthdayPromotions = BirthdayPromotion::with('promotion')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.birthday-promotion.index', compact('birthdayPromotions'));
    }

    public function create()
    {
        $promotions = Promotion::where('is_active', true)->get();

        return view('admin.birthday-promotion.create', compact('promotions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'message' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_url' => 'nullable|url',
            'promotion_id' => 'nullable|exists:promotions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->only(['title', 'message', 'link_url', 'promotion_id', 'start_date', 'end_date']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('birthday_promotions', 'public');
        }

        BirthdayPromotion::create($data);

        return redirect()->route('admin.birthday-promotion.index')->with('success', 'สร้างโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);
        $promotions = Promotion::where('is_active', true)->get();

        return view('admin.birthday-promotion.edit', compact('birthdayPromotion', 'promotions'));
    }

    public function update(Request $request, $id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:40',
            'message' => 'required|string|max:200',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_url' => 'nullable|url',
            'promotion_id' => 'nullable|exists:promotions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->only(['title', 'message', 'link_url', 'promotion_id', 'start_date', 'end_date']);

        if ($request->hasFile('image')) {
            if ($birthdayPromotion->image_path) {
                Storage::disk('public')->delete($birthdayPromotion->image_path);
            }
            $data['image_path'] = $request->file('image')->store('birthday_promotions', 'public');
        }

        $birthdayPromotion->update($data);

        return redirect()->route('admin.birthday-promotion.index')->with('success', 'อัปเดตโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $birthdayPromotion = BirthdayPromotion::findOrFail($id);

        if ($birthdayPromotion->image_path) {
            Storage::disk('public')->delete($birthdayPromotion->image_path);
        }

        $birthdayPromotion->delete();

        return redirect()->route('admin.birthday-promotion.index')->with('success', 'ลบโปรโมชั่นวันเกิดเรียบร้อยแล้ว');
    }

    public function toggleStatus(BirthdayPromotion $birthdayPromotion)
    {
        // สลับสถานะแค่ตัวที่กดเท่านั้น (อนุญาตให้เปิดพร้อมกันได้)
        $birthdayPromotion->is_active = ! $birthdayPromotion->is_active;
        $birthdayPromotion->save();

        return response()->json([
            'success' => true,
            'is_active' => $birthdayPromotion->is_active,
            'message' => 'อัปเดตสถานะสำเร็จ',
        ]);
    }
}
