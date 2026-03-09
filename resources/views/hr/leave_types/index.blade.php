@extends('layouts.app')

@section('title', 'Leave Types')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Leave Type Configuration</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#typeModal"
                            id="addNewBtn">
                            <i class="fas fa-plus"></i> New Leave Type
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Paid Leave?</th>
                                <th>Default Quota (Days)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="typeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="typeForm">
                    @csrf
                    <input type="hidden" name="id" id="typeId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">New Leave Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Leave Name</label>
                            <input type="text" name="name" id="name" class="form-control" required
                                placeholder="e.g. Annual Leave">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="is_paid" id="is_paid" class="form-control" required>
                                <option value="1">Paid (Cuti Dibayar)</option>
                                <option value="0">Unpaid (Cuti Tidak Dibayar / Potong Gaji)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Default Yearly Quota</label>
                            <input type="number" name="default_quota" id="default_quota" class="form-control" required
                                value="12">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('leave-types.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'is_paid',
                        name: 'is_paid',
                        className: 'text-center'
                    },
                    {
                        data: 'default_quota',
                        name: 'default_quota',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $('#addNewBtn').click(function() {
                $('#typeForm').trigger("reset");
                $('#modalTitle').html("New Leave Type");
                $('#typeId').val('');
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('leave-types.index') }}/" + id, function(data) {
                    $('#modalTitle').html("Edit Leave Type");
                    $('#typeId').val(data.id);
                    $('#name').val(data.name);
                    $('#is_paid').val(data.is_paid ? '1' : '0');
                    $('#default_quota').val(data.default_quota);
                    $('#typeModal').modal('show');
                });
            });

            $('#typeForm').submit(function(e) {
                e.preventDefault();
                let id = $('#typeId').val();
                let url = id ? "{{ route('leave-types.index') }}/" + id :
                "{{ route('leave-types.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#typeModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong',
                            'error');
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete this leave type?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('leave-types.index') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
