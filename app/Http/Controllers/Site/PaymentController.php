<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Mostra a página do boleto
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showBoletoPage($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $boletoUrl = session('boleto_url');

        if (!$boletoUrl) {
            return redirect()->route('site.orders.show', $order->id)
                ->with('error', 'Link do boleto não encontrado.');
        }

        return view('site.payments.boleto', compact('order', 'boletoUrl'));
    }

    /**
     * Mostra a página do PIX
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showPixPage($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Recuperar dados do PIX da sessão
        $pixQrCode = session('pix_qrcode');
        $pixCode = session('pix_code');
        $pixId = session('pix_id');

        if (!$pixQrCode) {
            return redirect()->route('site.orders.show', $order->id)
                ->with('error', 'QR Code do PIX não encontrado.');
        }

        return view('site.payments.pix', compact('order', 'pixQrCode', 'pixCode', 'pixId'));
    }
    
    /**
     * Verifica o status do pagamento PIX
     *
     * @param int $orderId
     * @param string $pixId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPixStatus($orderId, $pixId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso não autorizado'], 403);
        }
        
        // Consultar o status do PIX na Rede Itaú
        $redeItauService = app(\App\Services\RedeItauService::class);
        $pixStatus = $redeItauService->getPixTransaction($pixId);
        
        if ($pixStatus['success']) {
            $status = $pixStatus['data']['status'] ?? 'pending';
            
            // Se estiver pago, atualizar o pedido
            if ($status === 'approved' || $status === 'paid') {
                $order->payment_status = 'paid';
                $order->updateStatus('processing', 'Pagamento confirmado. Pedido em processamento.', null);
                
                return response()->json([
                    'status' => 'paid',
                    'message' => 'Pagamento confirmado!'
                ]);
            }
            
            return response()->json([
                'status' => $status,
                'message' => 'Aguardando pagamento'
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Erro ao verificar status do pagamento'
        ]);
    }
}
