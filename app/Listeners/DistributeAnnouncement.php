<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GlobalAnnouncementNotification;

class DistributeAnnouncement implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(AnnouncementCreated $event): void
    {
        // Distribute to all students in chunks for memory efficiency
        User::where('tipo', 'aluno')
            ->chunk(100, function ($students) use ($event) {
                Notification::send($students, new GlobalAnnouncementNotification(
                    title: $event->title,
                    body: $event->body,
                    icon: $event->icon,
                    type: $event->type,
                    category: $event->category,
                    actionUrl: $event->actionUrl
                ));
            });
    }
}
