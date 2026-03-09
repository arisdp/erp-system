<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


use App\Models\User;
use App\Models\Company;
use App\Models\Role;

class FoundationSeeder extends Seeder
{

    public function run(): void
    {
        // Create Company
        $company = Company::updateOrCreate(
            ['code' => 'CMP-001'],
            ['name' => 'Demo Company', 'email' => 'demo@company.com']
        );

        // Create Admin User
        $user = User::updateOrCreate(
            ['email' => 'admin@erp.test'],
            [
                'company_id' => $company->id,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Attach user to company (using sync to avoid duplicates in pivot)
        $user->companies()->sync([$company->id]);

        // Create Role
        $role = Role::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'SuperAdmin'],
            ['guard_name' => 'web']
        );

        // Attach role to user
        $user->roles()->sync([$role->id]);
    }
}
