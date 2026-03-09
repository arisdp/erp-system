<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $assets = Asset::with('category')->select(['assets.*']);
            return DataTables::of($assets)
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->editColumn('purchase_price', function ($row) {
                    return number_format($row->purchase_price, 2);
                })
                ->editColumn('current_value', function ($row) {
                    return number_format($row->current_value, 2);
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '"><i class="fas fa-eye"></i></button>';
                })
                ->make(true);
        }
        $categories = AssetCategory::all();
        return view('finance.assets.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['code'] = 'ASSET-' . time(); // Basic code generator
        $data['current_value'] = $request->purchase_price;
        $data['company_id'] = auth()->user()->company_id;

        Asset::create($data);

        return response()->json(['message' => 'Asset registered successfully']);
    }

    public function runDepreciation()
    {
        $companyId = auth()->user()->company_id;
        \App\Jobs\RunDepreciationJob::dispatch($companyId);

        return response()->json(['message' => 'Depreciation process has been queued in the background.']);
    }
}
