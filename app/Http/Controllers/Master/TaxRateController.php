<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $taxRates = TaxRate::query();
            return DataTables::of($taxRates)
                ->editColumn('rate', function($row){ return number_format($row->rate, 2) . '%'; })
                ->editColumn('is_active', function($row){
                    return $row->is_active 
                        ? '<span class="badge badge-success">Active</span>' 
                        : '<span class="badge badge-danger">Inactive</span>';
                })
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
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('master.tax_rates.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        TaxRate::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Tax rate created successfully']);
    }

    public function edit(TaxRate $taxRate)
    {
        return response()->json($taxRate);
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $taxRate->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Tax rate updated successfully']);
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return response()->json(['status' => 'success', 'message' => 'Tax rate deleted successfully']);
    }
}
