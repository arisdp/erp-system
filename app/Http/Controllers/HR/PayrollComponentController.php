<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PayrollComponentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $components = PayrollComponent::select(['id', 'name', 'type', 'calculation_type', 'default_amount']);
            return DataTables::of($components)
                ->editColumn('type', function ($row) {
                    return $row->type === 'Allowance' ? '<span class="badge badge-success">Allowance</span>' : '<span class="badge badge-danger">Deduction</span>';
                })
                ->editColumn('default_amount', function ($row) {
                    return number_format($row->default_amount, 2);
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                })
                ->rawColumns(['type', 'action'])
                ->make(true);
        }
        return view('hr.payroll_components.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Allowance,Deduction',
            'calculation_type' => 'required|in:Fixed,Daily,Per Hour',
            'default_amount' => 'required|numeric|min:0',
        ]);

        PayrollComponent::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'type' => $request->type,
            'calculation_type' => $request->calculation_type,
            'default_amount' => $request->default_amount,
        ]);

        return response()->json(['message' => 'Payroll Component created successfully']);
    }

    public function show(PayrollComponent $payrollComponent)
    {
        return response()->json($payrollComponent);
    }

    public function update(Request $request, PayrollComponent $payrollComponent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Allowance,Deduction',
            'calculation_type' => 'required|in:Fixed,Daily,Per Hour',
            'default_amount' => 'required|numeric|min:0',
        ]);

        $payrollComponent->update($request->only(['name', 'type', 'calculation_type', 'default_amount']));

        return response()->json(['message' => 'Payroll Component updated successfully']);
    }

    public function destroy(PayrollComponent $payrollComponent)
    {
        $payrollComponent->delete();
        return response()->json(['message' => 'Payroll Component deleted successfully']);
    }
}
