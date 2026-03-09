@extends('layouts.app')

@section('title', 'Stock Transfers')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Stock Transfers (Warehouse Mutations)</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-transfers.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Transfer
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Transfer #</th>
                                <th>Date</th>
                                <th>Source Warehouse</th>
                                <th>Destination Warehouse</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock-transfers.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'transfer_number',
                        name: 'transfer_number'
                    },
                    {
                        data: 'transfer_date',
                        name: 'transfer_date'
                    },
                    {
                        data: 'from_warehouse_name',
                        name: 'fromWarehouse.name'
                    },
                    {
                        data: 'to_warehouse_name',
                        name: 'toWarehouse.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
        });
    </script>
@endsection
