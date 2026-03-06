<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $branches = Branch::with('company')->select('branches.*');

            return DataTables::of($branches)
                ->addColumn('company_name', function ($row) {
                    return $row->company->name ?? '-';
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
                ->rawColumns(['action'])
                ->make(true);
        }

        $companies = Company::where('is_active', true)->get();
        return view('master.branches.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required',
            'name' => 'required',
            'address' => 'nullable',
        ]);

        $branch = Branch::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Branch created successfully',
            'data' => $branch
        ]);
    }

    public function edit(Branch $branch)
    {
        return response()->json($branch);
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required',
            'name' => 'required',
            'address' => 'nullable',
        ]);

        $branch->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Branch updated successfully'
        ]);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Branch deleted successfully'
        ]);
    }
}
