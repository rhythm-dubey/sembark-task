<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();
        $password = Hash::make('password');
        $roleId = Role::where('name', 'super_admin')->value('id');

        User::updateOrCreate(
            [
                'email' => 'superadmin@sembark.test',
            ],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'role_id' => $roleId,
                'company_id' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
