@extends('layouts.app')

@section('page-title', 'PO Details: ' . $purchaseOrder->po_number)

@section('content')
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">PO: {{ $purchaseOrder->po_number }}</h3>
                    @if ($purchaseOrder->status == 'Draft')
                        <form action="{{ route('purchase-orders.submit', $purchaseOrder->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-xs mr-2">
                                <i class="fas fa-paper-plane mr-1"></i> Submit for Approval
                            </button>
                        </form>
                    @endif
                    <span
                        class="badge badge-{{ $purchaseOrder->status == 'Approved' ? 'info' : ($purchaseOrder->status == 'Open' ? 'success' : ($purchaseOrder->status == 'Pending' ? 'warning' : 'secondary')) }} px-3 py-2">
                        {{ $purchaseOrder->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6 border-right">
                        <h6 class="text-muted text-xs uppercase font-weight-bold">Supplier Info</h6>
                        <p class="mb-0 font-weight-bold">{{ $purchaseOrder->supplier->name }}</p>
                        <p class="text-sm mb-0">{{ $purchaseOrder->supplier->address }}</p>
                        <p class="text-sm mb-0">Phone: {{ $purchaseOrder->supplier->phone }}</p>
                    </div>
                    <div class="col-sm-3 border-right">
                        <h6 class="text-muted text-xs uppercase font-weight-bold">Order Date</h6>
                        <p class="mb-2">{{ $purchaseOrder->order_date->format('l, d F Y') }}</p>
                        <h6 class="text-muted text-xs uppercase font-weight-bold">Currency</h6>
                        <p class="mb-0">{{ $purchaseOrder->currency->name }} ({{ $purchaseOrder->currency->code }})
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <h6 class="text-muted text-xs uppercase font-weight-bold">Payment Terms</h6>
                        <p class="mb-0">{{ $purchaseOrder->paymentTerm->name ?? 'None' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light text-xs uppercase">
                            <tr>
                                <th>Item / Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($purchaseOrder->lines as $line)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{ $line->product?->name ?? 'Misc' }}</span><br>
                                        <small class="text-muted">{{ $line->description }}</small>
                                    </td>
                                    <td class="text-center">{{ number_format($line->quantity, 2) }}
                                        {{ $line->unit?->symbol }}</td>
                                    <td class="text-right">{{ number_format($line->unit_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($line->tax_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($line->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">TOTAL (EXCL. TAX)</td>
                                <td class="text-right">{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right text-success">TOTAL TAX</td>
                                <td class="text-right text-success">{{ number_format($purchaseOrder->tax_amount, 2) }}
                                </td>
                            </tr>
                            <tr class="table-info uppercase" style="font-size: 1.1rem">
                                <td colspan="4" class="text-right">NET AMOUNT DUE</td>
                                <td class="text-right">{{ $purchaseOrder->currency->code }}
                                    {{ number_format($purchaseOrder->net_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($purchaseOrder->notes)
                    <div class="mt-4 p-3 bg-light border-left border-dark rounded">
                        <h6 class="text-xs uppercase font-weight-bold mb-1">Additional Notes</h6>
                        <p class="text-sm mb-0 italic">"{{ $purchaseOrder->notes }}"</p>
                    </div>
                @endif

                @if ($approval)
                    <div
                        class="mt-4 alert alert-{{ $approval->status == 'Approved' ? 'success' : ($approval->status == 'Rejected' ? 'danger' : 'warning') }} border shadow-sm">
                        <h6 class="font-weight-bold mb-1 uppercase small"><i class="fas fa-shield-alt mr-1"></i> Approval
                            Context</h6>
                        <p class="mb-1 text-xs">Status: <strong>{{ $approval->status }}</strong></p>
                        <p class="mb-1 text-xs">Requested by: {{ $approval->requestedBy->name }} on
                            {{ $approval->created_at->format('d M Y H:i') }}</p>
                        @if ($approval->approved_by)
                            <p class="mb-1 text-xs">Decided by: {{ $approval->approvedBy->name }}</p>
                        @endif
                        @if ($approval->notes)
                            <p class="mb-0 text-xs italic">"{{ $approval->notes }}"</p>
                        @endif
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white text-right">
                <button class="btn btn-outline-dark btn-sm mr-2"><i class="fas fa-print mr-1"></i> Print PO</button>
                <button class="btn btn-outline-primary btn-sm mr-2"><i class="fas fa-paper-plane mr-1"></i> Email to
                    Supplier</button>
                @if ($purchaseOrder->status == 'Draft')
                    <form action="{{ route('purchase-orders.submit', $purchaseOrder->id) }}" method="POST"
                        class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check-circle mr-1"></i>
                            Submit for Approval</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary">
                <h3 class="card-title text-sm">PO Timeline / Logs</h3>
            </div>
            <div class="card-body p-0">
                <div class="p-3 text-xs border-bottom">
                    <p class="mb-1 font-weight-bold">Draft Created</p>
                    <p class="text-muted mb-0">{{ $purchaseOrder->created_at->format('d/m/Y H:i') }} by SYSTEM</p>
                </div>
                {{-- Dynamic logs would go here --}}
            </div>
        </div>
    </div>
    </div>
@endsection
