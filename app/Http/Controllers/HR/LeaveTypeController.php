<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $types = LeaveType::select(['id', 'name', 'is_paid', 'default_quota']);
            return DataTables::of($types)
                ->editColumn('is_paid', function ($row) {
                    return $row->is_paid ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-danger">Unpaid</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                })
                ->rawColumns(['is_paid', 'action'])
                ->make(true);
        }
        return view('hr.leave_types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_paid' => 'required|boolean',
            'default_quota' => 'required|integer|min:0',
        ]);

        LeaveType::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'is_paid' => $request->is_paid,
            'default_quota' => $request->default_quota,
        ]);

        return response()->json(['message' => 'Leave Type created successfully']);
    }

    public function show(LeaveType $leaveType)
    {
        return response()->json($leaveType);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_paid' => 'required|boolean',
            'default_quota' => 'required|integer|min:0',
        ]);

        $leaveType->update($request->only(['name', 'is_paid', 'default_quota']));

        return response()->json(['message' => 'Leave Type updated successfully']);
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return response()->json(['message' => 'Leave Type deleted successfully']);
    }
}
