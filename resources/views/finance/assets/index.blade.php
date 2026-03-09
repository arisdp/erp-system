@extends('layouts.app')

@section('title', 'Fixed Assets')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Fixed Asset Register</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info mr-1" id="runDepreciationBtn">
                            <i class="fas fa-sync"></i> Run Depreciation
                        </button>
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#assetModal"
                            id="addNewBtn">
                            <i class="fas fa-plus"></i> Register Asset
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Purchase Date</th>
                                <th>Price</th>
                                <th>Current Value</th>
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
    <div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="assetForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Register New Asset</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Asset Name</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="e.g. MacBook Pro M3">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->useful_life_years }}
                                        Years)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Purchase Price</label>
                                    <input type="number" name="purchase_price" class="form-control" required
                                        step="0.01">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Salvage Value (Nilai Sisa)</label>
                                    <input type="number" name="salvage_value" class="form-control" value="0"
                                        step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('assets.index') }}",
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category.name'
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'purchase_price',
                        name: 'purchase_price',
                        className: 'text-right'
                    },
                    {
                        data: 'current_value',
                        name: 'current_value',
                        className: 'text-right'
                    },
                    {
                        data: 'status',
                        name: 'status',
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

            $('#assetForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('assets.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#assetModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong',
                            'error');
                    }
                });
            });

            $('#runDepreciationBtn').click(function() {
                Swal.fire({
                    title: 'Run Depreciation?',
                    text: "This will start the depreciation process in the background for this month.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, run it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('assets.run-depreciation') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Queued!', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON.message ||
                                    'Something went wrong', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
