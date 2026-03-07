@extends('layouts.app')

@section('page-title', 'Create Delivery Order')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ route('delivery-orders.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-truck-loading mr-2"></i> Delivery Header</h3>
                    </div>
                    <div class="card-body text-sm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">DO Number</label>
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="{{ $nextNumber }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery Date <span class="text-danger">*</span></label>
                                    <input type="date" name="delivery_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sales Order <span class="text-danger">*</span></label>
                                    <select name="sales_order_id" class="form-control form-control-sm select2" required
                                        onchange="window.location.href='{{ route('delivery-orders.create') }}?sales_order_id=' + this.value">
                                        <option value="">Select Approved SO</option>
                                        @foreach ($salesOrders as $so)
                                            <option value="{{ $so->id }}"
                                                {{ isset($salesOrder) && $salesOrder->id == $so->id ? 'selected' : '' }}>
                                                {{ $so->so_number }} - {{ $so->customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" class="form-control form-control-sm select2" required>
                                        <option value="">Select Source Warehouse</option>
                                        @foreach ($warehouses as $w)
                                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Shipped By</label>
                                    <input type="text" name="shipped_by" class="form-control form-control-sm"
                                        placeholder="Driver name / Courier">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-boxes mr-2"></i> Shipped Items</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-light text-center text-xs uppercase">
                                <tr>
                                    <th>Product</th>
                                    <th width="150">Unit</th>
                                    <th width="150">Qty Ordered</th>
                                    <th width="200">Qty Shipped <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($salesOrder))
                                    @foreach ($salesOrder->lines as $index => $line)
                                        <tr>
                                            <td class="align-middle px-3">
                                                <input type="hidden"
                                                    name="lines[{{ $index }}][sales_order_line_id]"
                                                    value="{{ $line->id }}">
                                                <input type="hidden" name="lines[{{ $index }}][product_id]"
                                                    value="{{ $line->product_id }}">
                                                <input type="hidden" name="lines[{{ $index }}][unit_id]"
                                                    value="{{ $line->unit_id }}">
                                                <strong>{{ $line->product->name }}</strong>
                                            </td>
                                            <td
                                                class="text-center align-middle uppercase small font-weight-bold text-muted">
                                                {{ $line->unit->symbol }}
                                            </td>
                                            <td class="text-center align-middle font-weight-bold">
                                                <input type="hidden" name="lines[{{ $index }}][quantity_ordered]"
                                                    value="{{ $line->quantity }}">
                                                {{ number_format($line->quantity, 2) }}
                                            </td>
                                            <td>
                                                <input type="number" name="lines[{{ $index }}][quantity_shipped]"
                                                    class="form-control form-control-sm text-right" step="0.000001"
                                                    value="{{ $line->quantity }}" max="{{ $line->quantity }}" required>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small italic">Select a Sales
                                            Order to load items</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-right py-3">
                        <a href="{{ route('delivery-orders.index') }}"
                            class="btn btn-default btn-sm mr-2 px-4 shadow-sm border">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm px-5 shadow-sm font-weight-bold"
                            {{ !isset($salesOrder) ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane mr-1"></i> Post Delivery
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@endpush
