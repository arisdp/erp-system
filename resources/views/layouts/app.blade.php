<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP System | Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .main-sidebar {
            background-color: #1e2d3b !important;
        }

        .brand-link {
            border-bottom: 1px solid #4b545c !important;
        }

        .nav-link.active {
            background-color: #3f6791 !important;
            color: #fff !important;
        }

        /* DataTables Premium Polish */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: .25rem .5rem;
            font-size: .875rem;
            margin-left: 0.5rem;
            display: inline-block;
            width: auto;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: .25rem 1.5rem .25rem .5rem;
            margin: 0 5px;
            width: auto !important;
            height: auto !important;
            display: inline-block !important;
            font-size: .875rem;
            background-color: #fff;
        }

        .dataTables_length label,
        .dataTables_filter label {
            display: flex;
            align-items: center;
            white-space: nowrap;
            font-weight: 400 !important;
        }

        .dataTables_filter {
            text-align: right !important;
            display: flex;
            justify-content: flex-end;
        }

        .card-body {
            padding: 1.25rem;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before {
            background-color: #3f6791 !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">

        @include('partials.navbar')
        @include('partials.sidebar')

        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('page-title')</h1>
                        </div>
                        <div class="col-sm-6">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                @yield('content')
            </div>
        </div>

    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- DataTables & Plugins -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global Loader -->
    <div id="globalLoader"
        style="display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(255,255,255,0.6);
            z-index:9999;
            text-align:center;
            padding-top:20%;">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div>
    </div>

    <script>
        // GLOBAL AJAX LOADER
        $(document).ajaxStart(function() {
            $('#globalLoader').show();
        });

        $(document).ajaxStop(function() {
            $('#globalLoader').hide();
        });
    </script>

    @stack('scripts')

</body>

</html>
