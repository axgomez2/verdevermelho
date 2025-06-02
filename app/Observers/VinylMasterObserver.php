<?php

namespace App\Observers;

use App\Models\User;
use App\Models\VinylMaster;
use App\Models\Wantlist;
use App\Notifications\WantlistItemAvailableNotification;
use Illuminate\Support\Facades\DB;

class VinylMasterObserver
{
    /**
     * Handle the VinylMaster "updated" event.
     *
     * @param  \App\Models\VinylMaster  $vinylMaster
     * @return void
     */
    public function updated(VinylMaster $vinylMaster)
    {
        // Verificar se o disco se tornou disponível
        $wasAvailable = false;
        $isAvailable = false;

        // Se o vinylSec foi carregado nas alterações
        if ($vinylMaster->vinylSec && $vinylMaster->wasChanged('vinylSec')) {
            $originalVinylSec = $vinylMaster->getOriginal('vinylSec');
            
            if ($originalVinylSec) {
                $wasAvailable = $originalVinylSec->in_stock && $originalVinylSec->quantity > 0;
            }
            
            $isAvailable = $vinylMaster->vinylSec->in_stock && $vinylMaster->vinylSec->quantity > 0;
        }

        // Se não foi carregado nas alterações, verifique diretamente
        if (!$wasAvailable && !$isAvailable && $vinylMaster->vinylSec) {
            $isAvailable = $vinylMaster->vinylSec->in_stock && $vinylMaster->vinylSec->quantity > 0;
            
            // Se não conseguimos determinar se estava disponível antes, vamos verificar 
            // se já enviamos alguma notificação para este disco nas últimas 24 horas
            // Se não enviamos, consideramos que não estava disponível
            $recentNotificationSent = DB::table('notifications')
                ->where('type', 'App\\Notifications\\WantlistItemAvailableNotification')
                ->where('data', 'like', '%"vinyl_id":' . $vinylMaster->id . '%')
                ->where('created_at', '>', now()->subHours(24))
                ->exists();
                
            $wasAvailable = $recentNotificationSent;
        }

        // Se o produto se tornou disponível
        if ($isAvailable && !$wasAvailable) {
            $this->notifyWantlistUsers($vinylMaster);
        }
    }

    /**
     * Notifica usuários que têm este disco em suas wantlists.
     *
     * @param  \App\Models\VinylMaster  $vinylMaster
     * @return void
     */
    protected function notifyWantlistUsers(VinylMaster $vinylMaster)
    {
        // Encontrar todos os usuários que têm este disco em suas wantlists
        $usersWithWantlist = Wantlist::where('product_id', $vinylMaster->id)
            ->where('product_type', 'App\\Models\\VinylMaster')
            ->pluck('user_id')
            ->unique();

        // Enviar notificações para cada usuário
        foreach ($usersWithWantlist as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->notify(new WantlistItemAvailableNotification($vinylMaster));
            }
        }
    }
}
