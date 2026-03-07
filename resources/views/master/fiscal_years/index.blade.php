@extends('layouts.app')

@section('page-title', 'Fiscal Years Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-calendar-alt mr-2"></i> Fiscal Years</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Year</button>
        </div>
        <div class="card-body">
            <table id="fiscalTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="fiscalModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="fiscalForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="fiscal_id">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Fiscal Year Form</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Year (YYYY)</label>
                            <div class="col-sm-8">
                                <input type="text" name="year" id="year" class="form-control"
                                    placeholder="e.g. 2026" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Start Date</label>
                            <div class="col-sm-8">
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">End Date</label>
                            <div class="col-sm-8">
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="is_closed" class="custom-control-input"
                                        id="is_closed_switch" value="1">
                                    <label class="custom-control-label" for="is_closed_switch">Closed</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Save Year</button>
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
            table = $('#fiscalTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('fiscal-years.index') }}",
                columns: [{
                        data: 'year'
                    },
                    {
                        data: 'start_date'
                    },
                    {
                        data: 'end_date'
                    },
                    {
                        data: 'is_closed'
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
                $('#fiscalModal').modal('show');
            });

            $('#fiscalForm').submit(function(e) {
                e.preventDefault();
                let id = $('#fiscal_id').val();
                let url = id ? "/master/fiscal-years/" + id : "{{ route('fiscal-years.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#fiscalModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        resetForm();
                    },
                    error: function(err) {
                        let msg = err.responseJSON.message || 'Error processing request';
                        Toast.fire({
                            icon: 'error',
                            title: msg
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("/master/fiscal-years/" + id + "/edit", function(res) {
                    $('#fiscal_id').val(res.id);
                    $('#year').val(res.year);
                    $('#start_date').val(res.start_date.split('T')[0]);
                    $('#end_date').val(res.end_date.split('T')[0]);
                    $('#is_closed_switch').prop('checked', res.is_closed);
                    $('#fiscalModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this fiscal year?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/fiscal-years/" + id,
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
                $('#fiscal_id').val('');
                $('#fiscalForm')[0].reset();
            }
        });
    </script>
@endpush
