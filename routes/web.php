<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CompanyController;




use App\Http\Controllers\Finance\BankAccountController;
use App\Http\Controllers\Finance\BankTransactionController;
use App\Http\Controllers\Finance\AssetCategoryController;
use App\Http\Controllers\Finance\AssetController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\System\AuditLogController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\ApprovalController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Approvals
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('approvals/approve/{id}', [ApprovalController::class, 'approveViaLink'])->name('approvals.approve-link');
    Route::get('approvals/reject/{id}', [ApprovalController::class, 'rejectViaLink'])->name('approvals.reject-link');

    // Finance & Reports
    Route::resource('bank-accounts', BankAccountController::class);
    Route::resource('bank-transactions', BankTransactionController::class);
    Route::resource('asset-categories', AssetCategoryController::class);
    Route::post('assets/run-depreciation', [AssetController::class, 'runDepreciation'])->name('assets.run-depreciation');
    Route::resource('assets', AssetController::class);
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/profit-loss/print', [ReportController::class, 'printProfitLoss'])->name('reports.profit-loss.print');
    Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('reports/balance-sheet/print', [ReportController::class, 'printBalanceSheet'])->name('reports.balance-sheet.print');
    Route::get('system/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::prefix('master')->group(function () {
        Route::resource('companies', \App\Http\Controllers\Master\CompanyController::class);
        Route::resource('branches', \App\Http\Controllers\Master\BranchController::class);
        Route::resource('departments', \App\Http\Controllers\Master\DepartmentController::class);
        Route::resource('users', \App\Http\Controllers\Master\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Master\RoleController::class);
        Route::resource('accounts', \App\Http\Controllers\Master\AccountController::class);
        Route::resource('account-groups', \App\Http\Controllers\Master\AccountGroupController::class);
        Route::resource('fiscal-years', \App\Http\Controllers\Master\FiscalYearController::class);
        Route::resource('journals', \App\Http\Controllers\General\JournalController::class);

        // Inventory
        Route::resource('units', \App\Http\Controllers\Master\UnitController::class);
        Route::resource('warehouses', \App\Http\Controllers\Master\WarehouseController::class);
        Route::resource('product-categories', \App\Http\Controllers\Master\ProductCategoryController::class);
        Route::resource('products', \App\Http\Controllers\Master\ProductController::class);
        Route::resource('customers', \App\Http\Controllers\Master\CustomerController::class);
        Route::resource('suppliers', \App\Http\Controllers\Master\SupplierController::class);
        Route::resource('tax-rates', \App\Http\Controllers\Master\TaxRateController::class);
        Route::resource('currencies', \App\Http\Controllers\Master\CurrencyController::class);
        Route::resource('marketplaces', \App\Http\Controllers\Master\MarketplaceController::class);
    });

    // INVENTORY
    Route::group(['prefix' => 'inventory'], function () {
        Route::resource('stock-adjustments', \App\Http\Controllers\Inventory\StockAdjustmentController::class);
        Route::resource('stock-transfers', \App\Http\Controllers\Inventory\StockTransferController::class);
        Route::get('stock-cards', [\App\Http\Controllers\Inventory\StockCardController::class, 'index'])->name('stock-cards.index');
        Route::get('stock-cards/{product_id}/{warehouse_id}', [\App\Http\Controllers\Inventory\StockCardController::class, 'show'])->name('stock-cards.show');
    });

    // PROCUREMENT
    Route::group(['prefix' => 'procurement'], function () {
        Route::post('purchase-orders/{id}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::get('goods-receipts/{goodsReceipt}/print-qr', [\App\Http\Controllers\Procurement\GoodsReceiptController::class, 'printQr'])->name('goods-receipts.print-qr');
        Route::resource('goods-receipts', \App\Http\Controllers\Procurement\GoodsReceiptController::class);
        Route::resource('purchase-invoices', \App\Http\Controllers\Procurement\PurchaseInvoiceController::class);
    });

    // SALES
    Route::group(['prefix' => 'sales'], function () {
        Route::post('sales-orders/{id}/submit', [SalesOrderController::class, 'submit'])->name('sales-orders.submit');
        Route::resource('sales-orders', SalesOrderController::class);
        Route::resource('delivery-orders', \App\Http\Controllers\Sales\DeliveryOrderController::class);
        Route::resource('sales-invoices', \App\Http\Controllers\Sales\SalesInvoiceController::class);
    });

    // HR
    Route::group(['prefix' => 'hr'], function () {
        Route::resource('departments', \App\Http\Controllers\HR\DepartmentController::class);
        Route::resource('job-positions', \App\Http\Controllers\HR\JobPositionController::class);
        Route::resource('employees', \App\Http\Controllers\HR\EmployeeController::class);

        // Payroll & Leave
        Route::resource('leave-types', \App\Http\Controllers\HR\LeaveTypeController::class);
        Route::resource('leave-requests', \App\Http\Controllers\HR\LeaveRequestController::class);
        Route::resource('attendances', \App\Http\Controllers\HR\AttendanceController::class);
        Route::resource('payroll-components', \App\Http\Controllers\HR\PayrollComponentController::class);
        Route::resource('payrolls', \App\Http\Controllers\HR\PayrollController::class);
    });
});

require __DIR__ . '/auth.php';
