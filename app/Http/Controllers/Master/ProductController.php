<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'unit'])->select(['products.*']);

            return DataTables::of($products)
                ->addColumn('category_name', function($row){ return $row->category->name ?? '-'; })
                ->addColumn('unit_name', function($row){ return $row->unit->name ?? '-'; })
                ->editColumn('selling_price', function($row){ return number_format($row->selling_price, 2); })
                ->editColumn('is_active', function($row){
                    return $row->is_active 
                        ? '<span class="badge badge-success">Active</span>' 
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('master.products.index');
    }

    public function create()
    {
        $categories = ProductCategory::all();
        $units = Unit::all();
        $taxRates = TaxRate::where('is_active', true)->get();
        return view('master.products.create', compact('categories', 'units', 'taxRates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        $units = Unit::all();
        $taxRates = TaxRate::where('is_active', true)->get();
        return view('master.products.edit', compact('product', 'categories', 'units', 'taxRates'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully']);
    }
}
