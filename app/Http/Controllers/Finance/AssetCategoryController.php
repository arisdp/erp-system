<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AssetCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = AssetCategory::select(['asset_categories.*']);
            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
                })
                ->make(true);
        }
        $coas = ChartOfAccount::all();
        return view('finance.asset_categories.index', compact('coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'useful_life_years' => 'required|integer|min:1',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        AssetCategory::create($request->all() + ['company_id' => auth()->user()->company_id]);

        return response()->json(['message' => 'Asset Category created successfully']);
    }

    public function show(AssetCategory $assetCategory)
    {
        return response()->json($assetCategory);
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $assetCategory->update($request->all());
        return response()->json(['message' => 'Asset Category updated successfully']);
    }
}
