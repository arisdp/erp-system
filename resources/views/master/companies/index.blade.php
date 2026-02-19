@extends('layouts.app')

@section('page-title', 'Company Management')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">
        Company
    </li>
</ol>
@endsection

@section('content')


<div class="card">
    <div class="card-header">
        <h3 class="card-title">Company List</h3>
        <button class="btn btn-primary mb-3" id="btnAdd">
            <i class="fas fa-plus"></i> Add Company
        </button>
    </div>

    <div class="card-body">
        <table id="companyTable"
            class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="companyModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="companyForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Company</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                            class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">
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

    $(function() {

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
                    searchable: false
                }
            ]
        });

    });

    function createCompany() {
        $.get("{{ route('companies.create') }}", function(res) {
            $('#companyFormContainer').html(res);
            $('#companyModal').modal('show');
        });
    }

    function editCompany(id) {
        $.get("/companies/" + id + "/edit", function(res) {
            $('#companyFormContainer').html(res);
            $('#companyModal').modal('show');
        });
    }

    $(document).ready(function() {

        $('#btnAdd').click(function() {
            $('#companyModal').modal('show');
        });

        $('#companyForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('companies.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert("Validation error!");
                }
            });
        });

    });
</script>
@endpush