@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Filter Date</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.balance-sheet') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">As of Date:</label>
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                        </div>
                        <button type="submit" class="btn btn-info">View Balance Sheet</button>
                        <button type="button" class="btn btn-default ml-2" onclick="window.print()"><i
                                class="fas fa-print"></i> Print</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>BALANCE SHEET</h3>
                        <h5>As of {{ $date }}</h5>
                    </div>

                    <div class="row">
                        <!-- Assets Side -->
                        <div class="col-md-6 border-right">
                            <h5 class="text-primary border-bottom pb-2">ASSETS</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Current Assets</td>
                                    <td class="text-right">{{ number_format($assets, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>TOTAL ASSETS</td>
                                    <td class="text-right">{{ number_format($assets, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Liabilities & Equity Side -->
                        <div class="col-md-6">
                            <h5 class="text-danger border-bottom pb-2">LIABILITIES & EQUITY</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Total Liabilities</td>
                                    <td class="text-right">{{ number_format($liabilities, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Equity</td>
                                    <td class="text-right">{{ number_format($equity, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>TOTAL LIABILITIES & EQUITY</td>
                                    <td class="text-right">{{ number_format($liabilities + $equity, 2) }}</td>
                                </tr>
                            </table>

                            @if (!$is_balanced)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> Balance Sheet is not balanced! Difference:
                                    {{ number_format($assets - ($liabilities + $equity), 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
