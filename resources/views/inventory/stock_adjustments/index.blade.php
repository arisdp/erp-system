@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Stock Adjustments</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Adjustment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Adjustment #</th>
                                <th>Date</th>
                                <th>Warehouse</th>
                                <th>Reason</th>
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
                ajax: "{{ route('stock-adjustments.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'adjustment_number',
                        name: 'adjustment_number'
                    },
                    {
                        data: 'adjustment_date',
                        name: 'adjustment_date'
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse.name'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
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
