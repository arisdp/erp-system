@extends('layouts.app')

@section('page-title', 'Create Sales Order')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ route('sales-orders.store') }}" method="POST" id="soForm">
                @csrf
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i> SO Header Information
                        </h3>
                    </div>
                    <div class="card-body text-sm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">SO Number</label>
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="{{ $nextNumber }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Order Date <span class="text-danger">*</span></label>
                                    <input type="date" name="order_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Transaction Type</label>
                                    <select name="transaction_type" id="transactionType"
                                        class="form-control form-control-sm">
                                        <option value="Offline">Offline / Direct</option>
                                        <option value="Online">Online / Marketplace</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Customer <span class="text-danger">*</span></label>
                                    <select name="customer_id" class="form-control form-control-sm select2" required>
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="marketplaceGroup" style="display:none">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Marketplace <span
                                                class="text-danger">*</span></label>
                                        <select name="marketplace_id" class="form-control form-control-sm select2">
                                            <option value="">Select Marketplace</option>
                                            @foreach ($marketplaces as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold small">Plat. Fee</label>
                                                <input type="number" name="platform_fee"
                                                    class="form-control form-control-sm online-calc" step="0.01"
                                                    value="0">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold small">Discount</label>
                                                <input type="number" name="platform_discount"
                                                    class="form-control form-control-sm online-calc" step="0.01"
                                                    value="0">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold small">Voucher</label>
                                                <input type="number" name="platform_voucher"
                                                    class="form-control form-control-sm online-calc" step="0.01"
                                                    value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="1" placeholder="Optional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2"></i> Order Items</h3>
                        <button type="button" class="btn btn-primary btn-xs" onclick="addRow()">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0" id="linesTable">
                            <thead class="bg-light text-center text-xs uppercase">
                                <tr>
                                    <th width="40%">Product</th>
                                    <th width="10%">Unit</th>
                                    <th width="12%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Tax</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be added dynamically -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right px-3">Total Subtotal</th>
                                    <th class="text-right px-3" id="totalSub">0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right px-3">Total Tax</th>
                                    <th class="text-right px-3" id="totalTax">0.00</th>
                                    <th></th>
                                </tr>
                                <tr class="bg-dark text-white">
                                    <th colspan="4" class="text-right px-3">Net Amount</th>
                                    <th class="text-right px-3" id="totalNet">0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer text-right py-3">
                        <a href="{{ route('sales-orders.index') }}"
                            class="btn btn-default btn-sm mr-2 px-4 font-weight-bold border">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm px-5 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Save Sales Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Template Row --}}
    <table style="display:none">
        <tr id="rowTemplate">
            <td>
                <select name="lines[IDX][product_id]" class="form-control form-control-sm product-select" required
                    onchange="handleProductChange(this)">
                    <option value="">Select Product</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-unit-id="{{ $p->unit_id }}"
                            data-unit-name="{{ $p->unit->symbol }}" data-price="{{ $p->selling_price }}"
                            data-tax-id="{{ $p->tax_rate_id }}">
                            {{ $p->name }} ({{ $p->sku }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center align-middle">
                <input type="hidden" name="lines[IDX][unit_id]" class="unit-id">
                <span class="unit-name text-muted small text-uppercase font-weight-bold">-</span>
            </td>
            <td>
                <input type="number" name="lines[IDX][quantity]"
                    class="form-control form-control-sm text-right qty-input" step="0.000001" value="1" required
                    onchange="calculateTotals()">
            </td>
            <td>
                <input type="number" name="lines[IDX][unit_price]"
                    class="form-control form-control-sm text-right price-input" step="0.01" value="0" required
                    onchange="calculateTotals()">
            </td>
            <td>
                <select name="lines[IDX][tax_rate_id]" class="form-control form-control-sm tax-select"
                    onchange="calculateTotals()">
                    <option value="" data-rate="0">No Tax</option>
                    @foreach ($taxRates as $tr)
                        <option value="{{ $tr->id }}" data-rate="{{ $tr->rate }}">
                            {{ $tr->name }} ({{ number_format($tr->rate, 0) }}%)
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-outline-danger btn-xs border-0" onclick="removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </table>
@endsection

@push('scripts')
    <script>
        let rowIndex = 0;

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
            addRow(); // Add first row

            $('#transactionType').change(function() {
                if ($(this).val() === 'Online') {
                    $('#marketplaceGroup').slideDown();
                } else {
                    $('#marketplaceGroup').slideUp();
                }
                calculateTotals();
            });

            $('.online-calc').on('input', calculateTotals);
        });

        function addRow() {
            let template = $('#rowTemplate').html();
            template = template.replace(/IDX/g, rowIndex);
            $('#linesTable tbody').append('<tr>' + template + '</tr>');

            // Initialize select2 for new row
            $('#linesTable tbody tr:last .product-select').select2({
                theme: 'bootstrap4'
            });

            rowIndex++;
            calculateTotals();
        }

        function removeRow(btn) {
            if ($('#linesTable tbody tr').length > 1) {
                $(btn).closest('tr').remove();
                calculateTotals();
            } else {
                Swal.fire('Tip', 'At least one item is required.', 'info');
            }
        }

        function handleProductChange(select) {
            let option = $(select).find('option:selected');
            let row = $(select).closest('tr');

            if (option.val()) {
                row.find('.unit-id').val(option.data('unit-id'));
                row.find('.unit-name').text(option.data('unit-name'));
                row.find('.price-input').val(option.data('price'));
                row.find('.tax-select').val(option.data('tax-id'));
            } else {
                row.find('.unit-id').val('');
                row.find('.unit-name').text('-');
                row.find('.price-input').val(0);
                row.find('.tax-select').val('');
            }

            calculateTotals();
        }

        function calculateTotals() {
            let totalSub = 0;
            let totalTax = 0;

            $('#linesTable tbody tr').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val()) || 0;
                let taxRate = parseFloat($(this).find('.tax-select option:selected').data('rate')) || 0;

                let sub = qty * price;
                let tax = (sub * taxRate) / 100;

                totalSub += sub;
                totalTax += tax;
            });

            let platformFee = parseFloat($('input[name="platform_fee"]').val()) || 0;
            let platformDiscount = parseFloat($('input[name="platform_discount"]').val()) || 0;
            let platformVoucher = parseFloat($('input[name="platform_voucher"]').val()) || 0;

            let netAmount = (totalSub + totalTax) - platformFee - platformDiscount + platformVoucher;

            $('#totalSub').text(totalSub.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('#totalTax').text(totalTax.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('#totalNet').text(netAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
        }
    </script>
@endpush
