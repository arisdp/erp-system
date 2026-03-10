@extends('layouts.app')

@section('page-title', 'Create New Product')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-plus mr-2 text-primary"></i> Product Information</h3>
                    </div>
                    <div class="card-body text-sm bg-light rounded-bottom">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label>SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
                                    <input type="text" name="sku"
                                        class="form-control form-control-sm @error('sku') is-invalid @enderror"
                                        value="{{ old('sku') }}" placeholder="e.g. PROD-001" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control form-control-sm @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="e.g. Laptop ASUS" required>
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
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}
                                                        ({{ $unit->symbol }})
                                                    </option>
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
                                            <option value="{{ $tax->id }}">{{ $tax->name }}
                                                ({{ number_format($tax->rate, 2) }}%)</option>
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
                                                    value="{{ old('purchase_price', 0) }}" step="0.01">
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
                                                    value="{{ old('selling_price', 0) }}" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="4"
                                        placeholder="Brief product description..."></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" class="custom-control-input"
                                            id="activeSwitch" value="1" checked>
                                        <label class="custom-control-label" for="activeSwitch">Active for
                                            transactions</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top text-right">
                        <a href="{{ route('products.index') }}" class="btn btn-default btn-sm mr-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save mr-1"></i> Save
                            Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
