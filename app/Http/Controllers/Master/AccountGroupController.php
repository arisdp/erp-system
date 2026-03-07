<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AccountGroup;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountGroupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $groups = AccountGroup::with('accountType')->select(['id', 'account_type_id', 'code', 'name']);

            return DataTables::of($groups)
                ->addColumn('type_name', function($row){ return $row->accountType->name ?? 'N/A'; })
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

        $types = AccountType::all();
        return view('master.account_groups.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'code' => 'required',
            'name' => 'required',
        ]);

        AccountGroup::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Group created successfully']);
    }

    public function edit(AccountGroup $accountGroup)
    {
        return response()->json($accountGroup);
    }

    public function update(Request $request, AccountGroup $accountGroup)
    {
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'code' => 'required',
            'name' => 'required',
        ]);

        $accountGroup->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Group updated successfully']);
    }

    public function destroy(AccountGroup $accountGroup)
    {
        $accountGroup->delete();
        return response()->json(['status' => 'success', 'message' => 'Group deleted successfully']);
    }
}
