<?php

namespace Database\Seeders;

use App\Models\CashoutRequest;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserPoint;
use App\Services\PointService;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $userPoint = UserPoint::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 650,
                    'total_earned' => 750,
                    'total_withdrawn' => 100,
                ]
            );

            // Transaksi 1: Cerita Disetujui
            PointTransaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'source' => 'story_approved',
                    'description' => "Poin kontributor: Tulisan 'Menelusuri Jejak Sejarah di Benteng Rotterdam' disetujui kurator",
                ],
                [
                    'amount' => 100,
                    'type' => 'credit',
                    'reference_id' => 1,
                    'status' => 'completed',
                    'created_at' => now()->subDays(5),
                ]
            );

            // Transaksi 2: Tambah Landmark Baru
            PointTransaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'source' => 'landmark_added',
                    'description' => "Poin kontributor: Menambahkan titik landmark baru 'Kawasan Pecinan Somba Opu'",
                ],
                [
                    'amount' => 75,
                    'type' => 'credit',
                    'reference_id' => 2,
                    'status' => 'completed',
                    'created_at' => now()->subDays(3),
                ]
            );

            // Transaksi 3: Event Check-in
            PointTransaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'source' => 'event_checkin',
                    'description' => "Poin partisipasi: Hadir & Check-in di Walk Event 'Makassar Sunset Walk #12'",
                ],
                [
                    'amount' => 50,
                    'type' => 'credit',
                    'reference_id' => 1,
                    'status' => 'completed',
                    'created_at' => now()->subDays(2),
                ]
            );

            // Transaksi 4: Update Fasilitas Landmark
            PointTransaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'source' => 'landmark_enriched',
                    'description' => "Poin kontributor: Memperbarui data akses trotoar & kran air di Pantai Losari",
                ],
                [
                    'amount' => 25,
                    'type' => 'credit',
                    'reference_id' => 1,
                    'status' => 'completed',
                    'created_at' => now()->subDay(),
                ]
            );
        }

        // Buat 1 contoh pengajuan cashout pending dari salah satu user member
        $firstMember = User::where('email', '!=', 'admin@jalanbareng.com')->first();
        if ($firstMember) {
            CashoutRequest::firstOrCreate(
                [
                    'user_id' => $firstMember->id,
                    'account_number' => '081234567890',
                    'status' => 'pending',
                ],
                [
                    'points_requested' => 500,
                    'rupiah_amount' => 50000,
                    'payment_method' => 'gopay',
                    'account_name' => $firstMember->name,
                    'status' => 'pending',
                    'created_at' => now()->subHours(6),
                ]
            );
        }
    }
}
