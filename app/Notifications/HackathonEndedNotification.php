<?php

namespace App\Notifications;

use App\Models\Hackathon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HackathonEndedNotification extends Notification
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
        $winnerGroupName = $this->hackathon->winnerGroup?->nome ?? 'Não informado';
        
        return [
            'title' => '📢 Hackathon Encerrado',
            'message' => 'O hackathon "' . $this->hackathon->nome . '" foi encerrado. Grupo vencedor: ' . $winnerGroupName . '. Você ganhou 200 XP pela participação!',
            'category' => 'individual',
            'level' => 'info',
            'icon' => 'flag',
            'action_url' => route('aluno.hackathons.index'),
            'hackathon_id' => $this->hackathon->id,
        ];
    }
}
