<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Obter todas as notificações do usuário
     * 
     * @param User $user
     * @param bool $unreadOnly
     * @param int $limit
     * @return Collection
     */
    public function getUserNotifications(User $user, bool $unreadOnly = false, int $limit = 20): Collection
    {
        $query = $unreadOnly ? $user->unreadNotifications() : $user->notifications();

        return $query->take($limit)->get()->map(function ($notification) {
            $data = $notification->data;
            
            return (object) [
                'id' => $notification->id,
                'type' => 'notification',
                'title' => $data['title'] ?? 'Notificação',
                'message' => $data['message'] ?? '',
                'category' => $data['category'] ?? 'individual',
                'level' => $data['level'] ?? 'info',
                'icon' => $data['icon'] ?? 'bell',
                'action_url' => $data['action_url'] ?? null,
                'is_read' => $notification->read_at !== null,
                'is_urgent' => $data['urgent'] ?? false,
                'created_at' => $notification->created_at,
            ];
        });
    }

    /**
     * Contar notificações não lidas
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead(User $user, string $id): bool
    {
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Obter notificações formatadas para JSON (API)
     */
    public function getNotificationsJson(User $user, ?string $category = null): array
    {
        $notifications = $this->getUserNotifications($user, false, 30);

        // Filtrar por categoria se especificado
        if ($category && $category !== 'all') {
            $notifications = $notifications->filter(fn($n) => $n->category === $category);
        }

        return [
            'notifications' => $notifications->values()->toArray(),
            'unread_count' => $this->getUnreadCount($user),
        ];
    }
}
