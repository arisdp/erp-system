<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accounts = ChartOfAccount::with(['type', 'group', 'parent'])->select('chart_of_accounts.*');

            return DataTables::of($accounts)
                ->addColumn('type_name', function ($row) {
                    return $row->type->name ?? 'N/A';
                })
                ->addColumn('group_name', function ($row) {
                    return $row->group->name ?? '-';
                })
                ->addColumn('parent_name', function ($row) {
                    return $row->parent->account_name ?? 'Root';
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

        $types = AccountType::all();
        $groups = AccountGroup::all();
        $parents = ChartOfAccount::where('is_postable', false)->get();
        
        return view('master.accounts.index', compact('types', 'groups', 'parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'account_code' => 'required',
            'account_name' => 'required',
            'is_postable' => 'boolean',
        ]);

        $account = ChartOfAccount::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully',
            'data' => $account
        ]);
    }

    public function edit(ChartOfAccount $account)
    {
        return response()->json($account);
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'account_code' => 'required',
            'account_name' => 'required',
            'is_postable' => 'boolean',
        ]);

        $account->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Account updated successfully'
        ]);
    }

    public function destroy(ChartOfAccount $account)
    {
        $account->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully'
        ]);
    }
}
