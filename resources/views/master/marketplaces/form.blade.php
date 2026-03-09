@extends('layouts.app')

@section('page-title', isset($marketplace) ? 'Edit Marketplace' : 'Add Marketplace')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <form
                    action="{{ isset($marketplace) ? route('marketplaces.update', $marketplace->id) : route('marketplaces.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($marketplace))
                        @method('PUT')
                    @endif
                    <div class="card-header">
                        <h3 class="card-title">Marketplace Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Marketplace Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name', $marketplace->name ?? '') }}" required
                                placeholder="e.g. Shopee, Tokopedia">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active"
                                    {{ old('is_active', $marketplace->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Is Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Marketplace</button>
                        <a href="{{ route('marketplaces.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
