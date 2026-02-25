<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnnouncementCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $title,
        public string $body,
        public string $icon = 'megaphone',
        public string $type = 'info',
        public string $category = 'general',
        public ?string $actionUrl = null,
        public ?\DateTime $expiresAt = null
    ) {}
}
