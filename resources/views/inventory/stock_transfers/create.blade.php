@extends('layouts.app')

@section('title', 'New Stock Transfer')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Create Stock Transfer ({{ $nextNumber }})</h3>
                </div>
                <form action="{{ route('stock-transfers.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="transfer_date"
                                        class="form-control @error('transfer_date') is-invalid @enderror"
                                        value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Source (From) Warehouse</label>
                                    <select name="from_warehouse_id"
                                        class="form-control select2 @error('from_warehouse_id') is-invalid @enderror"
                                        required>
                                        <option value="">Select Source Warehouse</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}"
                                                {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                {{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Destination (To) Warehouse</label>
                                    <select name="to_warehouse_id"
                                        class="form-control select2 @error('to_warehouse_id') is-invalid @enderror"
                                        required>
                                        <option value="">Select Destination Warehouse</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}"
                                                {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                {{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <input type="text" name="notes"
                                        class="form-control @error('notes') is-invalid @enderror"
                                        value="{{ old('notes') }}" placeholder="e.g. Restocking retail store">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Items to Transfer</h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="lines[0][product_id]" class="form-control select2" required>
                                            <option value="">Select Product...</option>
                                            @foreach (\App\Models\Product::with('unit')->get() as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->name }}
                                                    ({{ $prod->unit->name ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="lines[0][quantity]" class="form-control"
                                            required placeholder="Qty to move">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button type="button" class="btn btn-secondary btn-sm" id="add-item"><i
                                                class="fas fa-plus"></i> Add Item</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save as Draft</button>
                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-default float-right">Cancel</a>
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
                        <input type="number" step="0.01" name="lines[${itemIndex}][quantity]" class="form-control" required placeholder="Qty to move">
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
        });
    </script>
@endsection
