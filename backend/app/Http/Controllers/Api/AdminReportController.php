<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Data ringkasan dan tren engagement bulanan
     */
    public function engagement(Request $request)
    {
        $months = (int) $request->input('months', 6);
        $summary = ReportService::getEngagementSummary();
        $trends = ReportService::getMonthlyTrends($months);

        return response()->json([
            'data' => [
                'summary' => $summary,
                'trends' => $trends,
            ],
            'message' => 'Data laporan engagement berhasil dimuat',
        ]);
    }

    /**
     * Ekspor CSV / Excel data member
     */
    public function exportMembers(Request $request)
    {
        $csv = ReportService::generateMembersCsv();
        $filename = 'members_' . date('Ymd_His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Ekspor CSV / Excel pendaftar aktivasi / event
     */
    public function exportParticipants(Request $request)
    {
        $eventId = $request->input('event_id') ? (int) $request->input('event_id') : null;
        $csv = ReportService::generateParticipantsCsv($eventId);
        $filename = 'participants_' . ($eventId ? "event_{$eventId}_" : '') . date('Ymd_His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
