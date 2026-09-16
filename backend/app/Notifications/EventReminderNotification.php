<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $timeframe; // '24h' or '3h'

    public function __construct($event, string $timeframe = '24h')
    {
        $this->event = $event;
        $this->timeframe = $timeframe;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $timeLabel = $this->timeframe === '3h' ? '3 jam lagi' : 'besok (H-1)';
        $title = $this->event->title ?? 'Event Jalan Bareng';

        return [
            'type' => 'event_reminder',
            'actor_id' => null,
            'actor_name' => 'Sistem Jalan Bareng',
            'actor_photo' => null,
            'item_id' => $this->event->id,
            'item_type' => 'event',
            'item_title' => $title,
            'timeframe' => $this->timeframe,
            'target_url' => '/events/' . $this->event->id,
            'message' => 'Pengingat Event: "' . $title . '" akan dimulai ' . $timeLabel . '! Jangan lupa bersiap-siap ya.',
        ];
    }
}
