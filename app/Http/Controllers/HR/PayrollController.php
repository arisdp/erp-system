<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payrolls = Payroll::with('employee')->select(['payrolls.*']);
            return DataTables::of($payrolls)
                ->addColumn('employee_name', function($row){ return $row->employee->full_name; })
                ->editColumn('period', function($row){
                    return date('F', mktime(0, 0, 0, $row->period_month, 10)) . ' ' . $row->period_year;
                })
                ->editColumn('net_salary', function($row){ return number_format($row->net_salary, 2); })
                ->editColumn('status', function($row){
                    $class = $row->status === 'Paid' ? 'success' : 'secondary';
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('payrolls.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View Slip</a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('hr.payrolls.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $this->payrollService->generateMonthlyPayroll(
            auth()->user()->company_id,
            $request->month,
            $request->year
        );

        return response()->json(['message' => 'Monthly payroll generated successfully']);
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'details.component']);
        return view('hr.payrolls.show', compact('payroll'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if($request->action === 'pay') {
            $payroll->update(['status' => 'Paid']);
            return response()->json(['message' => 'Payroll marked as Paid']);
        }
        return response()->json(['message' => 'Invalid action'], 400);
    }
}
