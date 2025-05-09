<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Exibe a lista de pedidos do cliente
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['statusHistory' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('site.customer.orders.index', compact('orders'));
    }
    
    /**
     * Exibe os detalhes de um pedido específico
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Auth::user()->orders()
            ->with(['items.product', 'statusHistory' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'shippingAddress'])
            ->findOrFail($id);
            
        return view('site.customer.orders.show', compact('order'));
    }
    
    /**
     * Gera uma segunda via do boleto ou link de pagamento
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentLink($id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        
        // Verifica se o pedido está pendente
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Só é possível gerar segunda via para pedidos pendentes.');
        }
        
        // De acordo com o método de pagamento, gera a segunda via
        if ($order->payment_method === 'pix') {
            // TODO: Implementar geração de nova chave PIX
            $paymentUrl = '#'; // Aqui seria a URL para o PIX
        } else {
            $paymentUrl = route('site.checkout.review', ['order' => $order->id]);
        }
        
        return redirect($paymentUrl);
    }
    
    /**
     * Permite ao cliente cancelar um pedido pendente
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        
        // Verifica se o pedido pode ser cancelado
        if ($order->status !== 'pending' && $order->status !== 'processing') {
            return redirect()->back()->with('error', 'Este pedido não pode ser cancelado.');
        }
        
        // Atualiza o status para cancelado
        $order->status = 'canceled';
        $order->save();
        
        // Adiciona ao histórico
        $order->statusHistory()->create([
            'status' => 'canceled',
            'description' => 'Pedido cancelado pelo cliente',
            'is_customer_notified' => true
        ]);
        
        return redirect()->route('site.customer.orders.index')->with('success', 'Pedido cancelado com sucesso.');
    }
    
    /**
     * Permite ao cliente confirmar o recebimento do pedido
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmReceipt($id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        
        // Verifica se o pedido está em transporte
        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'Só é possível confirmar o recebimento de pedidos em transporte.');
        }
        
        // Atualiza o status para entregue
        $order->status = 'delivered';
        $order->save();
        
        // Adiciona ao histórico
        $order->statusHistory()->create([
            'status' => 'delivered',
            'description' => 'Entrega confirmada pelo cliente',
            'is_customer_notified' => true
        ]);
        
        return redirect()->route('site.customer.orders.show', $order->id)->with('success', 'Recebimento confirmado com sucesso.');
    }
}
