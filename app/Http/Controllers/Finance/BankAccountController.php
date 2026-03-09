<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accounts = BankAccount::with('chartOfAccount')->select(['bank_accounts.*']);
            return DataTables::of($accounts)
                ->addColumn('account_name', function($row){ return $row->chartOfAccount->name ?? '-'; })
                ->editColumn('current_balance', function($row){ return number_format($row->current_balance, 2); })
                ->editColumn('is_active', function($row){
                    return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }
        $coas = ChartOfAccount::whereHas('accountGroup', function($q){
            $q->where('name', 'LIKE', '%Bank%')->orWhere('name', 'LIKE', '%Kas%')
              ->orWhere('name', 'LIKE', '%Cash%');
        })->get();
        return view('finance.bank_accounts.index', compact('coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'initial_balance' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['current_balance'] = $request->initial_balance;
        $data['company_id'] = auth()->user()->company_id;

        BankAccount::create($data);

        return response()->json(['message' => 'Bank Account created successfully']);
    }

    public function show(BankAccount $bankAccount)
    {
        return response()->json($bankAccount);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bankAccount->update($request->all());

        return response()->json(['message' => 'Bank Account updated successfully']);
    }
}
