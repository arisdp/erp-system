<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $txs = BankTransaction::with(['bankAccount', 'offsetAccount'])->select(['bank_transactions.*']);
            return DataTables::of($txs)
                ->addColumn('bank_name', function($row){ return $row->bankAccount->name; })
                ->addColumn('offset_name', function($row){ return $row->offsetAccount->name ?? '-'; })
                ->editColumn('amount', function($row){ return number_format($row->amount, 2); })
                ->editColumn('transaction_type', function($row){
                    $class = $row->transaction_type === 'In' ? 'success' : 'danger';
                    return '<span class="badge badge-'.$class.'">'.$row->transaction_type.'</span>';
                })
                ->rawColumns(['transaction_type'])
                ->make(true);
        }
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $coas = ChartOfAccount::all();
        return view('finance.bank_transactions.index', compact('bankAccounts', 'coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'date' => 'required|date',
            'transaction_type' => 'required|in:In,Out',
            'amount' => 'required|numeric|min:0.01',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        $bankAccount = BankAccount::find($request->bank_account_id);
        
        // Update Balance
        if($request->transaction_type === 'In') {
            $bankAccount->increment('current_balance', $request->amount);
        } else {
            $bankAccount->decrement('current_balance', $request->amount);
        }

        BankTransaction::create($request->all() + ['company_id' => auth()->user()->company_id]);

        return response()->json(['message' => 'Transaction recorded successfully']);
    }
}
