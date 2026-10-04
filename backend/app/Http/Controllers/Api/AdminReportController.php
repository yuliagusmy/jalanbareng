<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function exportMembers()
    {
        $members = User::select('id', 'name', 'email', 'created_at')->get();
        $csvData = "ID,Nama,Email,Bergabung\n";
        
        foreach ($members as $member) {
            $csvData .= "{$member->id},\"{$member->name}\",{$member->email},{$member->created_at}\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members_export.csv"',
        ]);
    }

    public function exportParticipants()
    {
        $participants = DB::table('event_participants')
            ->join('events', 'event_participants.event_id', '=', 'events.id')
            ->select('event_participants.id', 'events.name as event_name', 'event_participants.name', 'event_participants.email', 'event_participants.phone', 'event_participants.created_at')
            ->get();

        $csvData = "ID,Event,Nama,Email,Telepon,Tanggal Daftar\n";
        
        foreach ($participants as $p) {
            $csvData .= "{$p->id},\"{$p->event_name}\",\"{$p->name}\",{$p->email},{$p->phone},{$p->created_at}\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="participants_export.csv"',
        ]);
    }
}
