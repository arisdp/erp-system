@extends('layouts.app')

@section('page-title', 'Create Purchase Order')

@section('content')
    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i> PO Header: {{ $nextNumber }}</h3>
                    </div>
                    <div class="card-body text-sm font-weight-light">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" class="form-control form-control-sm select2" required>
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>PO Date <span class="text-danger">*</span></label>
                                    <input type="date" name="order_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Delivery Expected</label>
                                    <input type="date" name="expected_delivery_date"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Currency</label>
                                    <select name="currency_id" class="form-control form-control-sm">
                                        @foreach ($currencies as $curr)
                                            <option value="{{ $curr->id }}" {{ $curr->is_base ? 'selected' : '' }}>
                                                {{ $curr->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Term</label>
                                    <select name="payment_term_id" class="form-control form-control-sm">
                                        <option value="">Select Term</option>
                                        @foreach ($paymentTerms as $term)
                                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary py-2">
                        <h3 class="card-title text-sm"><i class="fas fa-list mr-2"></i> Order Lines</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0" id="linesTable">
                            <thead class="bg-light text-xs uppercase">
                                <tr>
                                    <th width="300">Product</th>
                                    <th>Description</th>
                                    <th width="120">Qty</th>
                                    <th width="150">Price</th>
                                    <th width="150">Tax</th>
                                    <th width="150">Subtotal</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="lineBody">
                                {{-- JS will render rows here --}}
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="5" class="text-right">Total (Excl. Tax)</td>
                                    <td class="text-right" id="footerTotal">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Tax Amount</td>
                                    <td class="text-right" id="footerTax">0.00</td>
                                    <td></td>
                                </tr>
                                <tr class="table-info">
                                    <td colspan="5" class="text-right uppercase">Net Amount</td>
                                    <td class="text-right" id="footerNet">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        <button type="button" class="btn btn-outline-primary btn-xs" id="btnAddLine"><i
                                class="fas fa-plus"></i> Add Line Item</button>
                        <div class="float-right">
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-default btn-sm mr-2">Cancel</a>
                            <button type="submit" class="btn btn-success btn-sm font-weight-bold"><i
                                    class="fas fa-save mr-1"></i> Save Purchase Order</button>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label class="text-xs">Notes / External Comments</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Instruction for supplier..."></textarea>
                </div>
            </div>
        </div>
    </form>

    {{-- Hidden Template for Row --}}
    <script type="text/template" id="rowTemplate">
    <tr class="po-line">
        <td>
            <select name="lines[{index}][product_id]" class="form-control form-control-sm select-product" required>
                <option value="">Search Product...</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}" data-tax="{{ $p->tax_rate_id }}" data-unit="{{ $p->unit->name }}">{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="lines[{index}][description]" class="form-control form-control-sm text-xs"></td>
        <td><input type="number" name="lines[{index}][quantity]" class="form-control form-control-sm text-right qty" value="1" step="0.000001" min="0"></td>
        <td><input type="number" name="lines[{index}][unit_price]" class="form-control form-control-sm text-right price" value="0" step="0.01" min="0"></td>
        <td>
            <select name="lines[{index}][tax_rate_id]" class="form-control form-control-sm select-tax">
                <option value="" data-rate="0">None</option>
                @foreach($taxRates as $tr)
                    <option value="{{ $tr->id }}" data-rate="{{ $tr->rate }}">{{ $tr->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="text-right pr-3 pt-2 subtotal">0.00</td>
        <td class="text-center"><button type="button" class="btn btn-link btn-xs btn-remove text-danger"><i class="fas fa-times"></i></button></td>
    </tr>
</script>
@endsection

@push('scripts')
    <script>
        let lineIndex = 0;
        const template = $('#rowTemplate').html();

        function addLine() {
            let row = template.replace(/{index}/g, lineIndex);
            $('#lineBody').append(row);
            lineIndex++;
            calculateAll();
        }

        function calculateAll() {
            let grandTotal = 0;
            let grandTax = 0;

            $('.po-line').each(function() {
                let q = parseFloat($(this).find('.qty').val()) || 0;
                let p = parseFloat($(this).find('.price').val()) || 0;
                let taxRate = parseFloat($(this).find('.select-tax option:selected').data('rate')) || 0;

                let sub = q * p;
                let tax = (sub * taxRate) / 100;

                $(this).find('.subtotal').text(sub.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                }));

                grandTotal += sub;
                grandTax += tax;
            });

            $('#footerTotal').text(grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('#footerTax').text(grandTax.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('#footerNet').text((grandTotal + grandTax).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
        }

        $(document).ready(function() {
            addLine(); // Start with one line

            $('#btnAddLine').click(addLine);

            $(document).on('click', '.btn-remove', function() {
                if ($('.po-line').length > 1) {
                    $(this).closest('tr').remove();
                    calculateAll();
                }
            });

            $(document).on('change', '.select-product', function() {
                let row = $(this).closest('tr');
                let opt = $(this).find('option:selected');
                if (opt.val()) {
                    row.find('.price').val(opt.data('price'));
                    row.find('.select-tax').val(opt.data('tax'));
                    calculateAll();
                }
            });

            $(document).on('input', '.qty, .price, .select-tax', calculateAll);
        });
    </script>
@endpush
