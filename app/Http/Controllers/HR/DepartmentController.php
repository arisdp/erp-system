<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::select(['id', 'code', 'name']);
            return DataTables::of($departments)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                })
                ->make(true);
        }
        return view('hr.departments.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:departments,code,NULL,id,company_id,' . auth()->user()->company_id,
            'name' => 'required|string|max:255',
        ]);

        Department::create([
            'company_id' => auth()->user()->company_id,
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Department created successfully']);
    }

    public function show(Department $department)
    {
        return response()->json($department);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id . ',id,company_id,' . auth()->user()->company_id,
            'name' => 'required|string|max:255',
        ]);

        $department->update($request->only(['code', 'name']));

        return response()->json(['message' => 'Department updated successfully']);
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return response()->json(['message' => 'Department deleted successfully']);
    }
}
