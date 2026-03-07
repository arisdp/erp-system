@extends('layouts.app')

@section('page-title', 'Sales Invoices (AR)')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h3 class="card-title font-weight-bold mb-0 text-dark"><i
                    class="fas fa-file-invoice-dollar mr-2 text-primary"></i>
                Sales Invoices</h3>
            <a href="{{ route('sales-invoices.create') }}" class="btn btn-primary btn-sm ml-auto font-weight-bold">
                <i class="fas fa-plus mr-1"></i> New Invoice
            </a>
        </div>
        <div class="card-body">
            <table id="siTable" class="table table-hover table-striped w-100 text-sm">
                <thead class="bg-light">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
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
            $('#siTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sales-invoices.index') }}",
                columns: [{
                        data: 'invoice_number',
                        name: 'invoice_number',
                        className: 'font-weight-bold'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        className: 'text-right font-weight-bold'
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
                    [2, 'desc']
                ]
            });
        });
    </script>
@endpush
