<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send event reminder notifications to participants (H-1 and 3 hours before)';

    public function handle()
    {
        $now = Carbon::now();
        $this->info("Checking event reminders at: " . $now->toDateTimeString());

        // 1. Check for 24-hour (H-1) reminders: events starting between 23h and 25h from now
        $events24h = Event::with('participants')
            ->whereBetween('date', [$now->copy()->addHours(23), $now->copy()->addHours(25)])
            ->get();

        $sent24h = 0;
        foreach ($events24h as $event) {
            foreach ($event->participants as $participant) {
                // Check if already notified for 24h
                $alreadySent = $participant->notifications()
                    ->where('data->type', 'event_reminder')
                    ->where('data->item_id', $event->id)
                    ->where('data->timeframe', '24h')
                    ->exists();

                if (!$alreadySent) {
                    $participant->notify(new EventReminderNotification($event, '24h'));
                    $sent24h++;
                }
            }
        }
        $this->info("Sent {$sent24h} 24-hour reminder(s).");

        // 2. Check for 3-hour reminders: events starting between now and 3h from now
        $events3h = Event::with('participants')
            ->whereBetween('date', [$now, $now->copy()->addHours(3)])
            ->get();

        $sent3h = 0;
        foreach ($events3h as $event) {
            foreach ($event->participants as $participant) {
                // Check if already notified for 3h
                $alreadySent = $participant->notifications()
                    ->where('data->type', 'event_reminder')
                    ->where('data->item_id', $event->id)
                    ->where('data->timeframe', '3h')
                    ->exists();

                if (!$alreadySent) {
                    $participant->notify(new EventReminderNotification($event, '3h'));
                    $sent3h++;
                }
            }
        }
        $this->info("Sent {$sent3h} 3-hour reminder(s).");

        return 0;
    }
}
