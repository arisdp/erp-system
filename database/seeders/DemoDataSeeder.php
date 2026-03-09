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
        $cat = ProductCategory::updateOrCreate(['name' => 'Electronics', 'company_id' => $companyId]);

        $wh1 = Warehouse::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Main Warehouse'],
            ['code' => 'W-MAIN', 'is_active' => true]
        );

        $p1 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'LAP-001'],
            ['name' => 'Laptop Pro 14', 'category_id' => $cat->id, 'unit_id' => $unit->id, 'purchase_price' => 12000000, 'selling_price' => 15000000]
        );
        $p2 = Product::updateOrCreate(
            ['company_id' => $companyId, 'sku' => 'MOU-001'],
            ['name' => 'Wireless Mouse', 'category_id' => $cat->id, 'unit_id' => $unit->id, 'purchase_price' => 200000, 'selling_price' => 350000]
        );

        // 4. CRM / SCM
        $currency = Currency::where('code', 'IDR')->first();
        $term = PaymentTerm::where('company_id', $companyId)->first();

        $cust = Customer::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Budi Santoso'],
            [
                'code' => 'CUST001',
                'email' => 'budi@gmail.test',
                'phone' => '08123456789',
                'type' => 'Both',
                'currency_id' => $currency->id ?? null,
                'payment_term_id' => $term->id ?? null
            ]
        );
        $supp = Supplier::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Tech Synergy Ltd'],
            [
                'code' => 'SUPP001',
                'email' => 'sales@techsynergy.test',
                'currency_id' => $currency->id ?? null,
                'payment_term_id' => $term->id ?? null
            ]
        );
        $mp = Marketplace::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Tokopedia'],
            ['is_active' => true]
        );

        // 5. HR
        $hrDept = Department::updateOrCreate(
            ['company_id' => $companyId, 'name' => 'Information Technology'],
            ['code' => 'IT']
        );
        $pos = JobPosition::updateOrCreate(['company_id' => $companyId, 'name' => 'Senior Developer']);

        $emp = Employee::updateOrCreate(
            ['company_id' => $companyId, 'employee_code' => 'EMP001'],
            [
                'full_name' => 'John Doe',
                'email' => 'john.doe@demo.test',
                'department_id' => $hrDept->id,
                'position_id' => $pos->id,
                'is_active' => true,
                'join_date' => Carbon::now()->subYear()
            ]
        );

        // 6. Bank Account
        DB::table('bank_accounts')->updateOrInsert(
            ['company_id' => $companyId, 'account_number' => '1234567890'],
            [
                'id' => DB::table('bank_accounts')->where('account_number', '1234567890')->value('id') ?? Str::uuid(),
                'bank_name' => 'BCA',
                'name' => 'PT Demo Sukses',
                'chart_of_account_id' => $bankAcc->id,
                'initial_balance' => 500000000,
                'current_balance' => 500000000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 7. Dummy Transactions
        // Manual Journal
        $je = JournalEntry::updateOrCreate(
            ['company_id' => $companyId, 'journal_number' => 'JV/' . date('Y/m') . '/001'],
            [
                'fiscal_year_id' => $fy->id,
                'journal_date' => Carbon::now()->startOfMonth(),
                'description' => 'Opening Balance',
                'status' => 'Posted',
                'posted_at' => now()
            ]
        );

        JournalEntryLine::where('journal_entry_id', $je->id)->delete();
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'company_id' => $companyId, 'account_id' => $bankAcc->id, 'debit' => 500000000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'company_id' => $companyId, 'account_id' => $stokAcc->id, 'debit' => 100000000, 'credit' => 0]);

        // Sales Orders
        SalesOrder::updateOrCreate(
            ['company_id' => $companyId, 'so_number' => 'SO/2026/03/001'],
            [
                'customer_id' => $cust->id,
                'order_date' => Carbon::now()->subDays(2),
                'status' => 'Approved',
                'transaction_type' => 'Offline',
                'total_amount' => 15000000,
                'tax_amount' => 0,
                'net_amount' => 15000000
            ]
        );

        SalesOrder::updateOrCreate(
            ['company_id' => $companyId, 'so_number' => 'SO/TOKOPEDIA/001'],
            [
                'customer_id' => $cust->id,
                'marketplace_id' => $mp->id,
                'order_date' => Carbon::now(),
                'status' => 'Draft',
                'transaction_type' => 'Online',
                'total_amount' => 350000,
                'tax_amount' => 0,
                'net_amount' => 350000
            ]
        );

        // Purchase Order
        PurchaseOrder::updateOrCreate(
            ['company_id' => $companyId, 'po_number' => 'PO/2026/03/001'],
            [
                'supplier_id' => $supp->id,
                'payment_term_id' => $term->id ?? null,
                'currency_id' => $currency->id ?? null,
                'order_date' => Carbon::now()->subDays(5),
                'status' => 'Open',
                'total_amount' => 50000000,
                'tax_amount' => 0,
                'net_amount' => 50000000
            ]
        );

        // Attendance Demo
        DB::table('attendances')->updateOrInsert(
            ['company_id' => $companyId, 'employee_id' => $emp->id, 'date' => Carbon::today()],
            [
                'id' => DB::table('attendances')->where('employee_id', $emp->id)->where('date', Carbon::today())->value('id') ?? Str::uuid(),
                'check_in' => '08:00:00',
                'status' => 'Present',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
