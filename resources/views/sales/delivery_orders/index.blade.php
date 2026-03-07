@extends('layouts.app')

@section('page-title', 'Delivery Orders (DO)')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h3 class="card-title font-weight-bold mb-0 text-dark"><i class="fas fa-truck mr-2 text-success"></i> Delivery
                Orders</h3>
            <a href="{{ route('delivery-orders.create') }}" class="btn btn-success btn-sm ml-auto font-weight-bold">
                <i class="fas fa-plus mr-1"></i> New Delivery
            </a>
        </div>
        <div class="card-body">
            <table id="doTable" class="table table-hover table-striped w-100 text-sm">
                <thead class="bg-light">
                    <tr>
                        <th>DO Number</th>
                        <th>SO Number</th>
                        <th>Customer</th>
                        <th>Warehouse</th>
                        <th>Delivery Date</th>
                        <th class="text-center">Status</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#doTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('delivery-orders.index') }}",
                columns: [{
                        data: 'do_number',
                        name: 'do_number',
                        className: 'font-weight-bold'
                    },
                    {
                        data: 'so_number',
                        name: 'so_number'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse_name'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [4, 'desc']
                ]
            });
        });
    </script>
@endpush
