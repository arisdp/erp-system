@extends('layouts.app')

@section('page-title', 'Role Management')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Role</li>
    </ol>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-user-shield mr-2"></i> Role List</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Role</button>
        </div>
        <div class="card-body">
            <table id="roleTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Company</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="roleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="roleForm">
                    @csrf
                    <input type="hidden" name="id" id="role_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Role Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Company</label>
                            <select name="company_id" id="company_id" class="form-control">
                                <option value="">Global / System</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Role Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Guard Name</label>
                            <input type="text" name="guard_name" id="guard_name" class="form-control" value="web"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
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
            table = $('#roleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('roles.index') }}",
                columns: [{
                        data: 'company_name'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'guard_name'
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
                $('#roleModal').modal('show');
            });

            $('#roleForm').submit(function(e) {
                e.preventDefault();
                let id = $('#role_id').val();
                let url = id ? "/master/roles/" + id : "{{ route('roles.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#roleModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        resetForm();
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error processing request'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("/master/roles/" + id + "/edit", function(res) {
                    $('#role_id').val(res.id);
                    $('#company_id').val(res.company_id);
                    $('#name').val(res.name);
                    $('#guard_name').val(res.guard_name);
                    $('#roleModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this role?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/master/roles/" + id,
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
                $('#role_id').val('');
                $('#roleForm')[0].reset();
            }
        });
    </script>
@endpush
