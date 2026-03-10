<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = \App\Models\Company::first();
        if (!$company) return;

        $marketplaces = [
            ['name' => 'Shopee', 'company_id' => $company->id],
            ['name' => 'Tokopedia', 'company_id' => $company->id],
            ['name' => 'TikTok Shop', 'company_id' => $company->id],
            ['name' => 'Lazada', 'company_id' => $company->id],
        ];

        foreach ($marketplaces as $m) {
            \App\Models\Marketplace::updateOrCreate(['name' => $m['name']], $m);
        }
    }
}
