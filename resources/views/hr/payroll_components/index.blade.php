@extends('layouts.app')

@section('title', 'Payroll Components')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Salary Components Configuration</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#componentModal"
                            id="addNewBtn">
                            <i class="fas fa-plus"></i> New Component
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Use names like <strong>"Makan"</strong> or
                        <strong>"Lembur"</strong> to enable automatic daily/hourly calculations.
                    </div>
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Component Name</th>
                                <th>Type</th>
                                <th>Calculation</th>
                                <th>Default Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="componentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="componentForm">
                    @csrf
                    <input type="hidden" name="id" id="componentId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">New Component</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Component Name</label>
                            <input type="text" name="name" id="name" class="form-control" required
                                placeholder="e.g. Uang Makan Harian">
                        </div>
                        <div class="form-group">
                            <label>Factor Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="Allowance">Allowance (Penambah Gaji)</option>
                                <option value="Deduction">Deduction (Pemotong Gaji)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Calculation Method</label>
                            <select name="calculation_type" id="calculation_type" class="form-control" required>
                                <option value="Fixed">Fixed (Flat Amount per Month)</option>
                                <option value="Daily">Daily (Count based on Presence Days)</option>
                                <option value="Per Hour">Hourly (Count based on Overtime Hours)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Default Amount (Rp)</label>
                            <input type="number" name="default_amount" id="default_amount" class="form-control" required
                                value="0" step="0.01">
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
                ajax: "{{ route('payroll-components.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        className: 'text-center'
                    },
                    {
                        data: 'calculation_type',
                        name: 'calculation_type',
                        className: 'text-center'
                    },
                    {
                        data: 'default_amount',
                        name: 'default_amount',
                        className: 'text-right'
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
                $('#componentForm').trigger("reset");
                $('#modalTitle').html("New Component");
                $('#componentId').val('');
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('payroll-components.index') }}/" + id, function(data) {
                    $('#modalTitle').html("Edit Component");
                    $('#componentId').val(data.id);
                    $('#name').val(data.name);
                    $('#type').val(data.type);
                    $('#calculation_type').val(data.calculation_type);
                    $('#default_amount').val(data.default_amount);
                    $('#componentModal').modal('show');
                });
            });

            $('#componentForm').submit(function(e) {
                e.preventDefault();
                let id = $('#componentId').val();
                let url = id ? "{{ route('payroll-components.index') }}/" + id :
                    "{{ route('payroll-components.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        table.ajax.reload();
                        $('#componentModal').modal('hide');
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
                    text: "Removing this will affect payroll calculations!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('payroll-components.index') }}/" + id,
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
