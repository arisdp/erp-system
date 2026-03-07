@extends('layouts.app')

@section('page-title', 'Delivery Order Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title font-weight-bold mb-0">DO: {{ $deliveryOrder->do_number }}</h3>
                    <span class="badge badge-success px-4 py-2 shadow-sm border">{{ $deliveryOrder->status }}</span>
                </div>
                <div class="card-body text-sm">
                    <div class="row mb-5">
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Shipment Info</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120">Shipment Date</td>
                                    <td>: <strong>{{ $deliveryOrder->delivery_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Shipped By</td>
                                    <td>: <strong>{{ $deliveryOrder->shipped_by ?: '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Warehouse</td>
                                    <td>: {{ $deliveryOrder->warehouse->name }}</td>
                                </tr>
                                <tr>
                                    <td>SO Ref</td>
                                    <td>: <a
                                            href="{{ route('sales-orders.show', $deliveryOrder->sales_order_id) }}"><strong>{{ $deliveryOrder->salesOrder->so_number }}</strong></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Customer Info</h6>
                            <address>
                                <strong
                                    class="text-lg text-primary">{{ $deliveryOrder->salesOrder->customer->name }}</strong><br>
                                {{ $deliveryOrder->salesOrder->customer->address ?: 'No address' }}<br>
                                <i class="fas fa-phone mr-1 text-muted"></i>
                                {{ $deliveryOrder->salesOrder->customer->phone ?: '-' }}
                            </address>
                        </div>
                        <div class="col-sm-4">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Internal Memo</h6>
                            <div class="p-3 bg-light rounded text-italic">
                                {{ $deliveryOrder->notes ?: 'No additional notes provided.' }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 px-0">
                            <table class="table table-bordered table-striped table-sm text-sm">
                                <thead class="bg-light text-center text-xs uppercase font-weight-bold">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Product / Description</th>
                                        <th width="120">Unit</th>
                                        <th width="150" class="text-center">Qty Ordered</th>
                                        <th width="150" class="text-center bg-secondary">Qty Shipped</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deliveryOrder->lines as $index => $line)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $line->product->name }}</strong><br>
                                                <small class="text-muted text-uppercase">SKU:
                                                    {{ $line->product->sku }}</small>
                                            </td>
                                            <td class="text-center align-middle text-uppercase small font-weight-bold">
                                                {{ $line->unit->symbol }}</td>
                                            <td class="text-center align-middle font-weight-bold">
                                                {{ number_format($line->quantity_ordered, 2) }}</td>
                                            <td class="text-center align-middle font-weight-bold text-success text-lg">
                                                {{ number_format($line->quantity_shipped, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top text-right py-3">
                    <a href="{{ route('delivery-orders.index') }}"
                        class="btn btn-secondary btn-sm px-4 font-weight-bold shadow-sm"><i
                            class="fas fa-arrow-left mr-1"></i> Back to List</a>
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm ml-2 px-4 font-weight-bold"><i
                            class="fas fa-print mr-1"></i> Print Delivery Note</button>
                    @if ($deliveryOrder->status == 'Draft')
                        <button class="btn btn-success btn-sm ml-2 px-4 font-weight-bold shadow-sm"><i
                                class="fas fa-truck mr-1"></i> Confirm Dispatch</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
