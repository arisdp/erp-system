@extends('layouts.app')

@section('title', 'Attendance & Overtime')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Attendance Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#attendanceModal"
                            id="addNewBtn">
                            <i class="fas fa-plus"></i> Record Attendance
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Overtime?</th>
                                <th>OT Hours</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="attendanceForm">
                    @csrf
                    <input type="hidden" name="id" id="attendanceId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Record Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control select2" required
                                style="width: 100%;">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Check In</label>
                                    <input type="time" name="check_in" id="check_in" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Check Out</label>
                                    <input type="time" name="check_out" id="check_out" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Leave">Leave</option>
                                <option value="Sick">Sick</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_overtime" class="custom-control-input" id="isOvertimeSwitch"
                                    value="1">
                                <label class="custom-control-label" for="isOvertimeSwitch">Is Overtime?</label>
                            </div>
                        </div>
                        <div id="overtimeSection" style="display: none;">
                            <div class="form-group">
                                <label>Overtime Hours</label>
                                <input type="number" name="overtime_hours" id="overtime_hours" class="form-control"
                                    value="0" step="0.5">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendances.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee.full_name'
                    },
                    {
                        data: 'check_in',
                        name: 'check_in'
                    },
                    {
                        data: 'check_out',
                        name: 'check_out'
                    },
                    {
                        data: 'is_overtime',
                        name: 'is_overtime',
                        className: 'text-center'
                    },
                    {
                        data: 'overtime_hours',
                        name: 'overtime_hours',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status'
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

            $('#isOvertimeSwitch').change(function() {
                if ($(this).is(':checked')) {
                    $('#overtimeSection').fadeIn();
                } else {
                    $('#overtimeSection').fadeOut();
                }
            });

            $('#addNewBtn').click(function() {
                $('#attendanceForm').trigger("reset");
                $('#employee_id').val('').trigger('change');
                $('#attendanceId').val('');
                $('#overtimeSection').hide();
            });

            $('#attendanceForm').submit(function(e) {
                e.preventDefault();
                let id = $('#attendanceId').val();
                let url = id ? "{{ route('attendances.index') }}/" + id :
                "{{ route('attendances.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#attendanceModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong',
                            'error');
                    }
                });
            });
        });
    </script>
@endsection
