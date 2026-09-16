<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateEventDatesSeeder extends Seeder
{
    public function run(): void
    {
        // Update all events to have upcoming dates
        $events = DB::table('events')->get();
        
        $count = 0;
        foreach ($events as $index => $event) {
            // Set dates from tomorrow onwards, spaced 7 days apart
            $newDate = Carbon::now()->addDays(($index * 7) + 1);
            
            DB::table('events')
                ->where('id', $event->id)
                ->update(['date' => $newDate]);
            
            $count++;
        }
        
        $this->command->info("Updated {$count} events to have upcoming dates");
    }
}
