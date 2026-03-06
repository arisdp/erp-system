<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::with('company')->select('departments.*');

            return DataTables::of($departments)
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
        return view('master.departments.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required',
            'name' => 'required',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Department created successfully',
            'data' => $department
        ]);
    }

    public function edit(Department $department)
    {
        return response()->json($department);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required',
            'name' => 'required',
        ]);

        $department->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Department updated successfully'
        ]);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Department deleted successfully'
        ]);
    }
}
