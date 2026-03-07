@extends('layouts.app')

@section('page-title', 'Tax Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-percentage mr-2"></i> Tax Rates</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add Tax
                Rate</button>
        </div>
        <div class="card-body">
            <table id="taxRateTable" class="table table-bordered table-hover table-striped w-100 font-weight-light">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Tax Name</th>
                        <th>Rate (%)</th>
                        <th class="text-center">Status</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="taxRateModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="taxRateForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="tax_rate_id">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title font-weight-bold">Tax Rate Form</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Tax Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="e.g. PPN 11%, PPh 23" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Rate (%)</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" name="rate" id="rate" class="form-control"
                                        placeholder="11.00" step="0.01" min="0" max="100" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Active Status</label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="is_active" class="custom-control-input" id="activeSwitch"
                                        value="1" checked>
                                    <label class="custom-control-label" for="activeSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Save Tax Rate</button>
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
            table = $('#taxRateTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('tax-rates.index') }}",
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'rate'
                    },
                    {
                        data: 'is_active',
                        className: 'text-center'
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
                $('#taxRateModal').modal('show');
            });

            $('#taxRateForm').submit(function(e) {
                e.preventDefault();
                let id = $('#tax_rate_id').val();
                let url = id ? "/master/tax-rates/" + id : "{{ route('tax-rates.store') }}";
                let method = id ? "PUT" : "POST";

                let formData = $(this).serializeArray();
                if (!formData.find(x => x.name === 'is_active')) formData.push({
                    name: 'is_active',
                    value: 0
                });

                $.ajax({
                    url: url,
                    type: method,
                    data: $.param(formData),
                    success: function(res) {
                        $('#taxRateModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        resetForm();
                    },
                    error: function(err) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error processing request'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("/master/tax-rates/" + id + "/edit", function(res) {
                    $('#tax_rate_id').val(res.id);
                    $('#name').val(res.name);
                    $('#rate').val(res.rate);
                    $('#activeSwitch').prop('checked', res.is_active);
                    $('#taxRateModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this tax rate?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/tax-rates/" + id,
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
                $('#tax_rate_id').val('');
                $('#taxRateForm')[0].reset();
                $('#activeSwitch').prop('checked', true);
            }
        });
    </script>
@endpush
