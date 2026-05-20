<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->upsert([
            ['name' => 'super_admin', 'label' => 'Super Admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'admin', 'label' => 'Admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'member', 'label' => 'Member', 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['label', 'updated_at']);
    }
}
