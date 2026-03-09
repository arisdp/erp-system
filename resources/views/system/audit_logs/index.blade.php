@extends('layouts.app')

@section('title', 'System Audit Trail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Event Logs & Data Changes</h3>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Model</th>
                                <th>Old Values</th>
                                <th>New Values</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('audit-logs.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'event',
                        name: 'event'
                    },
                    {
                        data: 'auditable_type',
                        name: 'auditable_type'
                    },
                    {
                        data: 'old_values',
                        name: 'old_values',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'new_values',
                        name: 'new_values',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    }
                ]
            });
        });
    </script>
@endsection
