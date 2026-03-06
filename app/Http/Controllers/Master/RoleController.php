<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('company')->select('roles.*');

            return DataTables::of($roles)
                ->addColumn('company_name', function ($row) {
                    return $row->company->name ?? 'Global';
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
        return view('master.roles.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'company_id' => 'nullable|exists:companies,id',
            'guard_name' => 'required',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    public function edit(Role $role)
    {
        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required',
            'company_id' => 'nullable|exists:companies,id',
            'guard_name' => 'required',
        ]);

        $role->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }
}
