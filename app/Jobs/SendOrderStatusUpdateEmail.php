<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O pedido a ser atualizado.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * O status do pedido.
     *
     * @var string
     */
    protected $status;

    /**
     * A descrição da atualização.
     *
     * @var string
     */
    protected $description;

    /**
     * O número de tentativas para o job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     * 
     * @param \App\Models\Order $order
     * @param string $status
     * @param string $description
     */
    public function __construct(Order $order, string $status, string $description)
    {
        $this->order = $order;
        $this->status = $status;
        $this->description = $description;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Carrega as relações necessárias
            $this->order->load(['items.product', 'user', 'shippingAddress']);

            // Mapeia o status para um texto amigável
            $statusText = $this->getStatusText($this->status);
            
            // Prepara os dados para o e-mail
            $mailData = [
                'order' => $this->order,
                'status' => $this->status,
                'statusText' => $statusText,
                'description' => $this->description,
                'customer' => $this->order->user,
                'store' => [
                    'name' => config('app.name'),
                    'email' => config('mail.from.address'),
                    'phone' => config('app.store_phone', '(00) 0000-0000'),
                ]
            ];

            // Envia o e-mail
            Mail::to($this->order->user->email)
                ->send(new \App\Mail\OrderStatusUpdate($mailData));
                
            Log::channel('email')->info('E-mail de atualização de status enviado para o pedido ' . $this->order->reference);
            
        } catch (\Exception $e) {
            Log::channel('email')->error('Erro ao enviar e-mail de atualização de status para o pedido ' . $this->order->reference . ': ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Converte o status para um texto amigável.
     *
     * @param string $status
     * @return string
     */
    private function getStatusText(string $status): string
    {
        $statusMap = [
            'pending' => 'Aguardando Pagamento',
            'paid' => 'Pagamento Confirmado',
            'processing' => 'Em Processamento',
            'shipped' => 'Pedido Enviado',
            'delivered' => 'Pedido Entregue',
            'canceled' => 'Pedido Cancelado',
            'refunded' => 'Pagamento Reembolsado'
        ];
        
        return $statusMap[$status] ?? ucfirst($status);
    }
}
