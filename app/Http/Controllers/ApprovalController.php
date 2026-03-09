<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        $approvals = ApprovalRequest::with(['approvable', 'requestedBy'])
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    public function approve(Request $request, $id)
    {
        $approval = ApprovalRequest::findOrFail($id);

        if ($approval->status !== 'Pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        DB::beginTransaction();
        try {
            $approval->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
                'notes' => $request->get('notes'),
            ]);

            // Update the source document status
            $document = $approval->approvable;

            // Generic status update for SO/PO
            if (method_exists($document, 'update')) {
                $document->update(['status' => 'Approved']);
            }

            DB::commit();
            return redirect()->route('approvals.index')->with('success', 'Request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $approval = ApprovalRequest::findOrFail($id);

        if ($approval->status !== 'Pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        DB::beginTransaction();
        try {
            $approval->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(),
                'notes' => $request->get('notes'),
            ]);

            // Update source document status
            $document = $approval->approvable;
            if (method_exists($document, 'update')) {
                $document->update(['status' => 'Draft']); // Return to Draft on rejection
            }

            DB::commit();
            return redirect()->route('approvals.index')->with('success', 'Request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }

    // Direct methods for email links
    public function approveViaLink($id)
    {
        // This would ideally require authentication or a secure token
        // For simplicity, we check if authenticated, otherwise redirect to login
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Please login to process approval.');
        }

        return $this->approve(new Request(), $id);
    }

    public function rejectViaLink($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Please login to process approval.');
        }

        return $this->reject(new Request(), $id);
    }
}
