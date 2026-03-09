@extends('layouts.app')

@section('page-title', 'Approval Workflow')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark">
                    <h3 class="card-title font-weight-bold">Pending Approvals</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover text-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Document Type</th>
                                    <th>Document No</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th width="250">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($approvals as $approval)
                                    <tr>
                                        <td>{{ $approval->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ str_replace('App\\Models\\', '', $approval->approvable_type) }}</td>
                                        <td>
                                            <strong>{{ $approval->approvable->so_number ?? ($approval->approvable->po_number ?? ($approval->approvable->number ?? 'N/A')) }}</strong>
                                        </td>
                                        <td>{{ $approval->requestedBy->name }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $approval->status == 'Approved' ? 'success' : ($approval->status == 'Rejected' ? 'danger' : 'warning') }}">
                                                {{ $approval->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($approval->status == 'Pending')
                                                <div class="d-flex">
                                                    <form action="{{ route('approvals.approve', $approval->id) }}"
                                                        method="POST" class="mr-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success px-3">
                                                            <i class="fas fa-check mr-1"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('approvals.reject', $approval->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-danger px-3">
                                                            <i class="fas fa-times mr-1"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted small">Processed by
                                                    {{ $approval->approvedBy->name ?? 'SYSTEM' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No pending approval requests
                                            found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $approvals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
