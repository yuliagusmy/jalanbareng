<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $commenter;
    protected $comment;
    protected $commentable;

    public function __construct($commenter, $comment, $commentable)
    {
        $this->commenter = $commenter;
        $this->comment = $comment;
        $this->commentable = $commentable;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = $this->commentable->title ?? $this->commentable->name ?? 'postingan Anda';
        $typeShort = str_contains(get_class($this->commentable), 'Destination') ? 'destinasi' : 'event';

        $targetUrl = '/';
        if ($typeShort === 'destinasi') {
            $targetUrl = '/destinations/' . $this->commentable->id;
        } elseif ($typeShort === 'event') {
            $targetUrl = '/events/' . $this->commentable->id;
        }

        $snippet = mb_strimwidth($this->comment->content ?? '', 0, 50, '...');

        return [
            'type' => 'comment',
            'actor_id' => $this->commenter->id,
            'actor_name' => $this->commenter->name,
            'actor_photo' => $this->commenter->photo,
            'item_id' => $this->commentable->id,
            'item_type' => $typeShort,
            'item_title' => $title,
            'comment_snippet' => $snippet,
            'target_url' => $targetUrl,
            'message' => $this->commenter->name . ' mengomentari ' . $typeShort . ' "' . $title . '": "' . $snippet . '"',
        ];
    }
}
