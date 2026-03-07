<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\TaxRate;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\DB;

class MasterFinanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Currencies
        Currency::updateOrCreate(['code' => 'IDR'], ['name' => 'Indonesian Rupiah', 'exchange_rate' => 1, 'is_base' => true]);
        Currency::updateOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'exchange_rate' => 15500, 'is_base' => false]);

        // 2. Tax Rates
        TaxRate::updateOrCreate(['name' => 'PPN 11%'], ['rate' => 11.00, 'is_active' => true]);
        TaxRate::updateOrCreate(['name' => 'Non-Taxable'], ['rate' => 0.00, 'is_active' => true]);

        // 3. Payment Terms (System defaults for all companies for now or specific to first company)
        $companyId = DB::table('companies')->first()->id;
        PaymentTerm::updateOrCreate(['name' => 'Cash', 'company_id' => $companyId], ['days' => 0]);
        PaymentTerm::updateOrCreate(['name' => 'Net 30', 'company_id' => $companyId], ['days' => 30]);
        PaymentTerm::updateOrCreate(['name' => 'Net 60', 'company_id' => $companyId], ['days' => 60]);
    }
}
