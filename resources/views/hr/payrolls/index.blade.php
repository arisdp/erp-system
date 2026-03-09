@extends('layouts.app')

@section('title', 'Monthly Payroll')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title">Salary Processing Period</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#generateModal">
                            <i class="fas fa-sync"></i> Generate Monthly Payroll
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Employee</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="generateForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Payroll</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Select period to calculate salary for all active employees.</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Month</label>
                                    <select name="month" class="form-control" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Year</label>
                                    <input type="number" name="year" class="form-control" value="{{ date('Y') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Run Calculation</button>
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
                ajax: "{{ route('payrolls.index') }}",
                columns: [{
                        data: 'period',
                        name: 'period_month'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee.full_name'
                    },
                    {
                        data: 'net_salary',
                        name: 'net_salary',
                        className: 'text-right'
                    },
                    {
                        data: 'status',
                        name: 'status',
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

            $('#generateForm').submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Start Calculation?',
                    text: "This will regenerate records for the selected period.",
                    icon: 'info',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('payrolls.store') }}",
                            type: 'POST',
                            data: $(this).serialize()
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#generateModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', 'Payroll generated successfully', 'success');
                    }
                });
            });
        });
    </script>
@endsection
