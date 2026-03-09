@extends('layouts.app')

@section('title', 'Stock Card: ' . $product->name)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-success card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">{{ $product->name }}</h3>
                    <p class="text-muted text-center">{{ $product->code ?? '-' }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Warehouse</b> <a class="float-right">{{ $warehouse->name }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Current Balance</b> <a class="float-right text-success"
                                style="font-size: 1.2rem; font-weight: bold;">{{ number_format($currentStock->quantity ?? 0, 2) }}
                                {{ $product->unit->name ?? '' }}</a>
                        </li>
                    </ul>

                    <a href="{{ route('stock-cards.index') }}" class="btn btn-default btn-block"><b>Back to Balances</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Transaction
                                Ledger</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="activity">
                            <table id="txTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Reference Doc</th>
                                        <th>Notes</th>
                                        <th class="text-right">Qty</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#txTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock-cards.show', ['product_id' => $product->id, 'warehouse_id' => $warehouse->id]) }}",
                columns: [{
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'reference_doc',
                        name: 'reference_doc',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        className: 'text-right'
                    }
                ],
                order: [
                    [0, 'desc']
                ] // Show latest first by default
            });
        });
    </script>
@endsection
