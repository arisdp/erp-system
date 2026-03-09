<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $attendances = Attendance::with('employee')->select(['attendances.*']);
            return DataTables::of($attendances)
                ->addColumn('employee_name', function($row){ return $row->employee->full_name; })
                ->editColumn('is_overtime', function($row){
                    return $row->is_overtime ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
                })
                ->rawColumns(['is_overtime', 'action'])
                ->make(true);
        }
        $employees = Employee::where('is_active', true)->get();
        return view('hr.attendances.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|in:Present,Absent,Leave,Sick',
            'is_overtime' => 'boolean',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            $request->all() + ['company_id' => auth()->user()->company_id]
        );

        return response()->json(['message' => 'Attendance recorded successfully']);
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $attendance->update($request->all());
        return response()->json(['message' => 'Attendance updated successfully']);
    }
}
