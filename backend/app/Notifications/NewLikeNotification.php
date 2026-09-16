<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification
{
    use Queueable;

    protected $liker;
    protected $likeable;
    protected $likeableType;

    public function __construct($liker, $likeable, string $likeableType)
    {
        $this->liker = $liker;
        $this->likeable = $likeable;
        $this->likeableType = $likeableType;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = $this->likeable->title ?? $this->likeable->name ?? 'postingan Anda';
        $typeShort = str_contains($this->likeableType, 'Destination') ? 'destinasi' : 
                     (str_contains($this->likeableType, 'Event') ? 'event' : 'komentar');

        $targetUrl = '/';
        if ($typeShort === 'destinasi') {
            $targetUrl = '/destinations/' . $this->likeable->id;
        } elseif ($typeShort === 'event') {
            $targetUrl = '/events/' . $this->likeable->id;
        }

        return [
            'type' => 'like',
            'actor_id' => $this->liker->id,
            'actor_name' => $this->liker->name,
            'actor_photo' => $this->liker->photo,
            'item_id' => $this->likeable->id,
            'item_type' => $typeShort,
            'item_title' => $title,
            'target_url' => $targetUrl,
            'message' => $this->liker->name . ' menyukai ' . $typeShort . ' Anda: "' . $title . '"',
        ];
    }
}
