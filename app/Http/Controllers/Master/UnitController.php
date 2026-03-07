<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::query();

            return DataTables::of($units)
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

        return view('master.units.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        Unit::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Unit created successfully']);
    }

    public function edit(Unit $unit)
    {
        return response()->json($unit);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
        ]);

        $unit->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Unit updated successfully']);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['status' => 'success', 'message' => 'Unit deleted successfully']);
    }
}
