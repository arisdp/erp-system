@extends('layouts.app')

@section('title', 'Bank Transactions')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Cash/Bank Ledger</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#txModal">
                            <i class="fas fa-plus"></i> New Transaction
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bank Account</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Offset Account (GL)</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="txModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="txForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Record Bank Transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Bank Account</label>
                            <select name="bank_account_id" class="form-control" required>
                                <option value="">Select Account</option>
                                @foreach ($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}
                                        ({{ number_format($acc->current_balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="date" class="form-control" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="transaction_type" class="form-control" required>
                                        <option value="In">Money In (Debit Bank)</option>
                                        <option value="Out">Money Out (Credit Bank)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" required step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Offset GL Account (Category)</label>
                            <select name="chart_of_account_id" class="form-control select2" required style="width: 100%;">
                                <option value="">Select Offset</option>
                                @foreach ($coas as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Reference/Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
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
                ajax: "{{ route('bank-transactions.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'bank_name',
                        name: 'bankAccount.name'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type',
                        className: 'text-center'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: 'text-right'
                    },
                    {
                        data: 'offset_name',
                        name: 'offsetAccount.name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    }
                ]
            });

            $('#txForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('bank-transactions.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#txModal').modal('hide');
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
