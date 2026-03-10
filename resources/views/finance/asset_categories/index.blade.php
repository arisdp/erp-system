@extends('layouts.app')

@section('title', 'Asset Categories')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0 text-dark"><i class="fas fa-tags mr-2 text-info"></i> Asset Categories</h3>
                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-sm btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#categoryModal"
                            id="addNewBtn">
                            <i class="fas fa-plus mr-1"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Method</th>
                                <th>Life (Years)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" name="id" id="categoryId">
                    <div class="modal-header">
                        <h5 class="modal-title">Asset Category Setup</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required
                                        placeholder="e.g. Vehicles, Office Equipment">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Method</label>
                                    <select name="depreciation_method" class="form-control" required>
                                        <option value="Straight Line">Straight Line</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Useful Life (Years)</label>
                                    <input type="number" name="useful_life_years" id="useful_life_years"
                                        class="form-control" required min="1">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Accounting Integration</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Asset GL Account</label>
                                    <select name="chart_of_account_id" id="chart_of_account_id" class="form-control select2"
                                        required style="width: 100%;">
                                        <option value="">Select GL</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Depreciation Expense GL</label>
                                    <select name="depreciation_expense_account_id" id="depreciation_expense_account_id"
                                        class="form-control select2" required style="width: 100%;">
                                        <option value="">Select GL</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Accumulated Depr. Contra GL</label>
                                    <select name="accumulated_depreciation_account_id"
                                        id="accumulated_depreciation_account_id" class="form-control select2" required
                                        style="width: 100%;">
                                        <option value="">Select GL</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Save Category</button>
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
                ajax: "{{ route('asset-categories.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'depreciation_method',
                        name: 'depreciation_method'
                    },
                    {
                        data: 'useful_life_years',
                        name: 'useful_life_years',
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

            $('#addNewBtn').click(function() {
                $('#categoryForm').trigger("reset");
                $('#categoryId').val('');
                $('.select2').val('').trigger('change');
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('asset-categories.index') }}/" + id, function(data) {
                    $('#categoryId').val(data.id);
                    $('#name').val(data.name);
                    $('#useful_life_years').val(data.useful_life_years);
                    $('#chart_of_account_id').val(data.chart_of_account_id).trigger('change');
                    $('#depreciation_expense_account_id').val(data.depreciation_expense_account_id)
                        .trigger('change');
                    $('#accumulated_depreciation_account_id').val(data
                        .accumulated_depreciation_account_id).trigger('change');
                    $('#categoryModal').modal('show');
                });
            });

            $('#categoryForm').submit(function(e) {
                e.preventDefault();
                let id = $('#categoryId').val();
                let url = id ? "{{ route('asset-categories.index') }}/" + id :
                    "{{ route('asset-categories.store') }}";
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#categoryModal').modal('hide');
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
