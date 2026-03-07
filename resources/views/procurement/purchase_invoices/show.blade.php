@extends('layouts.app')

@section('page-title', 'Purchase Invoice Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">Invoice: {{ $purchaseInvoice->invoice_number }}</h3>
                    <span class="badge badge-info px-3 py-2">{{ $purchaseInvoice->status }}</span>
                </div>
                <div class="card-body text-sm">
                    <div class="row mb-4">
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">General Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="150">Invoice Date</td>
                                    <td>: <strong>{{ $purchaseInvoice->invoice_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Due Date</td>
                                    <td>:
                                        <strong>{{ $purchaseInvoice->due_date ? $purchaseInvoice->due_date->format('d M Y') : '-' }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Vendor Invoice #</td>
                                    <td>: <strong>{{ $purchaseInvoice->vendor_invoice_number ?: '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>PO Ref</td>
                                    <td>: <strong>{{ $purchaseInvoice->purchaseOrder->po_number ?? '-' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">Supplier Info</h6>
                            <address>
                                <strong>{{ $purchaseInvoice->supplier->name }}</strong><br>
                                {{ $purchaseInvoice->supplier->phone }}<br>
                                {{ $purchaseInvoice->supplier->email }}
                            </address>
                        </div>
                        <div class="col-sm-4">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase">Financial Summary</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Total Amount</td>
                                    <td class="text-right">Rp {{ number_format($purchaseInvoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax Amount</td>
                                    <td class="text-right">Rp {{ number_format($purchaseInvoice->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="border-top font-weight-bold">
                                    <td>Net Amount</td>
                                    <td class="text-right">Rp {{ number_format($purchaseInvoice->net_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 px-0">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Product / Description</th>
                                        <th width="100">Qty</th>
                                        <th width="150">Unit Price</th>
                                        <th width="120">Tax</th>
                                        <th width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseInvoice->lines as $index => $line)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $line->product->name }}</strong><br>
                                                <small class="text-muted">SKU: {{ $line->product->sku }}</small>
                                            </td>
                                            <td class="text-right px-3">{{ number_format($line->quantity, 2) }}</td>
                                            <td class="text-right px-3">{{ number_format($line->unit_price, 2) }}</td>
                                            <td class="text-right px-3">{{ number_format($line->tax_amount, 2) }}</td>
                                            <td class="text-right px-3 font-weight-bold">
                                                {{ number_format($line->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-right">
                    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary btn-sm"><i
                            class="fas fa-arrow-left mr-1"></i> Back to List</a>
                    <button onclick="window.print()" class="btn btn-default btn-sm ml-2"><i class="fas fa-print mr-1"></i>
                        Print Invoice</button>
                </div>
            </div>
        </div>
    </div>
@endsection
