@extends('layouts.app')

@section('page-title', 'Journal Details: ' . $journal->journal_number)

@section('content')
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-dark d-flex justify-content-between">
                    <h3 class="card-title">Transaction Details</h3>
                    <span class="badge badge-success text-sm">{{ strtoupper($journal->status) }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light text-xs uppercase text-sm">
                            <tr>
                                <th>Account</th>
                                <th width="150" class="text-right">Debit</th>
                                <th width="150" class="text-right">Credit</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp
                            @foreach ($journal->lines as $line)
                                <tr>
                                    <td>{{ $line->account->code }} - {{ $line->account->name }}</td>
                                    <td class="text-right">{{ number_format($line->debit, 2) }}</td>
                                    <td class="text-right">{{ number_format($line->credit, 2) }}</td>
                                    <td class="text-muted small">{{ $line->description }}</td>
                                </tr>
                                @php
                                    $totalDebit += $line->debit;
                                    $totalCredit += $line->credit;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light font-weight-bold">
                            <tr>
                                <td class="text-right">TOTAL</td>
                                <td class="text-right text-primary">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-right text-primary">{{ number_format($totalCredit, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="{{ route('journals.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm float-right">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title">Metadata</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless text-sm m-2">
                        <tr>
                            <td width="100" class="text-muted">Number</td>
                            <td class="font-weight-bold">{{ $journal->journal_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>{{ $journal->journal_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fiscal Year</td>
                            <td>{{ $journal->fiscalYear->year }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>{{ $journal->description ?: '-' }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted">Created By</td>
                            <td>{{ $journal->createdBy->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created At</td>
                            <td>{{ $journal->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
