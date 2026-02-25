<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Listar notificações do usuário (API JSON)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $category = $request->query('category');

        $data = $this->notificationService->getNotificationsJson($user, $category);

        return response()->json($data);
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        $user = Auth::user();
        $success = $this->notificationService->markAsRead($user, $request->id);

        if ($success) {
            return response()->json([
                'success' => true,
                'unread_count' => $this->notificationService->getUnreadCount($user),
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Marcar todas como lidas
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Obter contagem de não lidas
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['unread_count' => $count]);
    }
}
