@extends('layouts.app')

@section('page-title', 'Create Goods Receipt Note')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ route('goods-receipts.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h3 class="card-title"><i class="fas fa-plus mr-2"></i> GRN Header Information</h3>
                    </div>
                    <div class="card-body text-sm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>GRN Number</label>
                                    <input type="text" class="form-control form-control-sm" value="{{ $nextNumber }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Received Date <span class="text-danger">*</span></label>
                                    <input type="date" name="received_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Order</label>
                                    <select name="purchase_order_id" id="poSelect"
                                        class="form-control form-control-sm select2">
                                        <option value="">-- Direct Receipt (No PO) --</option>
                                        @foreach ($purchaseOrders as $po)
                                            <option value="{{ $po->id }}"
                                                {{ isset($purchaseOrder) && $purchaseOrder->id == $po->id ? 'selected' : '' }}>
                                                {{ $po->po_number }} - {{ $po->supplier->name ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Destination Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" class="form-control form-control-sm select2" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Notes / Remarks</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="4"
                                        placeholder="Any additional information..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title"><i class="fas fa-list mr-2"></i> Items to Receive</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0" id="grnItemsTable">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th width="30%">Product</th>
                                    <th width="10%">Unit</th>
                                    <th width="15%">Qty Ordered</th>
                                    <th width="15%">Qty Received <span class="text-danger">*</span></th>
                                    <th>Batch / Expiry</th>
                                </tr>
                            </thead>
                            <tbody id="grnItemsBody">
                                @if (isset($purchaseOrder))
                                    @foreach ($purchaseOrder->lines as $index => $line)
                                        <tr>
                                            <td>
                                                <input type="hidden"
                                                    name="lines[{{ $index }}][purchase_order_line_id]"
                                                    value="{{ $line->id }}">
                                                <input type="hidden" name="lines[{{ $index }}][product_id]"
                                                    value="{{ $line->product_id }}">
                                                {{ $line->product->name }} ({{ $line->product->sku }})
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" name="lines[{{ $index }}][unit_id]"
                                                    value="{{ $line->unit_id }}">
                                                {{ $line->unit->symbol ?? '-' }}
                                            </td>
                                            <td class="text-right px-3">
                                                <input type="hidden" name="lines[{{ $index }}][quantity_ordered]"
                                                    value="{{ $line->quantity }}">
                                                {{ number_format($line->quantity, 2) }}
                                            </td>
                                            <td>
                                                <input type="number" name="lines[{{ $index }}][quantity_received]"
                                                    class="form-control form-control-sm text-right" step="0.000001"
                                                    value="{{ $line->quantity }}">
                                            </td>
                                            <td>
                                                <div class="row no-gutters">
                                                    <div class="col-6 pr-1">
                                                        <input type="text"
                                                            name="lines[{{ $index }}][batch_number]"
                                                            class="form-control form-control-sm" placeholder="Batch #">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="date"
                                                            name="lines[{{ $index }}][expiry_date]"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center py-4 text-muted small italic">
                                            Select a Purchase Order to populate items or add manually in Phase 6.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-default btn-sm mr-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save mr-1"></i> Post Goods Receipt
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

            $('#poSelect').change(function() {
                let poId = $(this).val();
                if (poId) {
                    window.location.href = "{{ route('goods-receipts.create') }}?purchase_order_id=" +
                    poId;
                } else {
                    window.location.href = "{{ route('goods-receipts.create') }}";
                }
            });
        });
    </script>
@endpush
