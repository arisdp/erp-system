<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JobPositionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $positions = JobPosition::select(['id', 'name']);
            return DataTables::of($positions)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit-btn" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                })
                ->make(true);
        }
        return view('hr.positions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:job_positions,name,NULL,id,company_id,' . auth()->user()->company_id,
        ]);

        JobPosition::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Position created successfully']);
    }

    public function show(JobPosition $jobPosition)
    {
        return response()->json($jobPosition);
    }

    public function update(Request $request, JobPosition $jobPosition)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:job_positions,name,' . $jobPosition->id . ',id,company_id,' . auth()->user()->company_id,
        ]);

        $jobPosition->update($request->only(['name']));

        return response()->json(['message' => 'Position updated successfully']);
    }

    public function destroy(JobPosition $jobPosition)
    {
        $jobPosition->delete();
        return response()->json(['message' => 'Position deleted successfully']);
    }
}
