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
        $company = Company::create([
            'code' => 'CMP-001',
            'name' => 'Demo Company',
            'email' => 'demo@company.com',
        ]);

        // Create Admin User
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@erp.test',
            'password' => Hash::make('password'),
        ]);

        // Attach user to company
        $user->companies()->attach($company->id);

        // Create Role
        $role = Role::create([
            'company_id' => $company->id,
            'name' => 'SuperAdmin',
            'guard_name' => 'web',
        ]);

        // Attach role to user
        $user->roles()->attach($role->id);
    }
}
