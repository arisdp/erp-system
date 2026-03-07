<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $currencies = Currency::query();
            return DataTables::of($currencies)
                ->editColumn('is_base', function($row){
                    return $row->is_base 
                        ? '<span class="badge badge-primary">Base</span>' 
                        : '<span class="badge badge-secondary">Secondary</span>';
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
                ->rawColumns(['is_base', 'action'])
                ->make(true);
        }

        return view('master.currencies.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_base' => 'boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        Currency::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Currency created successfully']);
    }

    public function edit(Currency $currency)
    {
        return response()->json($currency);
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'exchange_rate' => 'required|numeric|min:0',
            'is_base' => 'boolean',
        ]);

        $currency->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Currency updated successfully']);
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return response()->json(['status' => 'success', 'message' => 'Currency deleted successfully']);
    }
}
