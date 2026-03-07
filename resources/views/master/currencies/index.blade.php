@extends('layouts.app')

@section('page-title', 'Currency Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center text-sm">
            <h3 class="card-title mb-0"><i class="fas fa-coins mr-2"></i> Currencies</h3>
            <button class="btn btn-primary btn-sm ml-auto font-weight-bold" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add
                Currency</button>
        </div>
        <div class="card-body">
            <table id="currencyTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-xs uppercase">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Exchange Rate</th>
                        <th class="text-center">Type</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="currencyModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="currencyForm" class="text-sm font-weight-light">
                    @csrf
                    <input type="hidden" name="id" id="currency_id">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title font-weight-bold">Currency Form</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Currency Code</label>
                            <div class="col-sm-8">
                                <input type="text" name="code" id="code" class="form-control"
                                    placeholder="e.g. IDR, USD" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Currency Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="e.g. Indonesian Rupiah" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Exchange Rate</label>
                            <div class="col-sm-8 text-sm">
                                <input type="number" name="exchange_rate" id="exchange_rate"
                                    class="form-control text-right" step="0.000001" value="1.00" required>
                                <small class="text-muted italic">*Rate relative to base currency</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-sm">Is Base Currency?</label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="is_base" class="custom-control-input" id="baseSwitch"
                                        value="1">
                                    <label class="custom-control-label font-weight-light" for="baseSwitch">Set as
                                        base</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Save Currency</button>
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
            table = $('#currencyTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('currencies.index') }}",
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'exchange_rate',
                        className: 'text-right'
                    },
                    {
                        data: 'is_base',
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
                $('#currencyModal').modal('show');
            });

            $('#currencyForm').submit(function(e) {
                e.preventDefault();
                let id = $('#currency_id').val();
                let url = id ? "/master/currencies/" + id : "{{ route('currencies.store') }}";
                let method = id ? "PUT" : "POST";

                let formData = $(this).serializeArray();
                if (!formData.find(x => x.name === 'is_base')) formData.push({
                    name: 'is_base',
                    value: 0
                });

                $.ajax({
                    url: url,
                    type: method,
                    data: $.param(formData),
                    success: function(res) {
                        $('#currencyModal').modal('hide');
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
                $.get("/master/currencies/" + id + "/edit", function(res) {
                    $('#currency_id').val(res.id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#exchange_rate').val(res.exchange_rate);
                    $('#baseSwitch').prop('checked', res.is_base);
                    $('#currencyModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this currency?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/currencies/" + id,
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
                $('#currency_id').val('');
                $('#currencyForm')[0].reset();
            }
        });
    </script>
@endpush
