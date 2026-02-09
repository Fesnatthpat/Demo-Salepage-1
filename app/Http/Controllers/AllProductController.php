<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        // 1. เริ่ม Query สินค้าที่ Active
        $query = ProductSalepage::with('images')->where('pd_sp_active', 1);

        // 2. ระบบค้นหา (Search)
        if ($request->filled('search')) {
            $query->where('pd_sp_name', 'like', '%'.$request->search.'%');
        }

        // 3. ระบบกรองหมวดหมู่ (Category Filter)
        // หมายเหตุ: ตรวจสอบให้แน่ใจว่าในตาราง database ของคุณมีคอลัมน์ชื่อ 'pd_sp_category'
        // หรือเปลี่ยนเป็นชื่อคอลัมน์ที่คุณใช้เก็บหมวดหมู่จริง
        if ($request->filled('category')) {
            $query->where('pd_sp_category', $request->category);
        }

        // 4. ระบบเรียงลำดับ (Sorting)
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('pd_sp_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('pd_sp_price', 'desc');
                    break;
                case 'bestseller':
                    // ถ้ามีคอลัมน์เก็บยอดขาย เช่น 'pd_sp_sold_count' ให้ใช้บรรทัดล่างแทน
                    // $query->orderBy('pd_sp_sold_count', 'desc');
                    $query->orderBy('pd_sp_id', 'desc'); // Fallback
                    break;
                case 'popular':
                    // ถ้ามีคอลัมน์เก็บยอดวิว เช่น 'pd_sp_views' ให้ใช้บรรทัดล่างแทน
                    // $query->orderBy('pd_sp_views', 'desc');
                    $query->inRandomOrder(); // สุ่มสินค้าให้ดูมีความเคลื่อนไหว
                    break;
                case 'newest':
                default:
                    $query->orderBy('pd_sp_id', 'desc');
                    break;
            }
        } else {
            // Default: เรียงจากสินค้าใหม่สุดไปเก่า
            $query->orderBy('pd_sp_id', 'desc');
        }

        // 5. ดึงข้อมูลและแบ่งหน้า (Pagination)
        $products = $query->paginate(12);

        // 6. เพิ่มข้อมูลโปรโมชั่น/ของแถม (Logic เดิม)
        $products->getCollection()->transform(function ($product) {
            $promotions = $this->cartService->getPromotionsForProduct($product->pd_sp_id);
            if ($promotions->isNotEmpty()) {
                // คำนวณจำนวนของแถม
                $giftsPerItem = $promotions->first()->actions->sum(fn ($a) => (int) ($a->actions['quantity_to_get'] ?? 0));
                $product->gifts_per_item = $giftsPerItem > 0 ? $giftsPerItem : null;
            } else {
                $product->gifts_per_item = null;
            }

            return $product;
        });

        // 7. รายชื่อหมวดหมู่ (อัปเดตให้ตรงกับหน้าเว็บ)
        // หรือถ้าอยากดึงจาก DB โดยตรงให้ใช้: ProductSalepage::distinct()->pluck('pd_sp_category');
        $categories = [
            'กิมจิ',
            'ซอสเกาหลี',
            'Combo Set',
            'น้ำดอง ผักดอง',
            'เครื่องปรุงเกาหลี',
            'แป้ง/ข้าว/เส้น',
            'สาหร่าย',
            'เครื่องครัว',
            'ซอสญี่ปุ่น',
            'เครื่องปรุงญี่ปุ่น',
        ];

        return view('allproducts', compact('products', 'categories'));
    }
}
