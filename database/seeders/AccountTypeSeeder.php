<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => '1', 'name' => 'Asset', 'normal_balance' => 'debit'],
            ['code' => '2', 'name' => 'Liability', 'normal_balance' => 'credit'],
            ['code' => '3', 'name' => 'Equity', 'normal_balance' => 'credit'],
            ['code' => '4', 'name' => 'Income', 'normal_balance' => 'credit'],
            ['code' => '5', 'name' => 'Expense', 'normal_balance' => 'debit'],
        ];

        foreach ($types as $type) {
            AccountType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
