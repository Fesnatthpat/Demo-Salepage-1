<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\LogsActivity;
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->orderBy('pd_sp_id', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pd_sp_name', 'like', $searchTerm)
                    ->orWhere('pd_sp_code', 'like', $searchTerm);
            });
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        $products = $query->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();
        return view('admin.products.create', [
            'productSalepage' => new ProductSalepage,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $this->validateSalePage($request);

        $lastProduct = ProductSalepage::latest('pd_sp_id')->first();
        $nextId = $lastProduct ? ($lastProduct->pd_sp_id + 1) : 1;
        $generatedCode = 'P-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $dataToSave = [
            'pd_sp_code' => $generatedCode,
            'pd_sp_name' => $request->pd_sp_name,
            'pd_sp_description' => $request->pd_sp_details,
            'pd_sp_price' => $request->pd_sp_price,
            'pd_sp_discount' => $request->pd_sp_discount ?? 0,
            'pd_sp_stock' => $request->pd_sp_stock,
            'pd_sp_active' => $request->boolean('pd_sp_active'),
            'is_recommended' => $request->boolean('is_recommended'),
            'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
        ];

        $salePage = ProductSalepage::create($dataToSave);
        $this->logActivity($salePage, 'created');

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');
                $salePage->images()->create([ 'img_path' => $path ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว (รหัส '.$generatedCode.')');
    }

    public function edit($id)
    {
        $productSalepage = ProductSalepage::with(['images', 'options'])->where('pd_sp_id', $id)->firstOrFail();
        $products = ProductSalepage::where('pd_sp_id', '!=', $id)->orderBy('pd_sp_name')->get();
        
        return view('admin.products.edit', [
            'productSalepage' => $productSalepage,
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        $this->validateSalePage($request, $productSalepage);

        // 1. Get original state
        $originalData = $productSalepage->toArray();

        $updateData = [
            'pd_sp_name' => $request->pd_sp_name,
            'pd_sp_price' => $request->pd_sp_price,
            'pd_sp_discount' => $request->pd_sp_discount ?? 0,
            'pd_sp_description' => $request->pd_sp_details, // Correctly map details to description
            'pd_sp_stock' => $request->pd_sp_stock,
            'pd_sp_active' => $request->boolean('pd_sp_active'),
            'is_recommended' => $request->boolean('is_recommended'),
            'pd_sp_display_location' => $request->pd_sp_display_location,
        ];
        
        $productSalepage->fill($updateData);
        
        if ($productSalepage->isDirty()) {
            // 2. Log the full original and new states
            $this->logActivity($productSalepage, 'updated', $originalData, $productSalepage->toArray());
        }
        
        $productSalepage->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');
                $productSalepage->images()->create(['img_path' => $path]);
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        
        $this->logActivity($productSalepage, 'deleted');

        foreach ($productSalepage->images as $img) {
            Storage::disk('public')->delete($img->img_path);
        }

        $productSalepage->delete();

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    public function destroyImage($imageId)
    {
        $image = DB::table('product_images')->where('img_id', $imageId)->first();

        if ($image) {
            Storage::disk('public')->delete($image->img_path);
            DB::table('product_images')->where('img_id', $imageId)->delete();
            return response()->json(['success' => true, 'message' => 'ลบรูปภาพสำเร็จ']);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบรูปภาพ'], 404);
    }

    private function validateSalePage(Request $request, ?ProductSalepage $salePage = null): array
    {
        return $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_price' => 'required|numeric|min:0',
            // other validation rules
        ]);
    }
}
