<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\RedeItauService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O número de tentativas para o job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * O número de segundos que o job pode ser processado.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(RedeItauService $redeItauService): void
    {
        try {
            Log::channel('payment')->info('Iniciando verificação de pagamentos pendentes');
            
            // Buscar pedidos pendentes criados há mais de 5 minutos e menos de 48 horas
            $pendingOrders = Order::where('status', 'pending')
                ->where('payment_method', 'credit_card')
                ->where('created_at', '>', now()->subHours(48))
                ->where('created_at', '<', now()->subMinutes(5))
                ->whereNotNull('payment_tid')
                ->get();
            
            Log::channel('payment')->info('Encontrados ' . $pendingOrders->count() . ' pedidos pendentes para verificação');
            
            foreach ($pendingOrders as $order) {
                try {
                    // Verifica o status do pagamento na Rede Itaú
                    $paymentInfo = $redeItauService->getTransaction($order->payment_tid);
                    
                    if (!$paymentInfo) {
                        Log::channel('payment')->warning('Não foi possível obter informações do pagamento para o pedido ' . $order->reference);
                        continue;
                    }
                    
                    // Atualiza o status do pedido com base no status do pagamento
                    $this->updateOrderStatus($order, $paymentInfo, $redeItauService);
                    
                } catch (\Exception $e) {
                    Log::channel('payment')->error('Erro ao verificar pagamento do pedido ' . $order->reference . ': ' . $e->getMessage());
                }
            }
            
            // Verificar pedidos PIX pendentes
            $pendingPixOrders = Order::where('status', 'pending')
                ->where('payment_method', 'pix')
                ->where('created_at', '>', now()->subHours(24)) // PIX expira em 24 horas
                ->where('created_at', '<', now()->subMinutes(5))
                ->whereNotNull('payment_tid')
                ->get();
                
            Log::channel('payment')->info('Encontrados ' . $pendingPixOrders->count() . ' pedidos PIX pendentes para verificação');
            
            foreach ($pendingPixOrders as $order) {
                try {
                    // Verifica o status do pagamento PIX na Rede Itaú
                    $paymentInfo = $redeItauService->getPixTransaction($order->payment_tid);
                    
                    if (!$paymentInfo) {
                        Log::channel('payment')->warning('Não foi possível obter informações do pagamento PIX para o pedido ' . $order->reference);
                        continue;
                    }
                    
                    // Atualiza o status do pedido com base no status do pagamento
                    $this->updateOrderStatus($order, $paymentInfo, $redeItauService);
                    
                } catch (\Exception $e) {
                    Log::channel('payment')->error('Erro ao verificar pagamento PIX do pedido ' . $order->reference . ': ' . $e->getMessage());
                }
            }
            
            Log::channel('payment')->info('Verificação de pagamentos pendentes concluída');
            
        } catch (\Exception $e) {
            Log::channel('payment')->error('Erro na verificação de pagamentos pendentes: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Atualiza o status do pedido com base no status do pagamento
     * 
     * @param Order $order
     * @param array $paymentInfo
     * @param RedeItauService $redeItauService
     * @return void
     */
    private function updateOrderStatus(Order $order, array $paymentInfo, RedeItauService $redeItauService)
    {
        // Mapear status da Rede Itaú para status interno do sistema
        $statusMap = [
            'approved' => 'paid',
            'approved_pending_capture' => 'processing',
            'captured' => 'paid',
            'denied' => 'canceled',
            'refunded' => 'refunded',
            'pending' => 'pending',
            'authorized' => 'processing',
            'chargeback' => 'chargeback'
        ];
        
        $paymentStatus = $paymentInfo['status'] ?? 'pending';
        $newStatus = $statusMap[$paymentStatus] ?? 'pending';
        
        // Se o status não mudou, não fazemos nada
        if ($order->status === $newStatus) {
            return;
        }
        
        Log::channel('payment')->info('Atualizando status do pedido ' . $order->reference . ' de ' . $order->status . ' para ' . $newStatus);
        
        // Atualiza o status do pedido
        $order->status = $newStatus;
        $order->save();
        
        // Registra no histórico
        $order->statusHistory()->create([
            'status' => $newStatus,
            'description' => "Status atualizado para {$newStatus} via verificação periódica (TID: {$order->payment_tid})",
            'is_customer_notified' => false
        ]);
        
        // Ações adicionais baseadas no novo status
        if ($newStatus === 'paid' && $order->getOriginal('status') !== 'paid') {
            // Pedido foi confirmado agora
            $this->processPostPaymentActions($order);
        }
    }
    
    /**
     * Processa ações pós-pagamento
     * 
     * @param Order $order
     * @return void
     */
    private function processPostPaymentActions(Order $order)
    {
        try {
            // Reduzir estoque
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }
            
            // Notificar cliente
            dispatch(new SendOrderConfirmationEmail($order));
            
            Log::channel('payment')->info('Ações pós-pagamento processadas para o pedido ' . $order->reference);
        } catch (\Exception $e) {
            Log::channel('payment')->error('Erro ao processar ações pós-pagamento: ' . $e->getMessage());
        }
    }
}
