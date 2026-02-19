@extends('layouts.app')

@section('page-title', 'Company Management')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Company</li>
</ol>
@endsection

@section('content')

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-building mr-2"></i> Company List
        </h3>

        <button class="btn btn-primary btn-sm" id="btnAdd">
            <i class="fas fa-plus mr-1"></i> Add Company
        </button>
    </div>

    <div class="card-body">
        <table id="companyTable"
            class="table table-bordered table-hover table-striped w-100">
            <thead class="thead-dark">
                <tr>
                    <th width="50">ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th width="130" class="text-center">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="companyModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="companyForm">
                @csrf
                <input type="hidden" name="id" id="company_id">

                <div class="modal-header">
                    <h5 class="modal-title">Company Form</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code"
                            id="code"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name"
                            id="name"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                            id="email"
                            class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-success">
                        Save
                    </button>
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
        timer: 2500,
        timerProgressBar: true,
    });

    $(document).ready(function() {

        table = $('#companyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('companies.index') }}",
            columns: [{
                    data: 'id'
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
            $('#companyModal').modal('show');
        });

        $('#companyForm').submit(function(e) {
            e.preventDefault();

            let id = $('#company_id').val();
            let url = id ?
                "/companies/" + id :
                "{{ route('companies.store') }}";

            let method = id ? "PUT" : "POST";

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(res) {

                    $('#companyModal').modal('hide');
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
                        title: 'Validation error'
                    });
                }
            });
        });

        $(document).on('click', '.btn-edit', function() {

            let id = $(this).data('id');

            $.get("/companies/" + id + "/edit", function(res) {

                $('#company_id').val(res.id);
                $('#code').val(res.code);
                $('#name').val(res.name);
                $('#email').val(res.email);

                $('#companyModal').modal('show');
            });

        });

        $(document).on('click', '.btn-delete', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: 'Delete this company?',
                text: "This action cannot be undone",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "/companies/" + id,
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
            $('#company_id').val('');
            $('#companyForm')[0].reset();
        }

    });
</script>
@endpush