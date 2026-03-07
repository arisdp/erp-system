@extends('layouts.app')

@section('page-title', 'Purchase Invoices (AP)')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i> Purchase Invoices</h3>
            <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> New Invoice
            </a>
        </div>
        <div class="card-body">
            <table id="invoiceTable" class="table table-bordered table-hover table-striped w-100 font-weight-light text-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Supplier</th>
                        <th>PO Ref</th>
                        <th>Date</th>
                        <th class="text-right">Net Amount</th>
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
            $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchase-invoices.index') }}",
                columns: [{
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'purchase_order.po_number',
                        name: 'purchase_order.po_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        className: 'text-right'
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
