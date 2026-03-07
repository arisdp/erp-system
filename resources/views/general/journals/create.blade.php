@extends('layouts.app')

@section('page-title', 'Create Journal Entry')

@push('styles')
    <style>
        .line-remove {
            cursor: pointer;
            color: #dc3545;
        }

        .line-remove:hover {
            color: #a71d2a;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .balance-error {
            color: #dc3545;
            font-size: 0.9rem;
            display: none;
        }
    </style>
@endpush

@section('content')
    <form id="journalForm">
        @csrf
        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark">
                        <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i> New Journal Entry</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0" id="linesTable">
                            <thead class="thead-light text-xs uppercase">
                                <tr>
                                    <th width="35%">Account</th>
                                    <th width="20%">Debit</th>
                                    <th width="20%">Credit</th>
                                    <th width="20%">Description</th>
                                    <th width="50px"></th>
                                </tr>
                            </thead>
                            <tbody id="linesContainer">
                                <!-- Lines added via JS -->
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td class="text-right">TOTAL</td>
                                    <td id="totalDebit">0.00</td>
                                    <td id="totalCredit">0.00</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="p-2 border-top bg-light">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addLine">
                                <i class="fas fa-plus mr-1"></i> Add Line
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-sm">
                        <div class="form-group">
                            <label>Journal Date</label>
                            <input type="date" name="journal_date" class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Fiscal Year</label>
                            <select name="fiscal_year_id" class="form-control form-control-sm" required>
                                @foreach ($fiscalYears as $fy)
                                    <option value="{{ $fy->id }}">{{ $fy->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Global Description</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Entry purpose..."></textarea>
                        </div>
                        <hr>
                        <div id="balanceStatus" class="mb-3">
                            <span class="balance-error" id="unbalancedMsg">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Balance is not zero!
                            </span>
                        </div>
                        <button type="submit" class="btn btn-success btn-block" id="btnSubmit" disabled>
                            <i class="fas fa-save mr-1"></i> Post Entry
                        </button>
                        <a href="{{ route('journals.index') }}" class="btn btn-default btn-block btn-sm mt-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Template for JS --}}
    <template id="lineTemplate">
        <tr>
            <td class="p-1">
                <select name="lines[{index}][account_id]" class="form-control form-control-sm select2-account" required>
                    <option value="">Select Account</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-1">
                <input type="number" name="lines[{index}][debit]" class="form-control form-control-sm debit-input"
                    value="0" step="0.01" min="0">
            </td>
            <td class="p-1">
                <input type="number" name="lines[{index}][credit]" class="form-control form-control-sm credit-input"
                    value="0" step="0.01" min="0">
            </td>
            <td class="p-1">
                <input type="text" name="lines[{index}][description]" class="form-control form-control-sm"
                    placeholder="Optional...">
            </td>
            <td class="text-center align-middle p-1">
                <i class="fas fa-times-circle line-remove"></i>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        let lineIndex = 0;
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        $(document).ready(function() {
            // Add initial 2 lines
            addLine();
            addLine();

            $('#addLine').click(addLine);

            $(document).on('click', '.line-remove', function() {
                if ($('#linesContainer tr').length > 2) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Minimum 2 lines required'
                    });
                }
            });

            $(document).on('input', '.debit-input, .credit-input', function() {
                let row = $(this).closest('tr');
                if ($(this).hasClass('debit-input') && parseFloat($(this).val()) > 0) {
                    row.find('.credit-input').val(0);
                } else if ($(this).hasClass('credit-input') && parseFloat($(this).val()) > 0) {
                    row.find('.debit-input').val(0);
                }
                calculateTotals();
            });

            $('#journalForm').submit(function(e) {
                e.preventDefault();
                $('#btnSubmit').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Posting...');

                $.ajax({
                    url: "{{ route('journals.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.fire('Success', res.message, 'success').then(() => {
                            window.location.href = "{{ route('journals.index') }}";
                        });
                    },
                    error: function(err) {
                        $('#btnSubmit').prop('disabled', false).html(
                            '<i class="fas fa-save mr-1"></i> Post Entry');
                        let msg = err.responseJSON.message || 'Validation error';
                        Toast.fire({
                            icon: 'error',
                            title: msg
                        });
                    }
                });
            });

            function addLine() {
                let tpl = $('#lineTemplate').html().replace(/{index}/g, lineIndex++);
                $('#linesContainer').append(tpl);
                // Optionally initialize Select2 here if needed
            }

            function calculateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit-input').each(function() {
                    totalDebit += parseFloat($(this).val()) || 0;
                });
                $('.credit-input').each(function() {
                    totalCredit += parseFloat($(this).val()) || 0;
                });

                $('#totalDebit').text(totalDebit.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));
                $('#totalCredit').text(totalCredit.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                }));

                let balanced = Math.abs(totalDebit - totalCredit) < 0.01 && (totalDebit > 0);

                if (balanced) {
                    $('#unbalancedMsg').hide();
                    $('#btnSubmit').prop('disabled', false);
                } else {
                    $('#unbalancedMsg').show();
                    $('#btnSubmit').prop('disabled', true);
                }
            }
        });
    </script>
@endpush
