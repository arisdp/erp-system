<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = LeaveRequest::with(['employee', 'leaveType'])->select(['leave_requests.*']);
            return DataTables::of($requests)
                ->addColumn('employee_name', function($row){ return $row->employee->full_name; })
                ->addColumn('type_name', function($row){ return $row->leaveType->name; })
                ->editColumn('status', function($row){
                    $class = $row->status === 'Approved' ? 'success' : ($row->status === 'Rejected' ? 'danger' : 'secondary');
                    return '<span class="badge badge-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btns = '<button class="btn btn-sm btn-info view-btn" data-id="'.$row->id.'"><i class="fas fa-eye"></i></button> ';
                    if($row->status === 'Draft') {
                        $btns .= '<button class="btn btn-sm btn-success approve-btn" data-id="'.$row->id.'"><i class="fas fa-check"></i></button>';
                    }
                    return $btns;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $employees = Employee::where('is_active', true)->get();
        $leaveTypes = LeaveType::all();
        return view('hr.leave_requests.index', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        LeaveRequest::create($request->all() + [
            'company_id' => auth()->user()->company_id,
            'total_days' => $totalDays,
            'status' => 'Draft'
        ]);

        return response()->json(['message' => 'Leave Request submitted successfully']);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        if($request->action === 'approve') {
            $leaveRequest->update(['status' => 'Approved']);
            return response()->json(['message' => 'Leave Request approved']);
        }
        return response()->json(['message' => 'No action performed'], 400);
    }
}
