@extends('layouts.app')

@section('page-title', 'Supplier Management')

@section('content')
    <div class="card shadow-sm text-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-truck mr-2"></i> Suppliers</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add
                Supplier</button>
        </div>
        <div class="card-body">
            <table id="supplierTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>Code</th>
                        <th>Supplier Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Currency</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="supplierModal">
        <div class="modal-dialog modal-lg text-sm">
            <div class="modal-content">
                <form id="supplierForm">
                    @csrf
                    <input type="hidden" name="id" id="supplier_id">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title font-weight-bold">Supplier Information</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label>Supplier Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control form-control-sm"
                                        placeholder="SUP-001" required>
                                </div>
                                <div class="form-group">
                                    <label>Company/Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm"
                                        placeholder="PT. ABC Global" required>
                                </div>
                                <div class="form-group">
                                    <label>Official Address</label>
                                    <textarea name="address" id="address" class="form-control form-control-sm" rows="3"
                                        placeholder="Full address..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group font-weight-light">
                                    <label>Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-sm"
                                        placeholder="info@supplier.com">
                                </div>
                                <div class="form-group">
                                    <label>Phone / WhatsApp</label>
                                    <input type="text" name="phone" id="phone" class="form-control form-control-sm"
                                        placeholder="+62 812...">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Preferred Currency</label>
                                            <select name="currency_id" id="currency_id"
                                                class="form-control form-control-sm">
                                                @foreach ($currencies as $curr)
                                                    <option value="{{ $curr->id }}">{{ $curr->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Terms</label>
                                            <select name="payment_term_id" id="payment_term_id"
                                                class="form-control form-control-sm">
                                                @foreach ($paymentTerms as $term)
                                                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save mr-1"></i> Save
                            Supplier</button>
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
            table = $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('suppliers.index') }}",
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'currency.code'
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
                $('#supplierModal').modal('show');
            });

            $('#supplierForm').submit(function(e) {
                e.preventDefault();
                let id = $('#supplier_id').val();
                let url = id ? "/master/suppliers/" + id : "{{ route('suppliers.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#supplierModal').modal('hide');
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
                $.get("/master/suppliers/" + id + "/edit", function(res) {
                    $('#supplier_id').val(res.id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#phone').val(res.phone);
                    $('#address').val(res.address);
                    $('#currency_id').val(res.currency_id);
                    $('#payment_term_id').val(res.payment_term_id);
                    $('#supplierModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this supplier?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/suppliers/" + id,
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
                $('#supplier_id').val('');
                $('#supplierForm')[0].reset();
            }
        });
    </script>
@endpush
