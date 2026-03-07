@extends('layouts.app')

@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h3 class="card-title text-dark font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Product
                            Information</h3>
                    </div>
                    <div class="card-body text-sm">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label>SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
                                    <input type="text" name="sku"
                                        class="form-control form-control-sm @error('sku') is-invalid @enderror"
                                        value="{{ old('sku', $product->sku) }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control form-control-sm @error('name') is-invalid @enderror"
                                        value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category_id" class="form-control form-control-sm">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Base Unit <span class="text-danger">*</span></label>
                                            <select name="unit_id" class="form-control form-control-sm" required>
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Default Tax Rate</label>
                                    <select name="tax_rate_id" class="form-control form-control-sm">
                                        <option value="">No Tax / Exempt</option>
                                        @foreach ($taxRates as $tax)
                                            <option value="{{ $tax->id }}"
                                                {{ $product->tax_rate_id == $tax->id ? 'selected' : '' }}>
                                                {{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Purchase Price</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="purchase_price" class="form-control"
                                                    value="{{ old('purchase_price', $product->purchase_price) }}"
                                                    step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Selling Price <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="selling_price" class="form-control"
                                                    value="{{ old('selling_price', $product->selling_price) }}"
                                                    step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="4">{{ old('description', $product->description) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" class="custom-control-input"
                                            id="editActiveSwitch" value="1"
                                            {{ $product->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="editActiveSwitch">Active for
                                            transactions</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top text-right">
                        <a href="{{ route('products.index') }}" class="btn btn-default btn-sm mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Update
                            Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
