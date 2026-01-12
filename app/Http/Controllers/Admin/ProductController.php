<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->orderBy('pd_sp_id', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pd_sp_name', 'like', $searchTerm)
                  ->orWhere('pd_code', 'like', $searchTerm);
            });
        }

        // Status Filter
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        $salePages = $query->paginate(10);

        return view('admin.salepages.index', compact('salePages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.salepages.form', [
            'salePage' => new ProductSalepage(),
            'products' => Product::orderBy('pd_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateSalePage($request);
        ProductSalepage::create($validatedData);
        return redirect()->route('admin.salepages.index')->with('success', 'บันทึกราคา SalePage ใหม่เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSalepage $salepage)
    {
        return redirect()->route('admin.salepages.edit', $salepage);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductSalepage $salepage)
    {
        return view('admin.salepages.form', [
            'salePage' => $salepage,
            'products' => Product::orderBy('pd_name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductSalepage $salepage)
    {
        $validatedData = $this->validateSalePage($request, $salepage);
        $salepage->update($validatedData);
        return redirect()->route('admin.salepages.index')->with('success', 'อัปเดตราคา SalePage เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSalepage $salepage)
    {
        $salepage->delete();
        return back()->with('success', 'ลบราคา SalePage เรียบร้อยแล้ว');
    }

    private function validateSalePage(Request $request, ProductSalepage $salePage = null): array
    {
        $rules = [
            'pd_code' => [
                'required',
                'string',
                ($salePage)
                    ? Rule::unique('product_salepage')->ignore($salePage->pd_id, 'pd_id')
                    : Rule::unique('product_salepage')
            ],
            'pd_sp_price' => 'required|numeric|min:0',
            'pd_sp_discount' => 'nullable|numeric|min:0|lte:pd_sp_price',
            'pd_sp_details' => 'nullable|string',
            'pd_sp_active' => 'required|boolean',
        ];

        return $request->validate($rules);
    }
}