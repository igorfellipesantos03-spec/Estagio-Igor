<?php

namespace App\Notifications;

use App\Models\Grupo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupMediaRemovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Grupo $grupo
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Imagem do Grupo Removida',
            'message' => 'Um professor removeu a imagem do seu grupo "' . $this->grupo->nome . '". Por favor, insira uma nova imagem caso desejado.',
            'category' => 'individual',
            'level' => 'warning',
            'icon' => 'photograph',
            'action_url' => route('aluno.hackathons.index'),
            'hackathon_id' => $this->grupo->hackathon_id,
        ];
    }
}
