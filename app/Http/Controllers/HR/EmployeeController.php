<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeRequest;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with(['department', 'position', 'branch'])->select(['employees.*']);
            return DataTables::of($employees)
                ->addColumn('department_name', function($row){ return $row->department->name ?? '-'; })
                ->addColumn('position_name', function($row){ return $row->position->name ?? '-'; })
                ->addColumn('branch_name', function($row){ return $row->branch->name ?? '-'; })
                ->editColumn('is_active', function($row){
                    return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('employees.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="'.route('employees.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }
        return view('hr.employees.index');
    }

    public function create()
    {
        $departments = Department::all();
        $positions = JobPosition::all();
        $branches = Branch::all();
        $users = User::all();
        return view('hr.employees.create', compact('departments', 'positions', 'branches', 'users'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        Employee::create($request->validated() + ['company_id' => auth()->user()->company_id]);

        return redirect()->route('employees.index')->with('success', 'Employee registered successfully');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'position', 'branch', 'user']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = JobPosition::all();
        $branches = Branch::all();
        $users = User::all();
        return view('hr.employees.edit', compact('employee', 'departments', 'positions', 'branches', 'users'));
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
    }
}
