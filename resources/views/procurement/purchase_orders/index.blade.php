@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-shopping-cart mr-2"></i> Purchase Orders</h3>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> Create PO
            </a>
        </div>
        <div class="card-body">
            <table id="poTable" class="table table-bordered table-hover table-striped w-100 font-weight-light">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Currency</th>
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
            $('#poTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('purchase-orders.index') }}",
                columns: [{
                        data: 'po_number'
                    },
                    {
                        data: 'supplier.name'
                    },
                    {
                        data: 'order_date'
                    },
                    {
                        data: 'net_amount'
                    },
                    {
                        data: 'currency.code'
                    },
                    {
                        data: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endpush
