<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GlobalAnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $title,
        private string $body,
        private string $icon = 'megaphone',
        private string $type = 'info',
        private string $category = 'general',
        private ?string $actionUrl = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->body,
            'icon' => $this->icon,
            'level' => $this->type,
            'category' => $this->category,
            'action_url' => $this->actionUrl,
        ];
    }
}
