@extends('layouts.app')

@section('page-title', 'Branch Management')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Branch</li>
    </ol>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-code-branch mr-2"></i> Branch List</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Branch</button>
        </div>
        <div class="card-body">
            <table id="branchTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Company</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="branchModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="branchForm">
                    @csrf
                    <input type="hidden" name="id" id="branch_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Branch Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Company</label>
                            <select name="company_id" id="company_id" class="form-control" required>
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="code" id="code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
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
            table = $('#branchTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('branches.index') }}",
                columns: [{
                        data: 'company_name'
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
                $('#branchModal').modal('show');
            });

            $('#branchForm').submit(function(e) {
                e.preventDefault();
                let id = $('#branch_id').val();
                let url = id ? "/master/branches/" + id : "{{ route('branches.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#branchModal').modal('hide');
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
                $.get("/master/branches/" + id + "/edit", function(res) {
                    $('#branch_id').val(res.id);
                    $('#company_id').val(res.company_id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#address').val(res.address);
                    $('#branchModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this branch?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/master/branches/" + id,
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
                $('#branch_id').val('');
                $('#branchForm')[0].reset();
            }
        });
    </script>
@endpush
