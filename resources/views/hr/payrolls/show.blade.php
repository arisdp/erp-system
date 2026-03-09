@extends('layouts.app')

@section('title', 'Salary Slip: ' . $payroll->employee->full_name)

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="invoice p-4 mb-3" id="printArea">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h4>SALARY SLIP</h4>
                        <p class="mb-0">Period: <strong>{{ date('F', mktime(0, 0, 0, $payroll->period_month, 10)) }}
                                {{ $payroll->period_year }}</strong></p>
                    </div>
                </div>

                <div class="row invoice-info mb-4 border-bottom pb-3">
                    <div class="col-sm-6 invoice-col">
                        <strong>Employee Details:</strong>
                        <address>
                            Name: {{ $payroll->employee->full_name }}<br>
                            Employee ID: {{ $payroll->employee->employee_code }}<br>
                            Department: {{ $payroll->employee->department->name ?? '-' }}<br>
                            Position: {{ $payroll->employee->position->name ?? '-' }}
                        </address>
                    </div>
                    <div class="col-sm-6 invoice-col text-right">
                        <strong>Status:</strong>
                        @if ($payroll->status === 'Paid')
                            <h3 class="text-success">{{ $payroll->status }}</h3>
                        @else
                            <h3 class="text-secondary">{{ $payroll->status }}</h3>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 border-right">
                        <p class="lead">Allowances & Earnings</p>
                        <table class="table table-sm">
                            <tr>
                                <th>Basic Salary</th>
                                <td class="text-right">Rp {{ number_format($payroll->basic_salary, 2) }}</td>
                            </tr>
                            @foreach ($payroll->details->where('component.type', 'Allowance') as $detail)
                                <tr>
                                    <td>{{ $detail->component->name }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold">
                                <td>Total Earnings</td>
                                <td class="text-right border-top">Rp
                                    {{ number_format($payroll->basic_salary + $payroll->total_allowance, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <p class="lead">Deductions</p>
                        <table class="table table-sm">
                            @foreach ($payroll->details->where('component.type', 'Deduction') as $detail)
                                <tr>
                                    <td>{{ $detail->component->name }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            @if ($payroll->total_deduction > 0 && $payroll->details->where('component.type', 'Deduction')->count() == 0)
                                <tr>
                                    <td>Other Deductions (Leave, etc)</td>
                                    <td class="text-right">Rp {{ number_format($payroll->total_deduction, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="font-weight-bold">
                                <td>Total Deductions</td>
                                <td class="text-right border-top">Rp {{ number_format($payroll->total_deduction, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table border-0">
                                <tr style="font-size: 1.25rem;">
                                    <th class="text-right">NET TAKE HOME PAY:</th>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($payroll->net_salary, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row no-print">
                <div class="col-12">
                    <button onclick="window.print();" class="btn btn-default"><i class="fas fa-print"></i> Print</button>
                    @if ($payroll->status !== 'Paid')
                        <button class="btn btn-success float-right" id="payBtn"><i class="far fa-credit-card"></i> Mark
                            as Paid</button>
                    @endif
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary float-right mr-2">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#payBtn').click(function() {
            Swal.fire({
                title: 'Confirm Payment?',
                text: "This will mark this payroll as paid.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('payrolls.update', $payroll->id) }}",
                        type: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'pay'
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endsection
