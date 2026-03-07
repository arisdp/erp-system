@extends('layouts.app')

@section('page-title', 'Warehouses Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-warehouse mr-2"></i> Warehouses</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add
                Warehouse</button>
        </div>
        <div class="card-body">
            <table id="warehouseTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="warehouseModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="warehouseForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="warehouse_id">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Warehouse Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Code</label>
                            <div class="col-sm-8">
                                <input type="text" name="code" id="code" class="form-control"
                                    placeholder="e.g. WH-01" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Warehouse Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="e.g. Main Warehouse" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Address</label>
                            <div class="col-sm-8">
                                <textarea name="address" id="address" class="form-control" placeholder="Warehouse address..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Save Warehouse</button>
                    </div>
                </form>
            </div>
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
            table = $('#warehouseTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('warehouses.index') }}",
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'address'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $('#btnAdd').click(function() {
                resetForm();
                $('#warehouseModal').modal('show');
            });

            $('#warehouseForm').submit(function(e) {
                e.preventDefault();
                let id = $('#warehouse_id').val();
                let url = id ? "/master/warehouses/" + id : "{{ route('warehouses.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#warehouseModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        resetForm();
                    },
                    error: function(err) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error processing request'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("/master/warehouses/" + id + "/edit", function(res) {
                    $('#warehouse_id').val(res.id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#address').val(res.address);
                    $('#warehouseModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this warehouse?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/warehouses/" + id,
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

            function resetForm() {
                $('#warehouse_id').val('');
                $('#warehouseForm')[0].reset();
            }
        });
    </script>
@endpush
