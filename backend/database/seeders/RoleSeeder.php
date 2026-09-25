<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access and management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'community_admin',
                'display_name' => 'Community Admin',
                'description' => 'Can create events and moderate content',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'member',
                'display_name' => 'Member',
                'description' => 'Regular user with basic permissions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role['name']], $role);
        }
    }
}
