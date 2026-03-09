<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $companyId = auth()->user()->company_id;

        $data = $this->reportService->getProfitLoss($companyId, $startDate, $endDate);

        return view('reports.profit_loss', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->get('date', Carbon::now()->toDateString());
        $companyId = auth()->user()->company_id;

        $data = $this->reportService->getBalanceSheet($companyId, $date);

        return view('reports.balance_sheet', array_merge($data, [
            'date' => $date
        ]));
    }
}
