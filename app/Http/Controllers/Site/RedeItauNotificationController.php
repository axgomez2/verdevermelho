<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\RedeItauService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedeItauNotificationController extends Controller
{
    protected $redeItauService;

    public function __construct(RedeItauService $redeItauService)
    {
        $this->redeItauService = $redeItauService;
    }

    /**
     * Processa a notificação da Rede Itaú
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleNotification(Request $request)
    {
        Log::channel('payment')->info('Notificação Rede Itaú recebida', $request->all());

        try {
            // Obtém os dados da notificação
            $tid = $request->input('tid');
            $reference = $request->input('reference');
            $status = $request->input('status');

            if (!$tid || !$reference) {
                Log::channel('payment')->error('Dados insuficientes na notificação', $request->all());
                return response()->json(['message' => 'Dados insuficientes'], 400);
            }

            // Encontra o pedido pelo número de referência
            $order = Order::where('reference', $reference)->first();
            
            if (!$order) {
                Log::channel('payment')->error('Pedido não encontrado: ' . $reference);
                return response()->json(['message' => 'Pedido não encontrado'], 404);
            }

            // Atualiza o status do pedido
            $this->updateOrderStatus($order, $status, $tid);

            return response()->json(['message' => 'Notificação processada com sucesso']);
        } catch (\Exception $e) {
            Log::channel('payment')->error('Erro ao processar notificação: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao processar notificação'], 500);
        }
    }

    /**
     * Atualiza o status do pedido com base no status do pagamento
     *
     * @param Order $order
     * @param string $status
     * @param string $tid
     * @return void
     */
    private function updateOrderStatus(Order $order, $status, $tid)
    {
        // Mapear status da Rede Itaú para status interno do sistema
        $statusMap = [
            'approved' => 'paid',
            'denied' => 'canceled',
            'refunded' => 'refunded',
            'pending' => 'pending',
            'chargeback' => 'chargeback'
        ];

        $newStatus = $statusMap[$status] ?? 'pending';
        
        // Registrar transação e status
        $order->payment_tid = $tid;
        $order->status = $newStatus;
        $order->save();

        // Registrar histórico de status
        $order->statusHistory()->create([
            'status' => $newStatus,
            'description' => "Pagamento {$status} via Rede Itaú (TID: {$tid})",
            'is_customer_notified' => false
        ]);

        // Se o pagamento foi aprovado, processar ações pós-pagamento
        if ($newStatus === 'paid') {
            $this->processPostPaymentActions($order);
        }
    }

    /**
     * Processa ações pós-pagamento (estoque, notificações, etc)
     *
     * @param Order $order
     * @return void
     */
    private function processPostPaymentActions(Order $order)
    {
        // Reduzir estoque
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock -= $item->quantity;
            $product->save();
        }

        // Enviar notificação por email
        // Usar uma job para não bloquear o processamento do webhook
        dispatch(new \App\Jobs\SendOrderConfirmationEmail($order));
    }
}
