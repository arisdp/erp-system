@extends('layouts.app')

@section('title', 'New Stock Adjustment')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Create Stock Adjustment ({{ $nextNumber }})</h3>
                </div>
                <form action="{{ route('stock-adjustments.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Warehouse</label>
                                    <select name="warehouse_id"
                                        class="form-control select2 @error('warehouse_id') is-invalid @enderror" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}"
                                                {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="adjustment_date"
                                        class="form-control @error('adjustment_date') is-invalid @enderror"
                                        value="{{ old('adjustment_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Reason / Note</label>
                                    <input type="text" name="reason"
                                        class="form-control @error('reason') is-invalid @enderror"
                                        value="{{ old('reason') }}" required
                                        placeholder="e.g. Broken glass, Stock Opname Jan 2026">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Items</h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>System Qty</th>
                                    <th>Actual Qty</th>
                                    <th>Diff</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <!-- In a real app, this would be an ajax select2 to fetch product and its current sys qty -->
                                        <select name="lines[0][product_id]" class="form-control select2" required>
                                            <option value="">Select Product...</option>
                                            @foreach (\App\Models\Product::with('unit')->get() as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->name }}
                                                    ({{ $prod->unit->name ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="lines[0][system_quantity]"
                                            class="form-control sys-qty" required placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="lines[0][actual_quantity]"
                                            class="form-control act-qty" required placeholder="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control diff-qty" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-secondary btn-sm" id="add-item"><i
                                                class="fas fa-plus"></i> Add Item</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save as Draft</button>
                        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            let itemIndex = 1;
            $('#add-item').click(function() {
                let row = `
                <tr>
                    <td>
                        <select name="lines[${itemIndex}][product_id]" class="form-control select2" required>
                            <option value="">Select Product...</option>
                            @foreach (\App\Models\Product::with('unit')->get() as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->unit->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="lines[${itemIndex}][system_quantity]" class="form-control sys-qty" required placeholder="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="lines[${itemIndex}][actual_quantity]" class="form-control act-qty" required placeholder="0">
                    </td>
                    <td>
                        <input type="text" class="form-control diff-qty" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
                $('#items-table tbody').append(row);
                $('.select2').select2({
                    theme: 'bootstrap4'
                });
                itemIndex++;
            });

            $(document).on('click', '.remove-item', function() {
                if ($('#items-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });

            $(document).on('input', '.sys-qty, .act-qty', function() {
                let row = $(this).closest('tr');
                let sys = parseFloat(row.find('.sys-qty').val()) || 0;
                let act = parseFloat(row.find('.act-qty').val()) || 0;
                let diff = act - sys;
                row.find('.diff-qty').val(diff.toFixed(2));
            });
        });
    </script>
@endsection
