@extends('layouts.app')

@section('page-title', 'Goods Receipt Notes (GRN)')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-truck-loading mr-2"></i> Goods Receipt Notes</h3>
            <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> New Goods Receipt
            </a>
        </div>
        <div class="card-body">
            <table id="grnTable" class="table table-bordered table-hover table-striped w-100 font-weight-light">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>GRN Number</th>
                        <th>PO Number</th>
                        <th>Warehouse</th>
                        <th>Received Date</th>
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
            $('#grnTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('goods-receipts.index') }}",
                columns: [{
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse_name'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
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
                ]
            });
        });
    </script>
@endpush
