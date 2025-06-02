<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wantlist;
use App\Models\VinylMaster;
use App\Notifications\WantlistItemAvailableNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWantlistAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wantlist:check-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica discos na wantlist que ficaram disponíveis e notifica os usuários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando itens da wantlist que ficaram disponíveis...');
        
        // Encontrar todos os itens da wantlist que sejam discos (VinylMaster)
        $wantlistItems = Wantlist::where('product_type', 'App\\Models\\VinylMaster')
            ->select('user_id', 'product_id')
            ->get();
            
        if ($wantlistItems->isEmpty()) {
            $this->info('Nenhum item na wantlist encontrado.');
            return 0;
        }
        
        $this->info('Encontrados ' . $wantlistItems->count() . ' itens na wantlist.');
        
        // Agrupar por usuário para evitar consultas repetidas
        $itemsByUser = $wantlistItems->groupBy('user_id');
        $notifiedCount = 0;
        
        foreach ($itemsByUser as $userId => $items) {
            $user = User::find($userId);
            if (!$user) continue;
            
            // Coletar todos os IDs de discos para este usuário
            $vinylIds = $items->pluck('product_id')->toArray();
            
            // Verificar quais discos estão agora disponíveis
            $availableVinyls = VinylMaster::with(['artists', 'vinylSec'])
                ->whereIn('id', $vinylIds)
                ->whereHas('vinylSec', function ($query) {
                    $query->where('in_stock', 1)
                          ->where('quantity', '>', 0);
                })
                ->get();
                
            if ($availableVinyls->isEmpty()) {
                continue;
            }
            
            foreach ($availableVinyls as $vinyl) {
                // Verificar se já notificamos sobre este item
                $notificationSent = DB::table('notifications')
                    ->where('notifiable_id', $userId)
                    ->where('type', 'App\\Notifications\\WantlistItemAvailableNotification')
                    ->where('data', 'like', '%"vinyl_id":' . $vinyl->id . '%')
                    ->where('created_at', '>', now()->subDays(7)) // Não notificar novamente se já enviamos na última semana
                    ->exists();
                    
                if (!$notificationSent) {
                    // Enviar notificação
                    $user->notify(new WantlistItemAvailableNotification($vinyl));
                    $notifiedCount++;
                    
                    $this->info("Notificação enviada para {$user->name} sobre o disco {$vinyl->title}");
                }
            }
        }
        
        $this->info("Total de {$notifiedCount} notificações enviadas.");
        
        return 0;
    }
}
