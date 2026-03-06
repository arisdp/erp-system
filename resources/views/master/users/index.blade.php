@extends('layouts.app')

@section('page-title', 'User Management')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">User</li>
    </ol>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i> User List</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-user-plus mr-1"></i> Add
                User</button>
        </div>
        <div class="card-body">
            <table id="userTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="userModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="userForm">
                    @csrf
                    <input type="hidden" name="id" id="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title">User Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Leave blank to keep current">
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <select name="company_id" id="company_id" class="form-control">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
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
            table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'company_name'
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
                $('#userModal').modal('show');
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();
                let id = $('#user_id').val();
                let url = id ? "/master/users/" + id : "{{ route('users.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#userModal').modal('hide');
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
                $.get("/master/users/" + id + "/edit", function(res) {
                    $('#user_id').val(res.id);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#company_id').val(res.company_id);
                    $('#password').val('');
                    $('#userModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/master/users/" + id,
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
                $('#user_id').val('');
                $('#userForm')[0].reset();
            }
        });
    </script>
@endpush
