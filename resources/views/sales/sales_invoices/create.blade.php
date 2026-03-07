@extends('layouts.app')

@section('page-title', 'Create Sales Invoice')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ route('sales-invoices.store') }}" method="POST">
                @csrf
                @if ($sourceType == 'SO')
                    <input type="hidden" name="sales_order_id" value="{{ $sourceData->id }}">
                @elseif($sourceType == 'DO')
                    <input type="hidden" name="delivery_order_id" value="{{ $sourceData->id }}">
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark py-3">
                        <h3 class="card-title font-weight-bold mb-0">New Invoice
                            {{ $sourceType ? "(From $sourceType: " . ($sourceType == 'SO' ? $sourceData->so_number : $sourceData->do_number) . ')' : '' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Invoice Number</label>
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="{{ $nextNumber }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Invoice Date <span class="text-danger">*</span></label>
                                    <input type="date" name="invoice_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Due Date</label>
                                    <input type="date" name="due_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Customer <span class="text-danger">*</span></label>
                                    <select name="customer_id" class="form-control form-control-sm select2" required>
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ $sourceData && ($sourceData->customer_id ?? $sourceData->salesOrder->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Platform Fee (Marketplace)</label>
                                    <input type="number" name="platform_fee" class="form-control form-control-sm"
                                        step="0.01" value="{{ $sourceData->platform_fee ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Notes</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Additional info...">{{ $sourceData->notes ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="font-weight-bold text-muted uppercase mb-3 small">Invoice Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="itemsTable">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Unit</th>
                                        <th width="120">Qty</th>
                                        <th width="150">Price</th>
                                        <th width="120">Tax %</th>
                                        <th width="150">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($sourceData)
                                        @foreach ($sourceData->lines as $index => $line)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][product_id]"
                                                        value="{{ $line->product_id }}">
                                                    <strong>{{ $line->product->name }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <input type="hidden" name="items[{{ $index }}][unit_id]"
                                                        value="{{ $line->unit_id }}">
                                                    {{ $line->unit->symbol ?? '-' }}
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]"
                                                        class="form-control form-control-sm text-right qty-input"
                                                        value="{{ $line->quantity }}" step="0.0001" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][unit_price]"
                                                        class="form-control form-control-sm text-right price-input"
                                                        value="{{ $line->unit_price }}" step="0.01" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <input type="hidden" name="items[{{ $index }}][tax_rate_id]"
                                                        value="{{ $line->tax_rate_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][tax_rate]"
                                                        value="{{ $line->taxRate->rate ?? 0 }}">
                                                    {{ $line->taxRate->rate ?? 0 }}%
                                                </td>
                                                <td class="text-right font-weight-bold pt-2">
                                                    @php $lineTotal = ($line->quantity * $line->unit_price) * (1 + (($line->taxRate->rate ?? 0) / 100)); @endphp
                                                    Rp {{ number_format($lineTotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Please create invoice
                                                from Sales Order or Delivery Order for pre-filled data.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4 justify-content-end">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded border shadow-sm">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="font-weight-bold">Subtotal</td>
                                            <td class="text-right" id="dispSub">Rp
                                                {{ number_format($sourceData->total_amount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Tax</td>
                                            <td class="text-right" id="dispTax">Rp
                                                {{ number_format($sourceData->tax_amount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-danger">Plat. Fee (-)</td>
                                            <td class="text-right text-danger" id="dispFee">- Rp
                                                {{ number_format($sourceData->platform_fee ?? 0, 2) }}</td>
                                        </tr>
                                        <tr class="border-top border-dark">
                                            <td class="font-weight-bold text-lg">Net Amount</td>
                                            <td class="text-right font-weight-bold text-lg text-primary" id="dispNet">Rp
                                                {{ number_format($sourceData->net_amount ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top text-right py-3">
                        <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary btn-sm px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm ml-2">Save Invoice</button>
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
