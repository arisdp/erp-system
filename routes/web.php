<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CompanyController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    });

    // INVENTORY
    Route::group(['prefix' => 'inventory'], function () {
        Route::resource('stock-adjustments', \App\Http\Controllers\Inventory\StockAdjustmentController::class);
        Route::get('stock-cards', [\App\Http\Controllers\Inventory\StockCardController::class, 'index'])->name('stock-cards.index');
        Route::get('stock-cards/{product_id}/{warehouse_id}', [\App\Http\Controllers\Inventory\StockCardController::class, 'show'])->name('stock-cards.show');
    });

    // PROCUREMENT
    Route::group(['prefix' => 'procurement'], function () {
        Route::resource('purchase-orders', \App\Http\Controllers\Procurement\PurchaseOrderController::class);
        Route::resource('goods-receipts', \App\Http\Controllers\Procurement\GoodsReceiptController::class);
        Route::resource('purchase-invoices', \App\Http\Controllers\Procurement\PurchaseInvoiceController::class);
    });

    // SALES
    Route::group(['prefix' => 'sales'], function () {
        Route::resource('sales-orders', \App\Http\Controllers\Sales\SalesOrderController::class);
        Route::resource('delivery-orders', \App\Http\Controllers\Sales\DeliveryOrderController::class);
        Route::resource('sales-invoices', \App\Http\Controllers\Sales\SalesInvoiceController::class);
    });
});

require __DIR__ . '/auth.php';
