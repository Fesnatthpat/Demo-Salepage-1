<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\LogsActivity;
use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\ProductSalepage;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = ProductSalepage::with([
            'category',
            'stock',
            'images' => function ($q) {
                $q->orderBy('img_sort', 'asc');
            }
        ])->orderBy('pd_sp_id', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pd_sp_name', 'like', $searchTerm)
                    ->orWhere('pd_sp_code', 'like', $searchTerm)
                    ->orWhere('pd_sp_SKU', 'like', $searchTerm);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        if ($request->filled('type')) {
            switch ($request->type) {
                case 'recommended':
                    $query->where('is_recommended', 1);
                    break;
                case 'promotion':
                    $query->where('pd_sp_discount', '>', 0);
                    break;
                case 'general':
                    $query->where('is_recommended', 0)->where('pd_sp_discount', 0);
                    break;
                case 'out_of_stock':
                    $query->where(function ($q) {
                        $q->whereHas('stock', function ($sq) {
                            $sq->where('quantity', '<=', 0);
                        })->orWhereDoesntHave('stock');
                    });
                    break;
            }
        }

        $products = $query->paginate(10);
        $categories = \App\Models\Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();
        $categories = \App\Models\Category::orderBy('sort_order')->get();

        return view('admin.products.create', [
            'productSalepage' => new ProductSalepage,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $this->validateSalePage($request);

        try {
            return DB::transaction(function () use ($request) {
                // 1. สร้างสินค้าหลัก
                $lastProduct = ProductSalepage::latest('pd_sp_id')->first();
                $nextId = $lastProduct ? ($lastProduct->pd_sp_id + 1) : 1;
                $generatedCode = 'P-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

                $salePage = ProductSalepage::create([
                    'pd_sp_code' => $generatedCode,
                    'category_id' => $request->category_id,
                    'pd_sp_SKU' => $request->pd_sp_SKU,
                    'pd_sp_name' => $request->pd_sp_name,
                    'pd_sp_description' => $request->pd_sp_description,
                    'pd_sp_price' => $request->pd_sp_price,
                    'pd_sp_price2' => $request->pd_sp_price2,
                    'pd_sp_discount' => $request->pd_sp_discount ?? 0,
                    'pd_sp_active' => $request->boolean('pd_sp_active'),
                    'is_recommended' => $request->boolean('is_recommended'),
                    'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
                    'pd_sp_weight' => $request->pd_sp_weight,
                    'pd_sp_width' => $request->pd_sp_width,
                    'pd_sp_length' => $request->pd_sp_length,
                    'pd_sp_height' => $request->pd_sp_height,
                    'pd_sp_free_shipping' => $request->boolean('pd_sp_free_shipping'),
                    'pd_sp_free_cod' => $request->boolean('pd_sp_free_cod'),
                ]);

                // 2. บันทึกรูปภาพแกลเลอรีหลัก
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $file) {
                        $path = $file->store('product_images', 'public');
                        $salePage->images()->create([
                            'img_path' => $path,
                            'img_sort' => $index,
                        ]);
                    }
                }

                // 3. จัดการตัวเลือกสินค้า (Options) และ สต็อก
                $hasOptions = false;
                if ($request->has('product_options')) {
                    foreach ($request->product_options as $index => $optionData) {
                        if (! empty($optionData['option_name'])) {
                            $hasOptions = true;

                            $optionImgId = null;
                            if ($request->hasFile("product_options.{$index}.image")) {
                                $path = $request->file("product_options.{$index}.image")->store('product_images', 'public');
                                $newImage = ProductImage::create([
                                    'pd_sp_id' => $salePage->pd_sp_id,
                                    'img_path' => $path,
                                    'img_sort' => 99,
                                ]);
                                $optionImgId = $newImage->img_id;
                            }

                            $newOption = $salePage->options()->create([
                                'option_name' => $optionData['option_name'],
                                'option_SKU' => $optionData['option_SKU'] ?? null,
                                'option_price' => $optionData['option_price'] ?? $salePage->pd_sp_price,
                                'option_price2' => $optionData['option_price2'] ?? null,
                                'options_img_id' => $optionImgId,
                                'option_active' => 1,
                            ]);

                            StockProduct::create([
                                'pd_sp_id' => $salePage->pd_sp_id,
                                'option_id' => $newOption->option_id,
                                'quantity' => $optionData['option_stock'] ?? 0,
                            ]);
                        }
                    }
                }

                // บันทึกสต็อกหลัก (ถ้ามีตัวเลือก ให้สต็อกหลักเป็น 0 เพื่อป้องกันการตัดสต็อกผิดจุด)
                StockProduct::updateOrCreate(
                    ['pd_sp_id' => $salePage->pd_sp_id, 'option_id' => null],
                    ['quantity' => $hasOptions ? 0 : ($request->pd_sp_stock ?? 0)]
                );

                $this->logActivity($salePage, 'created');

                return redirect()->route('admin.products.index')
                    ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว');
            });
        } catch (\Exception $e) {
            \Log::error('Product creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $productSalepage = ProductSalepage::with(['images', 'options.stock', 'stock'])->where('pd_sp_id', $id)->firstOrFail();
        $products = ProductSalepage::where('pd_sp_id', '!=', $id)->orderBy('pd_sp_name')->get();
        $categories = \App\Models\Category::orderBy('sort_order')->get();

        return view('admin.products.edit', [
            'productSalepage' => $productSalepage,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->with('options')->firstOrFail();
        $this->validateSalePage($request, $productSalepage);

        try {
            return DB::transaction(function () use ($request, $productSalepage) {
                $originalData = $productSalepage->toArray();

                $productSalepage->update([
                    'category_id' => $request->category_id,
                    'pd_sp_name' => $request->pd_sp_name,
                    'pd_sp_SKU' => $request->pd_sp_SKU,
                    'pd_sp_price' => $request->pd_sp_price,
                    'pd_sp_price2' => $request->pd_sp_price2,
                    'pd_sp_discount' => $request->pd_sp_discount ?? 0,
                    'pd_sp_description' => $request->pd_sp_description,
                    'pd_sp_active' => $request->boolean('pd_sp_active'),
                    'is_recommended' => $request->boolean('is_recommended'),
                    'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
                    'pd_sp_weight' => $request->pd_sp_weight,
                    'pd_sp_width' => $request->pd_sp_width,
                    'pd_sp_length' => $request->pd_sp_length,
                    'pd_sp_height' => $request->pd_sp_height,
                    'pd_sp_free_shipping' => $request->boolean('pd_sp_free_shipping'),
                    'pd_sp_free_cod' => $request->boolean('pd_sp_free_cod'),
                ]);

                if ($request->hasFile('images')) {
                    $maxSort = $productSalepage->images()->max('img_sort') ?? -1;

                    foreach ($request->file('images') as $index => $file) {
                        $path = $file->store('product_images', 'public');
                        $productSalepage->images()->create([
                            'img_path' => $path,
                            'img_sort' => $maxSort + 1 + $index,
                        ]);
                    }
                }

                // จัดการตัวเลือกสินค้า (Options)
                $keepOptionIds = [];
                $hasOptions = false;
                
                if ($request->has('product_options')) {
                    foreach ($request->product_options as $index => $optionData) {
                        if (! empty($optionData['option_name'])) {
                            $hasOptions = true;
                            $existingOption = isset($optionData['option_id']) 
                                ? $productSalepage->options()->find($optionData['option_id']) 
                                : null;

                            // รักษา ID รูปภาพเดิมไว้หากไม่มีการอัปโหลดใหม่
                            $optionImgId = $existingOption ? $existingOption->options_img_id : null;

                            if ($request->hasFile("product_options.{$index}.image")) {
                                $path = $request->file("product_options.{$index}.image")->store('product_images', 'public');
                                $newImage = ProductImage::create([
                                    'pd_sp_id' => $productSalepage->pd_sp_id,
                                    'img_path' => $path,
                                    'img_sort' => 99,
                                ]);
                                $optionImgId = $newImage->img_id;
                            }

                            $option = $productSalepage->options()->updateOrCreate(
                                ['option_id' => $optionData['option_id'] ?? null],
                                [
                                    'option_name' => $optionData['option_name'],
                                    'option_SKU' => $optionData['option_SKU'] ?? null,
                                    'option_price' => $optionData['option_price'] ?? $productSalepage->pd_sp_price,
                                    'option_price2' => $optionData['option_price2'] ?? null,
                                    'options_img_id' => $optionImgId,
                                    'option_active' => 1,
                                ]
                            );

                            $keepOptionIds[] = $option->option_id;

                            StockProduct::updateOrCreate(
                                ['pd_sp_id' => $productSalepage->pd_sp_id, 'option_id' => $option->option_id],
                                ['quantity' => $optionData['option_stock'] ?? 0]
                            );
                        }
                    }
                }

                // ลบตัวเลือกและสต็อกที่ไม่ได้อยู่ในรายการที่ส่งมา
                $productSalepage->options()->whereNotIn('option_id', $keepOptionIds)->delete();
                StockProduct::where('pd_sp_id', $productSalepage->pd_sp_id)
                    ->whereNotNull('option_id')
                    ->whereNotIn('option_id', $keepOptionIds)
                    ->delete();

                // บันทึกสต็อกหลัก
                StockProduct::updateOrCreate(
                    ['pd_sp_id' => $productSalepage->pd_sp_id, 'option_id' => null],
                    ['quantity' => $hasOptions ? 0 : ($request->pd_sp_stock ?? 0)]
                );

                $this->logActivity($productSalepage, 'updated', $originalData, $productSalepage->toArray());

                return redirect()->route('admin.products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
            });
        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $productSalepage = ProductSalepage::with(['images', 'options', 'reviewImages'])->where('pd_sp_id', $id)->firstOrFail();
        
        try {
            return DB::transaction(function () use ($productSalepage) {
                $this->logActivity($productSalepage, 'deleted');

                // 1. ลบรูปภาพสินค้าหลัก
                foreach ($productSalepage->images as $img) {
                    Storage::disk('public')->delete($img->img_path);
                }
                $productSalepage->images()->delete();

                // 2. ลบรูปภาพรีวิว
                foreach ($productSalepage->reviewImages as $reviewImg) {
                    Storage::disk('public')->delete($reviewImg->image_url);
                }
                $productSalepage->reviewImages()->delete();

                // 3. ลบ Options และสต็อกที่เกี่ยวข้อง
                $productSalepage->options()->delete();
                StockProduct::where('pd_sp_id', $productSalepage->pd_sp_id)->delete();

                // 4. ลบสินค้าหลัก
                $productSalepage->delete();

                return redirect()->route('admin.products.index')->with('success', 'ลบสินค้าและข้อมูลที่เกี่ยวข้องเรียบร้อยแล้ว');
            });
        } catch (\Exception $e) {
            \Log::error('Product deletion failed: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบสินค้า');
        }
    }

    public function destroyImage($imageId)
    {
        $image = DB::table('product_images')->where('img_id', $imageId)->first();
        if ($image) {
            Storage::disk('public')->delete($image->img_path);
            DB::table('product_images')->where('img_id', $imageId)->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function setMainImage($imageId)
    {
        return DB::transaction(function () use ($imageId) {
            $image = \App\Models\ProductImage::find($imageId);

            if (! $image) {
                return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
            }

            \App\Models\ProductImage::where('pd_sp_id', $image->pd_sp_id)
                ->update(['img_sort' => 99]);

            $image->img_sort = 0;
            $image->save();

            \App\Models\ProductImage::where('pd_sp_id', $image->pd_sp_id)
                ->where('img_id', '!=', $imageId)
                ->orderBy('img_id')
                ->each(function ($img, $index) {
                    $img->update(['img_sort' => $index + 1]);
                });

            return response()->json(['success' => true]);
        });
    }

    public function toggleRecommended(ProductSalepage $product)
    {
        $product->is_recommended = ! $product->is_recommended;
        $product->save();

        return response()->json([
            'success' => true,
            'is_recommended' => $product->is_recommended,
        ]);
    }

    /**
     * ✅ ฟังก์ชันสำหรับกดปุ่มสลับสถานะสินค้า (ใช้งาน/ไม่ใช้งาน) จากหน้าสารบัญ
     */
    public function toggleStatus(ProductSalepage $product)
    {
        // สลับค่า (ถ้า 1 ให้เป็น 0, ถ้า 0 ให้เป็น 1)
        $product->pd_sp_active = ! $product->pd_sp_active;
        $product->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $product->pd_sp_active,
            'message' => 'อัปเดตสถานะการใช้งานสำเร็จ',
        ]);
    }

    private function validateSalePage(Request $request, ?ProductSalepage $salePage = null): array
    {
        return $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'pd_sp_SKU' => 'nullable|string|max:255',
            'pd_sp_price' => 'required|numeric|min:0',
            'pd_sp_price2' => 'nullable|numeric|min:0',
            'pd_sp_stock' => 'required|integer|min:0',
            'pd_sp_display_location' => 'nullable|string',
            'pd_sp_weight' => 'nullable|numeric|min:0',
            'pd_sp_width' => 'nullable|numeric|min:0',
            'pd_sp_length' => 'nullable|numeric|min:0',
            'pd_sp_height' => 'nullable|numeric|min:0',
            'pd_sp_active' => 'nullable|boolean',
            'is_recommended' => 'nullable|boolean',
            'pd_sp_free_shipping' => 'nullable|boolean',
            'pd_sp_free_cod' => 'nullable|boolean',
            'product_options' => 'nullable|array',
            'product_options.*.option_name' => 'nullable|string|max:255',
            'product_options.*.option_SKU' => 'nullable|string|max:255',
            'product_options.*.option_price' => 'nullable|numeric|min:0',
            'product_options.*.option_price2' => 'nullable|numeric|min:0',
            'product_options.*.option_stock' => 'nullable|integer|min:0',
            'product_options.*.options_img_id' => 'nullable|integer',
            'product_options.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // ✅ ตรวจสอบไฟล์รูปภาพตัวเลือก
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120', // ✅ ตรวจสอบไฟล์รูปภาพหลัก
        ]);
    }

    public function showReviewImages(ProductSalepage $product)
    {
        $product->load('reviewImages');

        return view('admin.products.review-images', compact('product'));
    }

    public function storeReviewImage(Request $request, ProductSalepage $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($request->hasFile('images')) {
            $maxSortOrder = $product->reviewImages()->max('sort_order') ?? -1;

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('review_images', 'public');
                $product->reviewImages()->create([
                    'image_url' => $path,
                    'sort_order' => $maxSortOrder + 1 + $index,
                ]);
            }
        }

        return back()->with('success', 'Review images uploaded successfully.');
    }

    public function destroyReviewImage($reviewImageId)
    {
        $reviewImage = \App\Models\ProductReviewImage::findOrFail($reviewImageId);

        Storage::disk('public')->delete($reviewImage->image_url);

        $reviewImage->delete();

        return back()->with('success', 'Review image deleted successfully.');
    }
}
