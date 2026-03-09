@extends('layouts.app')

@section('page-title', 'Marketplaces')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Marketplaces</h3>
            <div class="card-tools">
                <a href="{{ route('marketplaces.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <table class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($marketplaces as $marketplace)
                        <tr>
                            <td>{{ $marketplace->name }}</td>
                            <td>
                                <span class="badge badge-{{ $marketplace->is_active ? 'success' : 'danger' }}">
                                    {{ $marketplace->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('marketplaces.edit', $marketplace->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('marketplaces.destroy', $marketplace->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.datatable').DataTable({
                "responsive": true,
                "autoWidth": false,
            });
        });
    </script>
@endpush
