@extends('layouts.app')

@section('title', 'Profit & Loss Statement')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter Period</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.profit-loss') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="form-group mr-3">
                            <label class="mr-2">End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <button type="button" class="btn btn-default ml-2" onclick="window.print()"><i
                                class="fas fa-print"></i> Print</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>PROFIT & LOSS STATEMENT</h3>
                        <h5>Period: {{ $startDate }} to {{ $endDate }}</h5>
                    </div>

                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-bold">
                                <td>REVENUE</td>
                                <td class="text-right">{{ number_format($revenue, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 30px;">Total Income</td>
                                <td class="text-right border-bottom">{{ number_format($revenue, 2) }}</td>
                            </tr>

                            <tr class="font-weight-bold">
                                <td class="pt-4">EXPENSES</td>
                                <td class="text-right pt-4"></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 30px;">Total Operating Expenses</td>
                                <td class="text-right border-bottom">{{ number_format($expenses, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold h5 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                <td>NET PROFIT / LOSS</td>
                                <td class="text-right">{{ number_format($net_profit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
