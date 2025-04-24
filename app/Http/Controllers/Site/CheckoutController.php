<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Services\PagSeguroService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $pagSeguroService;

    public function __construct(PagSeguroService $pagSeguroService)
    {
        $this->pagSeguroService = $pagSeguroService;
    }

    public function index(Request $request)
    {
        $cart = $request->user()->cart ?? Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // Obter a sessão do PagSeguro
        $sessionId = $this->pagSeguroService->getSessionId();
        if (!$sessionId) {
            return redirect()->route('site.cart.index')->with('error', 'Não foi possível iniciar a sessão de pagamento. Tente novamente mais tarde.');
        }

        // Calcular valores
        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        $shippingCost = session('shipping_cost', 0);
        $tax = $subtotal * 0.1; // 10% de imposto
        $total = $subtotal + $shippingCost + $tax;

        return view('site.checkout.index', compact('cart', 'sessionId', 'subtotal', 'shippingCost', 'tax', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string'
        ]);

        $cart = $request->user()->cart ?? Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        DB::beginTransaction();

        try {
            // Verificar estoque final
            foreach ($cart->items as $item) {
                $vinylSec = $item->product->productable->vinylSec;
                if ($vinylSec->quantity < $item->quantity) {
                    throw new \Exception("Estoque insuficiente para o produto: {$item->product->name}");
                }
            }

            // Calcular valores
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            $shippingCost = session('shipping_cost', 0);
            $tax = $subtotal * 0.1; // 10% de imposto
            $total = $subtotal + $shippingCost + $tax;

            // Criar pedido
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->shipping_address_id, // Usando o mesmo endereço para faturamento
                'notes' => $request->notes ?? null
            ]);

            // Criar itens do pedido e atualizar estoque
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $vinylSec = $item->product->productable->vinylSec;
                $vinylSec->quantity -= $item->quantity;
                $vinylSec->save();
            }

            // Processar pagamento de acordo com o método selecionado
            $paymentResult = null;

            switch ($request->payment_method) {
                case 'credit_card':
                    $cardData = [
                        'token' => $request->card_token,
                        'installments' => $request->installments,
                        'holder' => $request->card_holder_name,
                        'cpf' => $request->card_holder_cpf,
                        'birth_date' => $request->card_holder_birth_date,
                    ];
                    $paymentResult = $this->pagSeguroService->processCreditCardPayment($order, $cardData);
                    break;

                case 'boleto':
                    $paymentResult = $this->pagSeguroService->processBoletoPayment($order);
                    break;

                case 'pix':
                    $paymentResult = $this->pagSeguroService->processPixPayment($order);
                    break;

                default:
                    throw new \Exception('Método de pagamento não suportado.');
            }

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message']);
            }

            // Atualizar pedido com informações do pagamento
            $order->transaction_code = $paymentResult['transaction_code'] ?? null;
            $order->payment_method = $request->payment_method;
            $order->save();

            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // Redirecionar com base no método de pagamento
            if ($request->payment_method === 'boleto' && isset($paymentResult['boleto_url'])) {
                return redirect()->route('site.payments.boleto', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Efetue o pagamento do boleto para completar a compra.')
                    ->with('boleto_url', $paymentResult['boleto_url']);
            } else if ($request->payment_method === 'pix' && isset($paymentResult['qr_code_url'])) {
                return redirect()->route('site.payments.pix', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Escaneie o QR Code para completar a compra.')
                    ->with('qr_code_url', $paymentResult['qr_code_url']);
            } else {
                return redirect()->route('site.orders.show', $order)
                    ->with('success', 'Pedido realizado com sucesso! ' . ($paymentResult['message'] ?? ''));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no checkout: ' . $e->getMessage());
            return redirect()->route('site.checkout.index')->with('error', $e->getMessage());
        }
    }
}
