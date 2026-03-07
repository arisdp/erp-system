<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = ProductCategory::with('parent')->select(['id', 'name', 'parent_id']);

            return DataTables::of($categories)
                ->addColumn('parent_name', function($row){ return $row->parent->name ?? '-'; })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->make(true);
        }

        $parents = ProductCategory::whereNull('parent_id')->get();
        return view('master.product_categories.index', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        ProductCategory::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Category created successfully']);
    }

    public function edit(ProductCategory $productCategory)
    {
        return response()->json($productCategory);
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        $productCategory->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Category updated successfully']);
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return response()->json(['status' => 'success', 'message' => 'Category deleted successfully']);
    }
}
