<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashoutRequest;
use App\Models\PointTransaction;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    /**
     * Get user point wallet info, balance, and transaction history
     */
    public function myPoints(Request $request)
    {
        $user = $request->user();
        $userPoint = PointService::getOrCreateUserPoint($user);

        $transactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $cashoutRequests = CashoutRequest::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'data' => [
                'point' => $userPoint,
                'balance' => $userPoint->balance,
                'rupiah_equivalent' => $userPoint->balance * PointService::RUPIAH_PER_POINT,
                'total_earned' => $userPoint->total_earned,
                'total_withdrawn' => $userPoint->total_withdrawn,
                'min_cashout_points' => PointService::MIN_CASHOUT_POINTS,
                'rupiah_per_point' => PointService::RUPIAH_PER_POINT,
                'transactions' => $transactions,
                'recent_cashouts' => $cashoutRequests,
            ],
            'message' => 'Data poin berhasil dimuat',
        ]);
    }

    /**
     * Submit a cashout / withdrawal request
     */
    public function requestCashout(Request $request)
    {
        $user = $request->user();
        $userPoint = PointService::getOrCreateUserPoint($user);

        $validated = $request->validate([
            'points_requested' => 'required|integer|min:' . PointService::MIN_CASHOUT_POINTS,
            'payment_method' => 'required|string|in:bank_bca,bank_mandiri,bank_bri,bank_bni,gopay,ovo,dana',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $points = (int) $validated['points_requested'];

        if ($userPoint->balance < $points) {
            return response()->json([
                'message' => "Saldo poin Anda ({$userPoint->balance}) tidak mencukupi untuk menarik {$points} poin.",
            ], 422);
        }

        $rupiah = $points * PointService::RUPIAH_PER_POINT;

        $cashout = DB::transaction(function () use ($user, $validated, $points, $rupiah) {
            $cashout = CashoutRequest::create([
                'user_id' => $user->id,
                'points_requested' => $points,
                'rupiah_amount' => $rupiah,
                'payment_method' => $validated['payment_method'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => 'pending',
            ]);

            PointService::deductPoints(
                $user,
                $points,
                'cashout',
                $cashout->id,
                "Pengajuan pencairan {$points} poin ke Rp " . number_format($rupiah, 0, ',', '.')
            );

            return $cashout;
        });

        return response()->json([
            'data' => $cashout,
            'message' => 'Pengajuan pencairan poin berhasil diajukan! Admin Jalan Bareng akan memproses permohonan Anda.',
        ], 201);
    }

    /**
     * Admin: List all cashout requests
     */
    public function adminCashoutIndex(Request $request)
    {
        $query = CashoutRequest::with(['user', 'processor'])->latest();

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $counts = [
            'all' => CashoutRequest::count(),
            'pending' => CashoutRequest::where('status', 'pending')->count(),
            'approved' => CashoutRequest::where('status', 'approved')->count(),
            'rejected' => CashoutRequest::where('status', 'rejected')->count(),
        ];

        $cashouts = $query->paginate(15);

        return response()->json([
            'data' => [
                'cashouts' => $cashouts,
                'counts' => $counts,
            ],
            'message' => 'Daftar pengajuan pencairan dana berhasil dimuat',
        ]);
    }

    /**
     * Admin: Approve or Reject cashout request
     */
    public function adminUpdateCashoutStatus(Request $request, $id)
    {
        $cashout = CashoutRequest::with('user')->findOrFail($id);

        if ($cashout->status !== 'pending') {
            return response()->json([
                'message' => "Pengajuan ini sudah berstatus {$cashout->status} dan tidak dapat diubah lagi.",
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $admin = $request->user();

        DB::transaction(function () use ($cashout, $validated, $admin, $request) {
            if ($request->hasFile('receipt_image')) {
                $path = $request->file('receipt_image')->store('cashout-receipts', 'public');
                $validated['receipt_image'] = $path;
            }

            $validated['processed_by'] = $admin->id;
            $validated['processed_at'] = now();

            if ($validated['status'] === 'approved') {
                $userPoint = PointService::getOrCreateUserPoint($cashout->user);
                $userPoint->increment('total_withdrawn', $cashout->points_requested);
            } elseif ($validated['status'] === 'rejected') {
                PointService::refundPoints(
                    $cashout->user,
                    $cashout->points_requested,
                    'cashout_rejected',
                    $cashout->id,
                    "Pengembalian saldo: Penarikan poin ditolak ({$validated['admin_notes']})"
                );
            }

            $cashout->update($validated);
        });

        $label = $cashout->status === 'approved' ? 'disetujui dan ditransfer' : 'ditolak';

        return response()->json([
            'data' => $cashout->fresh(['user', 'processor']),
            'message' => "Pengajuan pencairan berhasil {$label}.",
        ]);
    }
}
