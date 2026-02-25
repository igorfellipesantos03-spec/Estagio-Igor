<?php

namespace Database\Seeders;

use App\Models\GamificationEvent;
use Illuminate\Database\Seeder;

class GamificationEventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            // Participação (pontos baixos)
            [
                'key' => 'presence_confirmed',
                'name' => 'Presença Confirmada',
                'points' => 200,
                'icon' => 'check-circle',
                'description' => 'Pontos por ter presença validada em um hackathon.',
            ],
            [
                'key' => 'group_created',
                'name' => 'Grupo Criado',
                'points' => 100,
                'icon' => 'user-group',
                'description' => 'Pontos por criar um grupo para hackathon.',
            ],
            [
                'key' => 'group_joined',
                'name' => 'Entrou em Grupo',
                'points' => 50,
                'icon' => 'user-plus',
                'description' => 'Pontos por entrar em um grupo existente.',
            ],
            
            // Vitórias (pontos altos)
            [
                'key' => 'hackathon_win_1st',
                'name' => '1º Lugar no Hackathon',
                'points' => 2000,
                'icon' => 'trophy',
                'description' => 'Campeão do hackathon!',
            ],
            [
                'key' => 'hackathon_win_2nd',
                'name' => '2º Lugar no Hackathon',
                'points' => 1000,
                'icon' => 'medal',
                'description' => 'Vice-campeão do hackathon.',
            ],
            [
                'key' => 'hackathon_win_3rd',
                'name' => '3º Lugar no Hackathon',
                'points' => 500,
                'icon' => 'medal',
                'description' => 'Terceiro lugar no hackathon.',
            ],

            // Eventos de finalização
            [
                'key' => 'hackathon_winner',
                'name' => 'Vencedor do Hackathon',
                'points' => 1000,
                'icon' => 'trophy',
                'description' => 'Pontos por vencer o hackathon.',
            ],
            [
                'key' => 'hackathon_participation',
                'name' => 'Participação em Hackathon',
                'points' => 200,
                'icon' => 'star',
                'description' => 'Pontos por participar de um hackathon finalizado.',
            ],
        ];

        foreach ($events as $event) {
            GamificationEvent::updateOrCreate(
                ['key' => $event['key']],
                $event
            );
        }
    }
}
