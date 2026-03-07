@extends('layouts.app')

@section('page-title', 'Journal Entries')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-book mr-2"></i> Journal Entries</h3>
            <a href="{{ route('journals.create') }}" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus mr-1"></i> New Entry
            </a>
        </div>
        <div class="card-body">
            <table id="journalTable" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-dark text-sm">
                    <tr>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Description</th>
                        <th>Fiscal Year</th>
                        <th>Status</th>
                        <th width="80" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#journalTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('journals.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'journal_date'
                    },
                    {
                        data: 'journal_number'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'fiscal_year.year',
                        name: 'fiscalYear.year'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            let badge = data === 'posted' ? 'success' : 'warning';
                            return `<span class="badge badge-${badge}">${data.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endpush
