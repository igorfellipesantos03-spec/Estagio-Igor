<?php

namespace App\Notifications;

use App\Models\Hackathon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WinnerNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Hackathon $hackathon
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '🏆 Parabéns! Vocês venceram!',
            'message' => 'Seu grupo foi o vencedor do hackathon "' . $this->hackathon->nome . '"! Vocês ganharam 1000 XP como premiação.',
            'category' => 'individual',
            'level' => 'success',
            'icon' => 'trophy',
            'action_url' => route('aluno.hackathons.index'),
            'hackathon_id' => $this->hackathon->id,
        ];
    }
}
