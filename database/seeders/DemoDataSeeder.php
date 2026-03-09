<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\AccountGroup;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Marketplace;
use App\Models\JobPosition;
use App\Models\Employee;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalYear;
use App\Models\Currency;
use App\Models\PaymentTerm;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'Demo Company')->first();
        if (!$company) {
            $company = Company::create([
                'code' => 'DEMO',
                'name' => 'Demo Company',
                'email' => 'demo@company.com'
            ]);
        }
        $companyId = $company->id;

        // 0. Fiscal Year
        $fy = FiscalYear::updateOrCreate(
            ['company_id' => $companyId, 'year' => 2026],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_closed' => false
            ]
        );

        // 1. Branch
        $branch = Branch::updateOrCreate(
            ['company_id' => $companyId, 'code' => 'HQ'],
            ['name' => 'Headquarters', 'address' => 'Jakarta, Indonesia', 'is_active' => true]
        );

        // 2. Account Groups & COA
        $assetType = DB::table('account_types')->where('code', '1')->first();
        $incomeType = DB::table('account_types')->where('code', '4')->first();
        $expenseType = DB::table('account_types')->where('code', '5')->first();

        // Asset Group
        $cashGroup = AccountGroup::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Cash & Bank'],
            ['account_type_id' => $assetType->id, 'code' => '1100']
        );
        $inventoryGroup = AccountGroup::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Inventory Assets'],
            ['account_type_id' => $assetType->id, 'code' => '1200']
        );

        // Account
        $cashAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '1101'],
            ['account_type_id' => $assetType->id, 'account_group_id' => $cashGroup->id, 'account_name' => 'Petty Cash', 'is_postable' => true]
        );
        $bankAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '1102'],
            ['account_type_id' => $assetType->id, 'account_group_id' => $cashGroup->id, 'account_name' => 'BCA Operational', 'is_postable' => true]
        );
        $stokAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '1201'],
            ['account_type_id' => $assetType->id, 'account_group_id' => $inventoryGroup->id, 'account_name' => 'Merchandise Inventory', 'is_postable' => true]
        );

        // Income Group
        $salesGroup = AccountGroup::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Sales Revenue'],
            ['account_type_id' => $incomeType->id, 'code' => '4100']
        );
        $salesAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '4101'],
            ['account_type_id' => $incomeType->id, 'account_group_id' => $salesGroup->id, 'account_name' => 'Product Sales', 'is_postable' => true]
        );

        // Expense Group
        $cogsGroup = AccountGroup::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Cost of Goods Sold'],
            ['account_type_id' => $expenseType->id, 'code' => '5100']
        );
        $cogsAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '5101'],
            ['account_type_id' => $expenseType->id, 'account_group_id' => $cogsGroup->id, 'account_name' => 'COGS - Products', 'is_postable' => true]
        );

        $opexGroup = AccountGroup::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Operating Expenses'],
            ['account_type_id' => $expenseType->id, 'code' => '6100']
        );
        $salaryAcc = ChartOfAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_code' => '6101'],
            ['account_type_id' => $expenseType->id, 'account_group_id' => $opexGroup->id, 'account_name' => 'Salary Expense', 'is_postable' => true]
        );

        // 3. Inventory Master
        $unit = Unit::updateOrCreate(['name' => 'Pcs'], ['company_id' => $companyId, 'is_active' => true]);
        $box = Unit::updateOrCreate(['name' => 'Box'], ['company_id' => $companyId, 'is_active' => true]);

        $catElectronics = ProductCategory::updateOrCreate(['name' => 'Electronics', 'company_id' => $companyId]);
        $catFurniture = ProductCategory::updateOrCreate(['name' => 'Office Furniture', 'company_id' => $companyId]);
        $catStationery = ProductCategory::updateOrCreate(['name' => 'Stationery', 'company_id' => $companyId]);

        $wh1 = Warehouse::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Main Warehouse'],
            ['code' => 'W-MAIN', 'is_active' => true]
        );
        $wh2 = Warehouse::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Sub-Warehouse Bandung'],
            ['code' => 'W-BDG', 'is_active' => true]
        );

        $p1 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'LAP-001'],
            ['name' => 'Laptop Pro 14 i7', 'category_id' => $catElectronics->id, 'unit_id' => $unit->id, 'purchase_price' => 12000000, 'selling_price' => 15500000, 'min_stock' => 5, 'max_stock' => 50]
        );
        $p2 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'MOU-001'],
            ['name' => 'Wireless Mouse Ergo', 'category_id' => $catElectronics->id, 'unit_id' => $unit->id, 'purchase_price' => 250000, 'selling_price' => 450000, 'min_stock' => 20, 'max_stock' => 200]
        );
        $p3 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'CHR-001'],
            ['name' => 'Ergonomic Office Chair', 'category_id' => $catFurniture->id, 'unit_id' => $unit->id, 'purchase_price' => 1500000, 'selling_price' => 2500000, 'min_stock' => 10, 'max_stock' => 100]
        );
        $p4 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'PRN-001'],
            ['name' => 'Laser Jet Printer', 'category_id' => $catElectronics->id, 'unit_id' => $unit->id, 'purchase_price' => 3000000, 'selling_price' => 4500000, 'min_stock' => 5, 'max_stock' => 30]
        );
        $p5 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'PAP-A4'],
            ['name' => 'Paper A4 80gr', 'category_id' => $catStationery->id, 'unit_id' => $box->id, 'purchase_price' => 50000, 'selling_price' => 65000, 'min_stock' => 50, 'max_stock' => 500]
        );

        // 4. CRM / SCM
        $currIDR = Currency::updateOrCreate(['code' => 'IDR'], ['company_id' => $companyId, 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'exchange_rate' => 1.000000]);
        $currUSD = Currency::updateOrCreate(['code' => 'USD'], ['company_id' => $companyId, 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 15500.000000]);

        $term = PaymentTerm::updateOrCreate(['company_id' => $companyId, 'name' => 'Net 30'], ['code' => 'N30', 'days' => 30]);

        $cust1 = Customer::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Budi Santoso'],
            ['code' => 'CUST001', 'email' => 'budi@gmail.test', 'type' => 'Both', 'currency_id' => $currIDR->id, 'payment_term_id' => $term->id]
        );
        $cust2 = Customer::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Global Corp Inc'],
            ['code' => 'CUST002', 'email' => 'procurement@global.test', 'type' => 'Online', 'currency_id' => $currUSD->id, 'payment_term_id' => $term->id]
        );
        $cust3 = Customer::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Sinar Terang Toko'],
            ['code' => 'CUST003', 'email' => 'order@sinarterang.test', 'type' => 'Offline', 'currency_id' => $currIDR->id, 'payment_term_id' => $term->id]
        );

        $supp1 = Supplier::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Tech Synergy Ltd'],
            ['code' => 'SUPP001', 'email' => 'sales@techsynergy.test', 'currency_id' => $currIDR->id, 'payment_term_id' => $term->id]
        );
        $supp2 = Supplier::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Global Parts Corp'],
            ['code' => 'SUPP002', 'email' => 'export@globalparts.test', 'currency_id' => $currUSD->id, 'payment_term_id' => $term->id]
        );

        $mp1 = Marketplace::updateOrCreate(['company_id' => $companyId, 'name' => 'Tokopedia'], ['is_active' => true]);
        $mp2 = Marketplace::updateOrCreate(['company_id' => $companyId, 'name' => 'Shopee'], ['is_active' => true]);

        // 5. HR
        $hrIT = Department::updateOrCreate(['company_id' => $companyId, 'name' => 'Information Technology'], ['code' => 'IT']);
        $hrSales = Department::updateOrCreate(['company_id' => $companyId, 'name' => 'Sales & Marketing'], ['code' => 'MKT']);
        $hrFinance = Department::updateOrCreate(['company_id' => $companyId, 'name' => 'Finance & Accounting'], ['code' => 'FIN']);

        $posDev = JobPosition::updateOrCreate(['company_id' => $companyId, 'name' => 'Senior Developer']);
        $posManager = JobPosition::updateOrCreate(['company_id' => $companyId, 'name' => 'Department Manager']);
        $posStaff = JobPosition::updateOrCreate(['company_id' => $companyId, 'name' => 'Admin Staff']);

        $emp1 = Employee::updateOrCreate(
            ['company_id' => $companyId, 'employee_code' => 'EMP001'],
            ['full_name' => 'John Doe', 'email' => 'john.doe@demo.test', 'department_id' => $hrIT->id, 'position_id' => $posDev->id, 'is_active' => true, 'join_date' => Carbon::now()->subYears(2)]
        );
        $emp2 = Employee::updateOrCreate(
            ['company_id' => $companyId, 'employee_code' => 'EMP002'],
            ['full_name' => 'Jane Smith', 'email' => 'jane.smith@demo.test', 'department_id' => $hrFinance->id, 'position_id' => $posManager->id, 'is_active' => true, 'join_date' => Carbon::now()->subYear()]
        );
        $emp3 = Employee::updateOrCreate(
            ['company_id' => $companyId, 'employee_code' => 'EMP003'],
            ['full_name' => 'Randi Wijaya', 'email' => 'randi@demo.test', 'department_id' => $hrSales->id, 'position_id' => $posStaff->id, 'is_active' => true, 'join_date' => Carbon::now()->subMonths(6)]
        );

        \Illuminate\Support\Facades\Log::info('Seeding Bank Account...');
        // 6. Bank Account
        $bankBCA = BankAccount::updateOrCreate(
            ['company_id' => $companyId, 'account_number' => '1234567890'],
            [
                'bank_name' => 'BCA',
                'name' => 'Operasional IDR',
                'chart_of_account_id' => $bankAcc->id,
                'initial_balance' => 750000000,
                'current_balance' => 750000000,
                'is_active' => true
            ]
        );

        \Illuminate\Support\Facades\Log::info('Seeding Asset Categories...');
        // 7. Assets
        $catAssetOffice = AssetCategory::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Office Electronics'],
            ['depreciation_method' => 'Straight-Line', 'useful_life_years' => 4, 'chart_of_account_id' => $bankAcc->id, 'depreciation_expense_account_id' => $salaryAcc->id, 'accumulated_depreciation_account_id' => $bankAcc->id]
        );

        \Illuminate\Support\Facades\Log::info('Seeding Assets...');
        Asset::updateOrCreate(
            ['company_id' => $companyId, 'code' => 'AST-001'],
            ['name' => 'Office Server Dell', 'category_id' => $catAssetOffice->id, 'purchase_date' => Carbon::now()->subYear(), 'purchase_price' => 50000000, 'salvage_value' => 5000000, 'current_value' => 38750000, 'status' => 'Active']
        );
        Asset::updateOrCreate(
            ['company_id' => $companyId, 'code' => 'AST-002'],
            ['name' => 'MacBook Pro Director', 'category_id' => $catAssetOffice->id, 'purchase_date' => Carbon::now()->subMonths(3), 'purchase_price' => 35000000, 'salvage_value' => 3500000, 'current_value' => 33000000, 'status' => 'Active']
        );

        // 8. Dummy Transactions
        // Manual Journal
        $je = JournalEntry::updateOrCreate(
            ['company_id' => $companyId, 'journal_number' => 'JV/' . date('Y/m') . '/001'],
            ['fiscal_year_id' => $fy->id, 'journal_date' => Carbon::now()->startOfMonth(), 'description' => 'Opening Balance Setup', 'status' => 'Posted', 'posted_at' => now()]
        );

        JournalEntryLine::where('journal_entry_id', $je->id)->delete();
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'company_id' => $companyId, 'account_id' => $bankAcc->id, 'debit' => 750000000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'company_id' => $companyId, 'account_id' => $stokAcc->id, 'debit' => 250000000, 'credit' => 0]);

        // Sales Orders (Varied Status)
        SalesOrder::updateOrCreate(
            ['company_id' => $companyId, 'so_number' => 'SO/2026/03/001'],
            ['customer_id' => $cust1->id, 'order_date' => Carbon::now()->subDays(10), 'status' => 'Delivered', 'transaction_type' => 'Offline', 'total_amount' => 15500000, 'tax_amount' => 0, 'net_amount' => 15500000, 'currency_id' => $currIDR->id, 'exchange_rate' => 1]
        );
        SalesOrder::updateOrCreate(
            ['company_id' => $companyId, 'so_number' => 'SO/SHP/001'],
            ['customer_id' => $cust2->id, 'marketplace_id' => $mp2->id, 'order_date' => Carbon::now()->subDays(1), 'status' => 'Pending', 'transaction_type' => 'Online', 'total_amount' => 4500000, 'tax_amount' => 0, 'net_amount' => 4500000, 'currency_id' => $currIDR->id, 'exchange_rate' => 1]
        );
        SalesOrder::updateOrCreate(
            ['company_id' => $companyId, 'so_number' => 'SO/2026/INT/001'],
            ['customer_id' => $cust2->id, 'order_date' => Carbon::now()->subHours(5), 'status' => 'Draft', 'transaction_type' => 'Offline', 'total_amount' => 2500, 'tax_amount' => 0, 'net_amount' => 2500, 'currency_id' => $currUSD->id, 'exchange_rate' => 15500]
        );

        // Purchase Orders
        PurchaseOrder::updateOrCreate(
            ['company_id' => $companyId, 'po_number' => 'PO/2026/03/001'],
            ['supplier_id' => $supp1->id, 'payment_term_id' => $term->id, 'currency_id' => $currIDR->id, 'order_date' => Carbon::now()->subDays(15), 'status' => 'Closed', 'total_amount' => 120000000, 'tax_amount' => 0, 'net_amount' => 120000000, 'exchange_rate' => 1]
        );
        PurchaseOrder::updateOrCreate(
            ['company_id' => $companyId, 'po_number' => 'PO/2026/03/002'],
            ['supplier_id' => $supp2->id, 'payment_term_id' => $term->id, 'currency_id' => $currUSD->id, 'order_date' => Carbon::now()->subDays(2), 'status' => 'Open', 'total_amount' => 10000, 'tax_amount' => 0, 'net_amount' => 10000, 'exchange_rate' => 15505]
        );

        // Attendance Data
        foreach ([$emp1, $emp2, $emp3] as $e) {
            for ($i = 0; $i < 7; $i++) {
                Attendance::updateOrCreate(
                    ['company_id' => $companyId, 'employee_id' => $e->id, 'date' => Carbon::today()->subDays($i)],
                    ['check_in' => '08:00:00', 'check_out' => '17:00:00', 'status' => 'Present']
                );
            }
        }
    }
}
