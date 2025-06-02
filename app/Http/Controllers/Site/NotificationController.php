<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Exibir todas as notificações do usuário
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('site.notifications.index', compact('notifications'));
    }

    /**
     * Marcar uma notificação específica como lida
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        // Redirecionar para a URL armazenada na notificação, se existir
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
        
        return back()->with('success', 'Notificação marcada como lida.');
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Verificar notificações via AJAX (para atualização em tempo real)
     */
    public function check()
    {
        $unreadCount = Auth::user()->unreadNotifications->count();
        $recentNotifications = Auth::user()->notifications()->take(5)->get();
        
        $formattedNotifications = $recentNotifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'] ?? 'Nova notificação',
                'url' => $notification->data['url'] ?? null,
                'time' => $notification->created_at->diffForHumans(),
                'read' => !is_null($notification->read_at),
                'image' => isset($notification->data['cover_image']) 
                    ? asset('storage/' . $notification->data['cover_image']) 
                    : null,
            ];
        });
        
        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $formattedNotifications
        ]);
    }
}
