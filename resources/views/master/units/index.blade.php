@extends('layouts.app')

@section('page-title', 'Units of Measure')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-balance-scale mr-2"></i> Units of Measure</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Unit</button>
        </div>
        <div class="card-body">
            <table id="unitTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Name</th>
                        <th>Symbol</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="unitModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="unitForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="unit_id">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Unit Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Unit Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="e.g. Pieces, Kilograms" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Symbol</label>
                            <div class="col-sm-8">
                                <input type="text" name="symbol" id="symbol" class="form-control"
                                    placeholder="e.g. pcs, kg">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Save Unit</button>
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
            table = $('#unitTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('units.index') }}",
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'symbol'
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
                $('#unitModal').modal('show');
            });

            $('#unitForm').submit(function(e) {
                e.preventDefault();
                let id = $('#unit_id').val();
                let url = id ? "/master/units/" + id : "{{ route('units.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#unitModal').modal('hide');
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
                $.get("/master/units/" + id + "/edit", function(res) {
                    $('#unit_id').val(res.id);
                    $('#name').val(res.name);
                    $('#symbol').val(res.symbol);
                    $('#unitModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this unit?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/units/" + id,
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
                $('#unit_id').val('');
                $('#unitForm')[0].reset();
            }
        });
    </script>
@endpush
