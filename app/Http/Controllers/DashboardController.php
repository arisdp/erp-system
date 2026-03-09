<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\Asset;
use App\Models\WarehouseStock;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $stats = [
            'total_sales' => SalesOrder::where('company_id', $companyId)->where('status', 'confirmed')->sum('total_amount'),
            'total_purchases' => PurchaseOrder::where('company_id', $companyId)->where('status', 'confirmed')->sum('total_amount'),
            'asset_value' => Asset::where('company_id', $companyId)->sum('current_value'),
            'cash_balance' => BankAccount::where('company_id', $companyId)->sum('current_balance'),
            'low_stock' => WarehouseStock::where('company_id', $companyId)->whereColumn('quantity', '<=', 'min_stock')->count(),
            'pending_so' => SalesOrder::where('company_id', $companyId)->where('status', 'Draft')->count(),
            'pending_po' => PurchaseOrder::where('company_id', $companyId)->where('status', 'Draft')->count(),
            'unpaid_ar' => \App\Models\SalesInvoice::where('company_id', $companyId)->where('status', 'Unpaid')->count(),
            'unpaid_ap' => \App\Models\PurchaseInvoice::where('company_id', $companyId)->where('status', 'Unpaid')->count(),
            'attendance_today' => \App\Models\Attendance::where('company_id', $companyId)->whereDate('date', Carbon::today())->count(),
        ];

        // Sales Trend (Last 6 months)
        $salesTrend = SalesOrder::where('company_id', $companyId)
            ->where('status', 'confirmed')
            ->where('order_date', '>=', Carbon::now()->subMonths(6))
            ->selectRaw("TO_CHAR(order_date, 'YYYY-MM') as month, SUM(total_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent Activities (Combined)
        $recentSO = SalesOrder::where('company_id', $companyId)->latest()->take(3)->get()->map(function ($i) {
            $i->type = 'Sales Order';
            $i->ref = $i->so_number;
            return $i;
        });
        $recentPO = PurchaseOrder::where('company_id', $companyId)->latest()->take(3)->get()->map(function ($i) {
            $i->type = 'Purchase Order';
            $i->ref = $i->po_number;
            return $i;
        });
        $recentActivities = $recentSO->concat($recentPO)->sortByDesc('created_at')->take(5);

        return view('dashboard', compact('stats', 'salesTrend', 'recentActivities'));
    }
}
