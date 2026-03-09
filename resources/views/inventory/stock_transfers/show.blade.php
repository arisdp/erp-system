@extends('layouts.app')

@section('title', 'Stock Transfer Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Transfer: {{ $stockTransfer->transfer_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <strong>From Warehouse:</strong><br>
                            {{ $stockTransfer->fromWarehouse->name ?? '-' }}
                        </div>
                        <div class="col-sm-3">
                            <strong>To Warehouse:</strong><br>
                            {{ $stockTransfer->toWarehouse->name ?? '-' }}
                        </div>
                        <div class="col-sm-3">
                            <strong>Transfer Date:</strong><br>
                            {{ $stockTransfer->transfer_date->format('d/m/Y') }}
                        </div>
                        <div class="col-sm-3">
                            <strong>Status:</strong><br>
                            <span
                                class="badge badge-{{ $stockTransfer->status === 'Completed' ? 'success' : ($stockTransfer->status === 'Draft' ? 'secondary' : 'danger') }}">
                                {{ $stockTransfer->status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <strong>Notes:</strong><br>
                            {{ $stockTransfer->notes ?? '-' }}
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th class="text-right">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockTransfer->lines as $line)
                                        <tr>
                                            <td>{{ $line->product->name }}</td>
                                            <td>{{ $line->product->unit->name ?? '-' }}</td>
                                            <td class="text-right">{{ number_format($line->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($stockTransfer->status === 'Draft')
                    <div class="card-footer">
                        <form action="{{ route('stock-transfers.update', $stockTransfer->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Confirm transfer? This will update warehouse stocks.')">
                                <i class="fas fa-check"></i> Approve & Complete Transfer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
