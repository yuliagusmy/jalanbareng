<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $communityAdminRole = Role::where('name', 'community_admin')->first();
        $memberRole = Role::where('name', 'member')->first();

        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@jalanbareng.com'],
            [
                'name' => 'Admin Jalan Bareng',
                'password' => Hash::make('JalanBareng2025!'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create Community Admin User
        User::firstOrCreate(
            ['email' => 'community@jalanbareng.com'],
            [
                'name' => 'Community Admin',
                'password' => Hash::make('community123'),
                'role_id' => $communityAdminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create Multiple Community Admins
        $communityAdmins = [
            ['name' => 'Andi Rahman', 'email' => 'andi.rahman@jalanbareng.com'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@jalanbareng.com'],
        ];

        foreach ($communityAdmins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $communityAdminRole->id,
                    'email_verified_at' => now(),
                    'phone' => '081234567890',
                ]
            );
        }

        // Create Regular Members
        $members = [
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@gmail.com'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@gmail.com'],
            ['name' => 'Fajar Pratama', 'email' => 'fajar.pratama@gmail.com'],
            ['name' => 'Rina Amelia', 'email' => 'rina.amelia@gmail.com'],
            ['name' => 'Agus Wijaya', 'email' => 'agus.wijaya@gmail.com'],
            ['name' => 'Maya Sari', 'email' => 'maya.sari@gmail.com'],
            ['name' => 'Doni Kurniawan', 'email' => 'doni.kurniawan@gmail.com'],
            ['name' => 'Lisa Permata', 'email' => 'lisa.permata@gmail.com'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi.hermawan@gmail.com'],
            ['name' => 'Nina Safitri', 'email' => 'nina.safitri@gmail.com'],
            ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@gmail.com'],
            ['name' => 'Ani Wijayanti', 'email' => 'ani.wijayanti@gmail.com'],
            ['name' => 'Yudi Setiawan', 'email' => 'yudi.setiawan@gmail.com'],
            ['name' => 'Eka Putri', 'email' => 'eka.putri@gmail.com'],
            ['name' => 'Bambang Prasetyo', 'email' => 'bambang.prasetyo@gmail.com'],
        ];

        foreach ($members as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $memberRole->id,
                    'email_verified_at' => now(),
                    'phone' => '081234567891',
                ]
            );
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Total: 1 Admin + 3 Community Admins + 15 Members');
        $this->command->info('Admin: admin@jalanbareng.com / JalanBareng2025!');
        $this->command->info('Community Admin: community@jalanbareng.com / community123');
        $this->command->info('All passwords: password123');
    }
}
