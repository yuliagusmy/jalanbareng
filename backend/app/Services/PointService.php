<?php

namespace App\Services;

use App\Models\CashoutRequest;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Konversi poin: 1 Poin = Rp 100,-
     */
    public const RUPIAH_PER_POINT = 100;
    public const MIN_CASHOUT_POINTS = 500;

    /**
     * Dapatkan atau buat catatan saldo poin pengguna
     */
    public static function getOrCreateUserPoint(User $user): UserPoint
    {
        return UserPoint::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
            ]
        );
    }

    /**
     * Tambahkan poin (Kredit) ke saldo pengguna
     */
    public static function addPoints(User $user, int $amount, string $source, ?int $referenceId, string $description): PointTransaction
    {
        return DB::transaction(function () use ($user, $amount, $source, $referenceId, $description) {
            $userPoint = self::getOrCreateUserPoint($user);
            $userPoint->increment('balance', $amount);
            $userPoint->increment('total_earned', $amount);

            return PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'source' => $source,
                'reference_id' => $referenceId,
                'status' => 'completed',
                'description' => $description,
            ]);
        });
    }

    /**
     * Kurangi poin (Debit) untuk penarikan / redeem
     */
    public static function deductPoints(User $user, int $amount, string $source, ?int $referenceId, string $description): PointTransaction
    {
        return DB::transaction(function () use ($user, $amount, $source, $referenceId, $description) {
            $userPoint = self::getOrCreateUserPoint($user);

            if ($userPoint->balance < $amount) {
                throw new \InvalidArgumentException('Saldo poin Anda tidak mencukupi untuk melakukan transaksi ini.');
            }

            $userPoint->decrement('balance', $amount);

            return PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'source' => $source,
                'reference_id' => $referenceId,
                'status' => 'completed',
                'description' => $description,
            ]);
        });
    }

    /**
     * Kembalikan poin yang sempat terpotong (misal cashout ditolak)
     */
    public static function refundPoints(User $user, int $amount, string $source, ?int $referenceId, string $description): PointTransaction
    {
        return DB::transaction(function () use ($user, $amount, $source, $referenceId, $description) {
            $userPoint = self::getOrCreateUserPoint($user);
            $userPoint->increment('balance', $amount);

            return PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'source' => $source,
                'reference_id' => $referenceId,
                'status' => 'completed',
                'description' => $description,
            ]);
        });
    }
}
