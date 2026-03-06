@extends('layouts.app')

@section('page-title', 'Chart of Accounts')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">COA</li>
    </ol>
@endsection

@section('content')
    <div class="card shadow-sm text-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-sitemap mr-2"></i> Chart of Accounts</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add
                Account</button>
        </div>
        <div class="card-body">
            <table id="accountTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Parent</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="accountModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="accountForm">
                    @csrf
                    <input type="hidden" name="id" id="account_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Account Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Account Type</label>
                            <select name="account_type_id" id="account_type_id" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->normal_balance }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Parent Account</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">Root (No Parent)</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->account_code }} -
                                        {{ $parent->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Account Code</label>
                            <input type="text" name="account_code" id="account_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" name="account_name" id="account_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_postable" class="custom-control-input" id="is_postable"
                                    value="1" checked>
                                <label class="custom-control-label" for="is_postable">Can Post Transaction?</label>
                            </div>
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
            table = $('#accountTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('accounts.index') }}",
                columns: [{
                        data: 'type_name'
                    },
                    {
                        data: 'account_code'
                    },
                    {
                        data: 'account_name'
                    },
                    {
                        data: 'parent_name'
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
                $('#accountModal').modal('show');
            });

            $('#accountForm').submit(function(e) {
                e.preventDefault();
                let id = $('#account_id').val();
                let url = id ? "/master/accounts/" + id : "{{ route('accounts.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#accountModal').modal('hide');
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
                $.get("/master/accounts/" + id + "/edit", function(res) {
                    $('#account_id').val(res.id);
                    $('#account_type_id').val(res.account_type_id);
                    $('#parent_id').val(res.parent_id);
                    $('#account_code').val(res.account_code);
                    $('#account_name').val(res.account_name);
                    $('#is_postable').prop('checked', res.is_postable);
                    $('#accountModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this account?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/master/accounts/" + id,
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
                $('#account_id').val('');
                $('#accountForm')[0].reset();
            }
        });
    </script>
@endpush
