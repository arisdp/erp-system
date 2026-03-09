@extends('layouts.app')

@section('title', 'Hardware & Product Stocks')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Real-time Warehouse Balances</h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Product Name</th>
                                <th>Unit</th>
                                <th>Current Balance</th>
                                <th>History</th>
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
                ajax: "{{ route('stock-cards.index') }}",
                columns: [{
                        data: 'warehouse_name',
                        name: 'warehouse.name'
                    },
                    {
                        data: 'product_name',
                        name: 'product.name'
                    },
                    {
                        data: 'unit_name',
                        name: 'product.unit.name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        className: 'text-right font-weight-bold'
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
@endsection
