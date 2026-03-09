@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>IDR {{ number_format($stats['total_sales'] / 1000000, 1) }}M</h3>
                    <p>Total Sales (Confirmed)</p>
                </div>
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <a href="{{ route('sales-orders.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>IDR {{ number_format($stats['cash_balance'] / 1000000, 1) }}M</h3>
                    <p>Cash Balance</p>
                </div>
                <div class="icon"><i class="fas fa-university"></i></div>
                <a href="{{ route('bank-accounts.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>IDR {{ number_format($stats['asset_value'] / 1000000, 1) }}M</h3>
                    <p>Total Asset Value</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <a href="{{ route('assets.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['low_stock'] }}</h3>
                    <p>Low Stock Items</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('stock-cards.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- PENDING TASKS ROW -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-basket"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending SO</span>
                    <span class="info-box-number">{{ $stats['pending_so'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-cart-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending PO</span>
                    <span class="info-box-number">{{ $stats['pending_po'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Unpaid AR</span>
                    <span class="info-box-number">{{ $stats['unpaid_ar'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Attendance Today</span>
                    <span class="info-box-number">{{ $stats['attendance_today'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Sales Trend</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="salesChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                                <tr>
                                    <td>
                                        <small class="text-muted d-block">{{ $act->ref }}</small>
                                        {{ $act->type }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $act->status == 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($act->status) }}
                                        </span>
                                    </td>
                                    <td class="text-right"><strong>{{ number_format($act->total_amount, 0) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No recent activities</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="javascript:void(0)" class="uppercase">View All Transactions</a>
                </div>
            </div>

            <div class="card bg-gradient-info">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-th mr-1"></i> Quick Links</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('sales-orders.create') }}" class="btn btn-sm btn-light btn-block mb-1">New Sales
                        Order</a>
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-light btn-block mb-1">New
                        Purchase Order</a>
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-sm btn-light btn-block">Financial
                        Reports</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            var salesCanvas = $('#salesChart').get(0).getContext('2d');
            var salesData = {
                labels: @json($salesTrend->pluck('month')),
                datasets: [{
                    label: 'Sales (IDR)',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: @json($salesTrend->pluck('total'))
                }]
            };

            var salesOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                }
            };

            new Chart(salesCanvas, {
                type: 'bar',
                data: salesData,
                options: salesOptions
            });
        });
    </script>
@endpush
