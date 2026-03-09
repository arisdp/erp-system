@extends('layouts.app')

@section('title', 'Bank & Cash Accounts')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">List of Bank/Cash Accounts</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#accountModal"
                            id="addNewBtn">
                            <i class="fas fa-plus"></i> Add Account
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th>Bank Name</th>
                                <th>Account Number</th>
                                <th>GL Account</th>
                                <th>Balance</th>
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
    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="accountForm">
                    @csrf
                    <input type="hidden" name="id" id="accountId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Bank Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Account Name (Reference)</label>
                            <input type="text" name="name" id="name" class="form-control" required
                                placeholder="e.g. BCA Operational">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>GL Account (Chart of Account)</label>
                            <select name="chart_of_account_id" id="chart_of_account_id" class="form-control select2"
                                required style="width: 100%;">
                                <option value="">Select Account</option>
                                @foreach ($coas as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="initialBalanceGroup">
                            <label>Initial Balance</label>
                            <input type="number" name="initial_balance" id="initial_balance" class="form-control"
                                value="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="isActiveSwitch"
                                    checked value="1">
                                <label class="custom-control-label" for="isActiveSwitch">Is Active?</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Account</button>
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
                ajax: "{{ route('bank-accounts.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'account_number',
                        name: 'account_number'
                    },
                    {
                        data: 'account_name',
                        name: 'chartOfAccount.name'
                    },
                    {
                        data: 'current_balance',
                        name: 'current_balance',
                        className: 'text-right'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
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
                $('#accountForm').trigger("reset");
                $('#accountId').val('');
                $('#chart_of_account_id').val('').trigger('change');
                $('#initialBalanceGroup').show();
                $('#modalTitle').text('Add Bank Account');
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('bank-accounts.index') }}/" + id, function(data) {
                    $('#modalTitle').text('Edit Bank Account');
                    $('#accountId').val(data.id);
                    $('#name').val(data.name);
                    $('#bank_name').val(data.bank_name);
                    $('#account_number').val(data.account_number);
                    $('#chart_of_account_id').val(data.chart_of_account_id).trigger('change');
                    $('#initialBalanceGroup').hide();
                    $('#isActiveSwitch').prop('checked', data.is_active);
                    $('#accountModal').modal('show');
                });
            });

            $('#accountForm').submit(function(e) {
                e.preventDefault();
                let id = $('#accountId').val();
                let url = id ? "{{ route('bank-accounts.index') }}/" + id :
                    "{{ route('bank-accounts.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#accountModal').modal('hide');
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
