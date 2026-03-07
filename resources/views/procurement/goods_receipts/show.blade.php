@extends('layouts.app')

@section('page-title', 'Goods Receipt Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">GRN: {{ $goodsReceipt->grn_number }}</h3>
                    <div>
                        <a href="{{ route('purchase-invoices.create', ['goods_receipt_id' => $goodsReceipt->id]) }}"
                            class="btn btn-primary btn-xs mr-2">
                            <i class="fas fa-file-invoice mr-1"></i> Create Invoice
                        </a>
                        <span class="badge badge-success px-3 py-2">{{ $goodsReceipt->status }}</span>
                    </div>
                </div>
                <div class="card-body text-sm">
                    <div class="row mb-4">
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">General Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120">Date</td>
                                    <td>: <strong>{{ $goodsReceipt->received_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Warehouse</td>
                                    <td>: <strong>{{ $goodsReceipt->warehouse->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>PO Ref</td>
                                    <td>: <strong>{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Received By</td>
                                    <td>: {{ $goodsReceipt->received_by }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">Supplier Info</h6>
                            @if ($goodsReceipt->purchaseOrder)
                                <address>
                                    <strong>{{ $goodsReceipt->purchaseOrder->supplier->name }}</strong><br>
                                    {{ $goodsReceipt->purchaseOrder->supplier->phone }}<br>
                                    {{ $goodsReceipt->purchaseOrder->supplier->email }}
                                </address>
                            @else
                                <p class="text-muted">Direct receipt registration.</p>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">Notes</h6>
                            <p class="text-italic">{{ $goodsReceipt->notes ?: 'No additional notes provided.' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 px-0">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Product / Description</th>
                                        <th width="100">Unit</th>
                                        <th width="120">Qty Ordered</th>
                                        <th width="120">Qty Received</th>
                                        <th>Batch / Expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($goodsReceipt->lines as $index => $line)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $line->product->name }}</strong><br>
                                                <small class="text-muted">SKU: {{ $line->product->sku }}</small>
                                            </td>
                                            <td class="text-center">{{ $line->unit->symbol ?? '-' }}</td>
                                            <td class="text-right px-3 font-weight-bold text-primary">
                                                {{ number_format($line->quantity_ordered, 2) }}</td>
                                            <td class="text-right px-3 font-weight-bold text-success">
                                                {{ number_format($line->quantity_received, 2) }}</td>
                                            <td class="text-center small">
                                                @if ($line->batch_number)
                                                    <span class="badge badge-light border">B:
                                                        {{ $line->batch_number }}</span>
                                                @endif
                                                @if ($line->expiry_date)
                                                    <span class="badge badge-light border">E:
                                                        {{ $line->expiry_date->format('d/m/Y') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-right">
                    <a href="{{ route('goods-receipts.index') }}" class="btn btn-secondary btn-sm"><i
                            class="fas fa-arrow-left mr-1"></i> Back to List</a>
                    <button onclick="window.print()" class="btn btn-default btn-sm ml-2"><i class="fas fa-print mr-1"></i>
                        Print</button>
                </div>
            </div>
        </div>
    </div>
@endsection
