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

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O pedido a ser confirmado.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * O número de tentativas para o job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verifica se o pedido ainda está pago, para evitar envio de e-mail caso o status tenha mudado
            if ($this->order->status !== 'paid') {
                Log::channel('email')->info('E-mail de confirmação não enviado para o pedido ' . $this->order->reference . ' pois o status é ' . $this->order->status);
                return;
            }
            
            // Carrega as relações necessárias
            $this->order->load(['items.product', 'user']);

            // Prepara os dados para o e-mail
            $mailData = [
                'order' => $this->order,
                'customer' => $this->order->user,
                'items' => $this->order->items,
                'shipping' => [
                    'address' => $this->order->shippingAddress,
                    'method' => $this->order->shipping_method,
                    'price' => $this->order->shipping_price,
                ],
                'payment' => [
                    'method' => $this->order->payment_method === 'credit_card' ? 'Cartão de Crédito' : 'PIX',
                    'total' => $this->order->total,
                ],
                'store' => [
                    'name' => config('app.name'),
                    'email' => config('mail.from.address'),
                    'phone' => config('app.store_phone', '(00) 0000-0000'),
                ]
            ];

            // Enviar o e-mail
            Mail::to($this->order->user->email)
                ->send(new \App\Mail\OrderConfirmation($mailData));
                
            // Registrar no histórico de status que o cliente foi notificado
            $this->order->statusHistory()->create([
                'status' => $this->order->status,
                'description' => 'E-mail de confirmação enviado',
                'is_customer_notified' => true
            ]);

            Log::channel('email')->info('E-mail de confirmação enviado para o pedido ' . $this->order->reference);
            
        } catch (\Exception $e) {
            Log::channel('email')->error('Erro ao enviar e-mail de confirmação para o pedido ' . $this->order->reference . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
