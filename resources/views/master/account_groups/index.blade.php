@extends('layouts.app')

@section('page-title', 'Account Groups')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-layer-group mr-2"></i> Account Groups</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Group</button>
        </div>
        <div class="card-body">
            <table id="groupTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Group Name</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="groupModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="groupForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="group_id">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Account Group Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Type</label>
                            <div class="col-sm-8">
                                <select name="account_type_id" id="account_type_id" class="form-control" required>
                                    <option value="">Select Type</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Code</label>
                            <div class="col-sm-8">
                                <input type="text" name="code" id="code" class="form-control" required
                                    placeholder="e.g. 1100">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Group Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" id="name" class="form-control" required
                                    placeholder="e.g. Current Assets">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Save Group</button>
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
            table = $('#groupTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('account-groups.index') }}",
                columns: [{
                        data: 'type_name'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'name'
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
                $('#groupModal').modal('show');
            });

            $('#groupForm').submit(function(e) {
                e.preventDefault();
                let id = $('#group_id').val();
                let url = id ? "/master/account-groups/" + id : "{{ route('account-groups.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#groupModal').modal('hide');
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
                $.get("/master/account-groups/" + id + "/edit", function(res) {
                    $('#group_id').val(res.id);
                    $('#account_type_id').val(res.account_type_id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#groupModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this group?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/account-groups/" + id,
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
                $('#group_id').val('');
                $('#groupForm')[0].reset();
            }
        });
    </script>
@endpush
