@extends('layouts.app')

@section('page-title', 'Approval Workflow')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold mb-0 text-dark"><i
                            class="fas fa-check-shield mr-2 text-warning"></i> Pending Approvals</h3>
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
                                @if(count($approvals) > 0)
                                    @foreach ($approvals as $approval)
                                        <tr>
                                            <td>{{ $approval->created_at ? $approval->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>{{ str_replace('App\\Models\\', '', $approval->approvable_type) }}</td>
                                            <td>
                                                <strong>{{ $approval->approvable->so_number ?? ($approval->approvable->po_number ?? ($approval->approvable->number ?? 'N/A')) }}</strong>
                                            </td>
                                            <td>{{ $approval->requestedBy->name ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $approval->status == 'Pending' ? 'warning' : ($approval->status == 'Approved' ? 'success' : 'danger') }}">
                                                    {{ $approval->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $showRoute = '#';
                                                    if ($approval->approvable_type == 'App\\Models\\Sales\\SalesOrder') {
                                                        $showRoute = route('sales-orders.show', $approval->approvable_id);
                                                    } elseif (
                                                        $approval->approvable_type ==
                                                        'App\\Models\\Procurement\\PurchaseOrder'
                                                    ) {
                                                        $showRoute = route('purchase-orders.show', $approval->approvable_id);
                                                    }
                                                @endphp
                                                <a href="{{ $showRoute }}" class="btn btn-xs btn-info mr-2 px-3"
                                                    title="View Details">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>

                                                @if ($approval->status == 'Pending')
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
                                                @else
                                                    <span class="text-muted small italic">Processed by
                                                        {{ $approval->processedBy->name ?? 'System' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted italic">No pending approval
                                            requests</td>
                                    </tr>
                                @endif
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
