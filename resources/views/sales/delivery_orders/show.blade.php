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
                                    <td width="130">Shipment Date</td>
                                    <td>: <strong>{{ $deliveryOrder->delivery_date->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Method</td>
                                    <td>: <span class="badge badge-outline-dark">{{ $deliveryOrder->shipping_method ?: 'Not set' }}</span></td>
                                </tr>
                                <tr>
                                    <td>Tracking #</td>
                                    <td>: <strong>{{ $deliveryOrder->tracking_number ?: '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Warehouse</td>
                                    <td>: {{ $deliveryOrder->warehouse->name }}</td>
                                </tr>
                                <tr>
                                    <td>SO Ref</td>
                                    <td>: <a href="{{ route('sales-orders.show', $deliveryOrder->sales_order_id) }}"><strong>{{ $deliveryOrder->salesOrder->so_number }}</strong></a></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-4 border-right">
                            <h6 class="text-muted font-weight-bold mb-3 uppercase small">Customer & Receiver Info</h6>
                            <address class="mb-3">
                                <strong class="text-lg text-primary">{{ $deliveryOrder->salesOrder->customer->name }}</strong><br>
                                {{ $deliveryOrder->salesOrder->customer->address ?: 'No address' }}<br>
                                <i class="fas fa-phone mr-1 text-muted"></i> {{ $deliveryOrder->salesOrder->customer->phone ?: '-' }}
                            </address>
                            
                            @if ($deliveryOrder->status == 'Received')
                                <div class="p-2 border rounded bg-success-light">
                                    <label class="small font-weight-bold mb-0 text-success"><i class="fas fa-check-circle mr-1"></i> Received Info:</label>
                                    <p class="mb-0 small"><strong>By:</strong> {{ $deliveryOrder->received_by }}</p>
                                    <p class="mb-0 small"><strong>On:</strong> {{ $deliveryOrder->received_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
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
                    
                    @if ($deliveryOrder->status == 'Shipped')
                        <button type="button" class="btn btn-success btn-sm ml-2 px-4 font-weight-bold shadow-sm" 
                                data-toggle="modal" data-target="#receiveModal">
                            <i class="fas fa-check-double mr-1"></i> Mark as Received
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($deliveryOrder->status == 'Shipped')
    <!-- Receive Modal -->
    <div class="modal fade" id="receiveModal" tabindex="-1" role="dialog" aria-labelledby="receiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('delivery-orders.confirm-delivery', $deliveryOrder->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white text-sm">
                        <h5 class="modal-title font-weight-bold" id="receiveModalLabel"><i class="fas fa-handshake mr-2"></i> Confirm Cargo Receipt</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-sm">
                        <p class="mb-3">Please confirm that the items have been successfully delivered to the customer.</p>
                        <div class="form-group border-left border-success pl-2">
                            <label class="font-weight-bold small uppercase text-muted">Receiver Name <span class="text-danger">*</span></label>
                            <input type="text" name="received_by" class="form-control form-control-sm" 
                                   placeholder="Full name of person who received the goods" required>
                        </div>
                        <div class="form-group border-left border-success pl-2">
                            <label class="font-weight-bold small uppercase text-muted">Date & Time Received <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="received_at" class="form-control form-control-sm" 
                                   value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold shadow-sm">Confirm & Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection
