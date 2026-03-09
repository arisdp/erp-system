@extends('layouts.app')

@section('title', 'Stock Adjustment Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Adjustment: {{ $stockAdjustment->adjustment_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Warehouse:</strong><br>
                            {{ $stockAdjustment->warehouse->name ?? '-' }}
                        </div>
                        <div class="col-sm-4">
                            <strong>Adjustment Date:</strong><br>
                            {{ $stockAdjustment->adjustment_date->format('d/m/Y') }}
                        </div>
                        <div class="col-sm-4">
                            <strong>Status:</strong><br>
                            <span
                                class="badge badge-{{ $stockAdjustment->status === 'Approved' ? 'success' : ($stockAdjustment->status === 'Draft' ? 'secondary' : 'danger') }}">
                                {{ $stockAdjustment->status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <strong>Reason:</strong><br>
                            {{ $stockAdjustment->reason }}
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th class="text-right">System Qty</th>
                                        <th class="text-right">Actual Qty</th>
                                        <th class="text-right">Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockAdjustment->lines as $line)
                                        <tr>
                                            <td>{{ $line->product->name }}</td>
                                            <td>{{ $line->product->unit->name ?? '-' }}</td>
                                            <td class="text-right">{{ number_format($line->system_quantity, 2) }}</td>
                                            <td class="text-right">{{ number_format($line->actual_quantity, 2) }}</td>
                                            <td
                                                class="text-right {{ $line->difference > 0 ? 'text-success' : ($line->difference < 0 ? 'text-danger' : '') }}">
                                                {{ $line->difference > 0 ? '+' : '' }}{{ number_format($line->difference, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($stockAdjustment->status === 'Draft')
                    <div class="card-footer">
                        <form action="{{ route('stock-adjustments.update', $stockAdjustment->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('WARNING: Approving this will update real inventory. Continue?')">
                                <i class="fas fa-check"></i> Approve & Apply
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
