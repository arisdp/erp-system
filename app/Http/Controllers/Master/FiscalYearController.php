<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FiscalYearController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $years = FiscalYear::select(['id', 'year', 'start_date', 'end_date', 'is_closed']);

            return DataTables::of($years)
                ->editColumn('start_date', function($row){ return $row->start_date->format('Y-m-d'); })
                ->editColumn('end_date', function($row){ return $row->end_date->format('Y-m-d'); })
                ->editColumn('is_closed', function($row){
                    return $row->is_closed 
                        ? '<span class="badge badge-danger">Closed</span>' 
                        : '<span class="badge badge-success">Open</span>';
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
                ->rawColumns(['is_closed', 'action'])
                ->make(true);
        }

        return view('master.fiscal_years.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|digits:4',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_closed' => 'boolean',
        ]);

        $fiscalYear = FiscalYear::create($validated);

        return response()->json(['status' => 'success', 'message' => 'Fiscal Year created successfully']);
    }

    public function edit(FiscalYear $fiscalYear)
    {
        return response()->json($fiscalYear);
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $validated = $request->validate([
            'year' => 'required|digits:4',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_closed' => 'boolean',
        ]);

        $fiscalYear->update($validated);

        return response()->json(['status' => 'success', 'message' => 'Fiscal Year updated successfully']);
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $fiscalYear->delete();
        return response()->json(['status' => 'success', 'message' => 'Fiscal Year deleted successfully']);
    }
}
