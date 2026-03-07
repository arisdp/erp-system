@extends('layouts.app')

@section('page-title', 'Create Purchase Invoice')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ route('purchase-invoices.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i> Invoice Header</h3>
                    </div>
                    <div class="card-body text-sm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Internal Invoice #</label>
                                    <input type="text" class="form-control form-control-sm" value="{{ $nextNumber }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Vendor Invoice #</label>
                                    <input type="text" name="vendor_invoice_number" class="form-control form-control-sm"
                                        placeholder="e.g. INV/2024/001">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplierSelect"
                                        class="form-control form-control-sm select2" required>
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $s)
                                            <option value="{{ $s->id }}"
                                                {{ isset($goodsReceipt) && $goodsReceipt->purchaseOrder && $goodsReceipt->purchaseOrder->supplier_id == $s->id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Invoice Date <span class="text-danger">*</span></label>
                                            <input type="date" name="invoice_date" class="form-control form-control-sm"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Due Date</label>
                                            <input type="date" name="due_date" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Order Ref</label>
                                    <input type="hidden" name="purchase_order_id"
                                        value="{{ $goodsReceipt->purchase_order_id ?? '' }}">
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title"><i class="fas fa-list mr-2"></i> Invoice Lines</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>Product</th>
                                    <th width="120">Quantity</th>
                                    <th width="150">Unit Price</th>
                                    <th width="150">Tax Rate</th>
                                    <th width="150">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($goodsReceipt))
                                    @foreach ($goodsReceipt->lines as $index => $line)
                                        <tr>
                                            <td>
                                                <input type="hidden"
                                                    name="lines[{{ $index }}][goods_receipt_line_id]"
                                                    value="{{ $line->id }}">
                                                <input type="hidden" name="lines[{{ $index }}][product_id]"
                                                    value="{{ $line->product_id }}">
                                                <input type="hidden" name="lines[{{ $index }}][unit_id]"
                                                    value="{{ $line->unit_id }}">
                                                {{ $line->product->name }}
                                            </td>
                                            <td>
                                                <input type="number" name="lines[{{ $index }}][quantity]"
                                                    class="form-control form-control-sm text-right qty-input"
                                                    step="0.000001" value="{{ $line->quantity_received }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="lines[{{ $index }}][unit_price]"
                                                    class="form-control form-control-sm text-right price-input"
                                                    step="0.01" value="{{ $line->purchaseOrderLine->unit_price ?? 0 }}">
                                            </td>
                                            <td>
                                                <select name="lines[{{ $index }}][tax_rate_id]"
                                                    class="form-control form-control-sm tax-select">
                                                    <option value="" data-rate="0">No Tax</option>
                                                    @foreach (\App\Models\TaxRate::where('is_active', true)->get() as $tr)
                                                        <option value="{{ $tr->id }}"
                                                            data-rate="{{ $tr->rate }}"
                                                            {{ isset($line->purchaseOrderLine) && $line->purchaseOrderLine->tax_rate_id == $tr->id ? 'selected' : '' }}>
                                                            {{ $tr->name }} ({{ number_format($tr->rate, 0) }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm text-right line-subtotal"
                                                    value="0" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right px-3">Subtotal</th>
                                    <th class="text-right px-3" id="totalSub">0.00</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right px-3">Tax</th>
                                    <th class="text-right px-3" id="totalTax">0.00</th>
                                </tr>
                                <tr class="bg-dark text-white">
                                    <th colspan="4" class="text-right px-3">Net Amount</th>
                                    <th class="text-right px-3" id="totalNet">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-default btn-sm mr-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save mr-1"></i> Post
                            Invoice</button>
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

            function calculateTotals() {
                let totalSub = 0;
                let totalTax = 0;

                $('tbody tr').each(function() {
                    let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    let price = parseFloat($(this).find('.price-input').val()) || 0;
                    let taxRate = parseFloat($(this).find('.tax-select option:selected').data('rate')) || 0;

                    let sub = qty * price;
                    let tax = (sub * taxRate) / 100;

                    $(this).find('.line-subtotal').val(sub.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));

                    totalSub += sub;
                    totalTax += tax;
                });

                $('#totalSub').text(totalSub.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));
                $('#totalTax').text(totalTax.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));
                $('#totalNet').text((totalSub + totalTax).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));
            }

            $('.price-input, .tax-select').change(calculateTotals);
            calculateTotals();
        });
    </script>
@endpush
