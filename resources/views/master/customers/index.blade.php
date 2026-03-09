@extends('layouts.app')

@section('page-title', 'Customer Management')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i> Customers</h3>
            <button class="btn btn-primary btn-sm ml-auto" id="btnAdd"><i class="fas fa-plus mr-1"></i> Add
                Customer</button>
        </div>
        <div class="card-body">
            <table id="customerTable" class="table table-bordered table-hover table-striped w-100 font-weight-light">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Currency</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="customerModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="customerForm" class="text-sm">
                    @csrf
                    <input type="hidden" name="id" id="customer_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">Customer Form</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label>Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        placeholder="CUST-001" required>
                                </div>
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Customer Name" required>
                                </div>
                                <div class="form-group text-sm">
                                    <label>Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Jl. Raya No. 123..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group font-weight-light">
                                    <label>Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="customer@example.com">
                                </div>
                                <div class="form-group text-sm">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="08123456789">
                                </div>
                                <div class="form-group">
                                    <label>Customer Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="Offline">Offline</option>
                                        <option value="Online">Online</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency</label>
                                            <select name="currency_id" id="currency_id" class="form-control">
                                                @foreach ($currencies as $curr)
                                                    <option value="{{ $curr->id }}">{{ $curr->code }} -
                                                        {{ $curr->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Term</label>
                                            <select name="payment_term_id" id="payment_term_id" class="form-control">
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
                        <button type="submit" class="btn btn-primary btn-sm">Save Customer</button>
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
            table = $('#customerTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('customers.index') }}",
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
                        data: 'type',
                        render: function(data) {
                            let badge = data === 'Online' ? 'info' : (data === 'Both' ? 'primary' :
                                'secondary');
                            return '<span class="badge badge-' + badge + '">' + data + '</span>';
                        }
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
                $('#customerModal').modal('show');
            });

            $('#customerForm').submit(function(e) {
                e.preventDefault();
                let id = $('#customer_id').val();
                let url = id ? "/master/customers/" + id : "{{ route('customers.store') }}";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#customerModal').modal('hide');
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
                $.get("/master/customers/" + id + "/edit", function(res) {
                    $('#customer_id').val(res.id);
                    $('#code').val(res.code);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#phone').val(res.phone);
                    $('#address').val(res.address);
                    $('#type').val(res.type);
                    $('#currency_id').val(res.currency_id);
                    $('#payment_term_id').val(res.payment_term_id);
                    $('#customerModal').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this customer?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "/master/customers/" + id,
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
                $('#customer_id').val('');
                $('#customerForm')[0].reset();
            }
        });
    </script>
@endpush
