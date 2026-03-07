@extends('layouts.app')

@section('page-title', 'Sales Order Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title font-weight-bold mb-0">SO: {{ $salesOrder->so_number }}</h3>
                    <div>
                        <a href="{{ route('delivery-orders.create', ['sales_order_id' => $salesOrder->id]) }}"
                            class="btn btn-success btn-xs mr-2">
                            <i class="fas fa-truck mr-1"></i> Create Delivery
                        </a>
                        <a href="{{ route('sales-invoices.create', ['sales_order_id' => $salesOrder->id]) }}"
                            class="btn btn-primary btn-xs mr-2">
                            <i class="fas fa-file-invoice mr-1"></i> Create Invoice
                        </a>
                        <span class="badge badge-lg badge-info px-4 py-2 border shadow-sm">{{ $salesOrder->status }}</span>
                    </div>
                </div>
                <div class="card-body text-sm">
                    <div class="row mb-5">
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">General Info</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120">Order Date</td>
                                    <td>: <strong>{{ $salesOrder->order_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td>: <span
                                            class="badge badge-{{ $salesOrder->transaction_type == 'Online' ? 'primary' : 'secondary' }}">{{ $salesOrder->transaction_type }}</span>
                                    </td>
                                </tr>
                                @if ($salesOrder->transaction_type == 'Online')
                                    <tr>
                                        <td>Marketplace</td>
                                        <td>: <strong>{{ optional($salesOrder->marketplace)->name ?? '-' }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>Notes</td>
                                    <td>: <span class="text-italic">{{ $salesOrder->notes ?: '-' }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Customer Info</h6>
                            <address class="mb-0">
                                <strong class="text-primary text-lg">{{ $salesOrder->customer->name }}</strong><br>
                                {{ $salesOrder->customer->address ?: 'No address provided' }}<br>
                                <i class="fas fa-phone mr-1 text-muted"></i> {{ $salesOrder->customer->phone ?: '-' }}<br>
                                <i class="fas fa-envelope mr-1 text-muted"></i> {{ $salesOrder->customer->email ?: '-' }}
                            </address>
                        </div>
                        <div class="col-sm-4 bg-light p-3 rounded shadow-sm border">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Order Summary</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-right">Rp {{ number_format($salesOrder->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax Amount</td>
                                    <td class="text-right">Rp {{ number_format($salesOrder->tax_amount, 2) }}</td>
                                </tr>
                                @if ($salesOrder->transaction_type == 'Online')
                                    <tr class="text-danger small">
                                        <td>Platform Fee (-)</td>
                                        <td class="text-right">- Rp {{ number_format($salesOrder->platform_fee, 2) }}</td>
                                    </tr>
                                    <tr class="text-danger small">
                                        <td>Platform Discount (-)</td>
                                        <td class="text-right">- Rp {{ number_format($salesOrder->platform_discount, 2) }}
                                        </td>
                                    </tr>
                                    <tr class="text-success small">
                                        <td>Platform Voucher (+)</td>
                                        <td class="text-right">+ Rp {{ number_format($salesOrder->platform_voucher, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-top border-dark font-weight-bold text-lg mt-2">
                                    <td>Net Amount</td>
                                    <td class="text-right text-primary">Rp {{ number_format($salesOrder->net_amount, 2) }}
                                    </td>
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
                                    @foreach ($salesOrder->lines as $index => $line)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $line->product->name }}</strong><br>
                                                <small class="text-muted uppercase">SKU: {{ $line->product->sku }}</small>
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
                    <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary btn-sm px-4 font-weight-bold"><i
                            class="fas fa-arrow-left mr-1"></i> Back to List</a>
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm ml-2 px-4 font-weight-bold"><i
                            class="fas fa-print mr-1"></i> Print Order</button>
                    @if ($salesOrder->status == 'Draft')
                        <button class="btn btn-primary btn-sm ml-2 px-4 font-weight-bold shadow-sm"><i
                                class="fas fa-check mr-1"></i> Approve Order</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
