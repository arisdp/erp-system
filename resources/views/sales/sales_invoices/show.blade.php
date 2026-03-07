@extends('layouts.app')

@section('page-title', 'Sales Invoice Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title font-weight-bold mb-0">Invoice: {{ $salesInvoice->invoice_number }}</h3>
                    <div>
                        <span
                            class="badge badge-lg badge-{{ $salesInvoice->status == 'Paid' ? 'success' : 'warning' }} px-4 py-2 border shadow-sm">{{ $salesInvoice->status }}</span>
                    </div>
                </div>
                <div class="card-body text-sm">
                    <div class="row mb-5">
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Invoice Info</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120">Invoice Date</td>
                                    <td>: <strong>{{ $salesInvoice->invoice_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Due Date</td>
                                    <td>: <span
                                            class="text-danger font-weight-bold">{{ $salesInvoice->due_date ? $salesInvoice->due_date->format('d M Y') : '-' }}</span>
                                    </td>
                                </tr>
                                @if ($salesInvoice->salesOrder)
                                    <tr>
                                        <td>Ref. SO</td>
                                        <td>: <a
                                                href="{{ route('sales-orders.show', $salesInvoice->salesOrder->id) }}">{{ $salesInvoice->salesOrder->so_number }}</a>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Customer Info</h6>
                            <address class="mb-0">
                                <strong class="text-primary text-lg">{{ $salesInvoice->customer->name }}</strong><br>
                                {{ $salesInvoice->customer->address ?: 'No address provided' }}<br>
                                <i class="fas fa-phone mr-1 text-muted"></i> {{ $salesInvoice->customer->phone ?: '-' }}
                            </address>
                        </div>
                        <div class="col-sm-4 bg-light p-3 rounded shadow-sm border">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Invoice Summary</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-right">Rp {{ number_format($salesInvoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax Amount</td>
                                    <td class="text-right">Rp {{ number_format($salesInvoice->tax_amount, 2) }}</td>
                                </tr>
                                @if ($salesInvoice->platform_fee > 0)
                                    <tr class="text-danger small">
                                        <td>Platform Fee (-)</td>
                                        <td class="text-right">- Rp {{ number_format($salesInvoice->platform_fee, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-top border-dark font-weight-bold text-lg">
                                    <td>Net Amount</td>
                                    <td class="text-right text-primary">Rp
                                        {{ number_format($salesInvoice->net_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row px-2">
                        <div class="col-12">
                            <table class="table table-bordered table-striped table-sm text-sm">
                                <thead class="bg-dark color-white text-center text-xs uppercase">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Product / Description</th>
                                        <th width="100">Unit</th>
                                        <th width="120">Quantity</th>
                                        <th width="150">Unit Price</th>
                                        <th width="120">Tax</th>
                                        <th width="150" class="bg-secondary">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesInvoice->lines as $index => $line)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $line->product->name }}</strong><br>
                                                <small class="text-muted">SKU: {{ $line->product->sku }}</small>
                                            </td>
                                            <td class="text-center align-middle">{{ $line->unit->symbol ?? '-' }}</td>
                                            <td class="text-right align-middle px-3">
                                                {{ number_format($line->quantity, 2) }}</td>
                                            <td class="text-right align-middle px-3">Rp
                                                {{ number_format($line->unit_price, 2) }}</td>
                                            <td class="text-right align-middle px-3 text-muted">Rp
                                                {{ number_format($line->tax_amount, 2) }}</td>
                                            <td class="text-right align-middle px-3 font-weight-bold text-dark">Rp
                                                {{ number_format($line->subtotal + $line->tax_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top text-right py-3">
                    <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary btn-sm px-4 font-weight-bold"><i
                            class="fas fa-arrow-left mr-1"></i> Back to List</a>
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm ml-2 px-4 font-weight-bold"><i
                            class="fas fa-print mr-1"></i> Print Invoice</button>
                    @if ($salesInvoice->status == 'Unpaid')
                        <button class="btn btn-success btn-sm ml-2 px-4 font-weight-bold shadow-sm"><i
                                class="fas fa-money-bill mr-1"></i> Record Payment</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
