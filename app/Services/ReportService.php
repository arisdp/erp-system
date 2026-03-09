<?php

namespace App\Services;

use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Profit & Loss Data
     */
    public function getProfitLoss($companyId, $startDate, $endDate)
    {
        $revenueAccounts = ChartOfAccount::where('company_id', $companyId)
            ->whereHas('accountGroup', function($q) {
                $q->where('name', 'LIKE', '%Revenue%')->orWhere('name', 'LIKE', '%Income%')->orWhere('name', 'LIKE', '%Penjualan%');
            })->pluck('id');

        $expenseAccounts = ChartOfAccount::where('company_id', $companyId)
            ->whereHas('accountGroup', function($q) {
                $q->where('name', 'LIKE', '%Expense%')->orWhere('name', 'LIKE', '%Beban%')->orWhere('name', 'LIKE', '%Biaya%');
            })->pluck('id');

        $revenue = JournalEntryLine::whereIn('account_id', $revenueAccounts)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(credit - debit) as total'))
            ->first()->total ?? 0;

        $expenses = JournalEntryLine::whereIn('account_id', $expenseAccounts)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('SUM(debit - credit) as total'))
            ->first()->total ?? 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_profit' => $revenue - $expenses,
        ];
    }

    /**
     * Get Balance Sheet Data
     */
    public function getBalanceSheet($companyId, $date)
    {
        // Simple Balance Sheet aggregation
        $assets = $this->getGroupBalance($companyId, ['Asset', 'Aktiva', 'Harta'], $date);
        $liabilities = $this->getGroupBalance($companyId, ['Liability', 'Kewajiban', 'Hutang'], $date);
        $equity = $this->getGroupBalance($companyId, ['Equity', 'Modal', 'Ekuitas'], $date);

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'is_balanced' => abs($assets - ($liabilities + $equity)) < 0.001
        ];
    }

    private function getGroupBalance($companyId, array $groupNames, $date)
    {
        $accountIds = ChartOfAccount::where('company_id', $companyId)
            ->whereHas('accountGroup', function($q) use ($groupNames) {
                foreach($groupNames as $index => $name) {
                    if($index === 0) $q->where('name', 'LIKE', "%$name%");
                    else $q->orWhere('name', 'LIKE', "%$name%");
                }
            })->pluck('id');

        return JournalEntryLine::whereIn('account_id', $accountIds)
            ->where('created_at', '<=', $date)
            ->select(DB::raw('SUM(debit - credit) as balance'))
            ->first()->balance ?? 0;
    }

    /**
     * Get Stock Summary
     */
    public function getStockSummary($companyId)
    {
        return WarehouseStock::with(['product', 'warehouse'])
            ->where('company_id', $companyId)
            ->get();
    }
}
