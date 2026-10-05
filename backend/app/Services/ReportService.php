<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Like;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Hitung statistik ringkasan platform & engagement bulanan
     */
    public static function getEngagementSummary(): array
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // Bulanan (30 hari terakhir) vs Total
        return [
            'total_members' => User::count(),
            'new_members_month' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'total_destinations' => Destination::count(),
            'new_destinations_month' => Destination::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'total_comments' => Comment::count(),
            'new_comments_month' => Comment::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'total_likes' => Like::count(),
            'new_likes_month' => Like::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'total_events' => Event::count(),
            'total_participants' => DB::table('event_participants')->count(),
            'new_participants_month' => DB::table('event_participants')->where('created_at', '>=', $thirtyDaysAgo)->count(),
        ];
    }

    /**
     * Ambil data tren bulanan selama 6 atau 12 bulan terakhir
     */
    public static function getMonthlyTrends(int $months = 6): array
    {
        $months = max(3, min(24, $months));
        $result = [];
        $now = Carbon::now();

        // Siapkan array bulan dari terlama ke terbaru
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $yearMonth = $monthDate->format('Y-m');
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            $commentsCount = Comment::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $likesCount = Like::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $destinationsCount = Destination::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $membersCount = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $participantsCount = DB::table('event_participants')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            $result[] = [
                'period' => $yearMonth,
                'label' => $monthDate->locale('id')->isoFormat('MMM YYYY'),
                'comments' => $commentsCount,
                'likes' => $likesCount,
                'destinations' => $destinationsCount,
                'members' => $membersCount,
                'participants' => $participantsCount,
                'total_engagement' => $commentsCount + $likesCount + $destinationsCount,
            ];
        }

        return $result;
    }

    /**
     * Generate CSV konten untuk member
     */
    public static function generateMembersCsv(): string
    {
        $members = User::with('role')->orderBy('created_at', 'desc')->get();
        
        $output = fopen('php://temp', 'r+');
        // BOM UTF-8 agar karakter Indonesia / Excel membaca dengan benar
        fputs($output, "\xEF\xBB\xBF");
        
        fputcsv($output, ['ID', 'Nama Lengkap', 'Email', 'No Telepon', 'Role', 'Status Ban', 'Tanggal Bergabung']);

        foreach ($members as $m) {
            fputcsv($output, [
                $m->id,
                $m->name,
                $m->email,
                $m->phone ?? '-',
                $m->role?->name ?? 'member',
                $m->ban_status ?? 'active',
                $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : '-',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generate CSV / XML-Spreadsheet konten untuk peserta aktivasi (kompatibel Excel)
     */
    public static function generateParticipantsCsv(?int $eventId = null): string
    {
        $query = DB::table('event_participants')
            ->join('events', 'event_participants.event_id', '=', 'events.id')
            ->leftJoin('users', 'event_participants.user_id', '=', 'users.id')
            ->select(
                'event_participants.id',
                'events.name as event_name',
                'events.date as event_date',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'event_participants.created_at'
            )
            ->orderBy('event_participants.created_at', 'desc');

        if ($eventId) {
            $query->where('events.id', $eventId);
        }

        $participants = $query->get();

        $output = fopen('php://temp', 'r+');
        fputs($output, "\xEF\xBB\xBF");
        
        fputcsv($output, ['ID Registrasi', 'Nama Kegiatan/Event', 'Tanggal Event', 'Nama Peserta', 'Email', 'No Telepon', 'Tanggal Daftar']);

        foreach ($participants as $p) {
            fputcsv($output, [
                $p->id,
                $p->event_name,
                $p->event_date ?? '-',
                $p->user_name ?? '-',
                $p->user_email ?? '-',
                $p->user_phone ?? '-',
                $p->created_at ? Carbon::parse($p->created_at)->format('Y-m-d H:i:s') : '-',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
