@extends('layouts.app')

@section('page-title', 'Products Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-box mr-2"></i> Products</h3>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> Add Product
            </a>
        </div>
        <div class="card-body">
            <table id="productTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table;
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500
        });

        $(document).ready(function() {
            table = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('products.index') }}",
                columns: [{
                        data: 'sku'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: 'unit_name'
                    },
                    {
                        data: 'selling_price',
                        className: 'text-right'
                    },
                    {
                        data: 'is_active',
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

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this product?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/products/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                table.ajax.reload(null, false);
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
