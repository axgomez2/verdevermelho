<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Services\RedeItauService;
use App\Http\Controllers\Site\CartController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $redeItauService;

    public function __construct(RedeItauService $redeItauService)
    {
        $this->redeItauService = $redeItauService;
    }

    public function index(Request $request)
    {
        // Obter o carrinho através do CartController para manter consistência
        $cartController = app(CartController::class);
        $cart = $cartController->getOrCreateCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }
        
        // Verificar se um frete foi selecionado
        $shippingPostalCode = session('shipping_postal_code');
        $selectedShippingOption = session('selected_shipping_option');
        
        if (!$shippingPostalCode || !$selectedShippingOption) {
            return redirect()->route('site.cart.index')->with('error', 'Por favor, calcule e selecione uma opção de frete antes de finalizar a compra.');
        }

        // Calcular valores
        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        $shippingCost = session('selected_shipping_price', 0);
        $tax = 0; // Removido os impostos conforme solicitado
        $total = $subtotal + $shippingCost;
        
        // Obter endereços do usuário - carregando explicitamente para garantir que seja uma coleção
        $addresses = $request->user()->addresses()->get();
        
        // Obter opções de frete disponíveis
        $shippingOptions = [];
        if ($shippingPostalCode) {
            $shippingOptions = session('shipping_options', []);
        }

        return view('site.checkout.index', compact('cart', 'subtotal', 'shippingCost', 'tax', 'total', 'addresses', 'shippingOptions'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string',
            'installments' => 'required_if:payment_method,credit_card|integer|min:1|max:12',
            'card_holder_name' => 'required_if:payment_method,credit_card|string|max:255',
            'card_number' => 'required_if:payment_method,credit_card|string|min:13|max:19',
            'card_expiry_month' => 'required_if:payment_method,credit_card|string|size:2',
            'card_expiry_year' => 'required_if:payment_method,credit_card|string|size:4',
            'card_cvv' => 'required_if:payment_method,credit_card|string|min:3|max:4',
        ]);
        
        // Verificar se um frete foi selecionado
        if (!session('shipping_postal_code') || !session('selected_shipping_option')) {
            return redirect()->route('site.cart.index')
                ->with('error', 'Por favor, calcule e selecione uma opção de frete antes de finalizar a compra.');
        }

        // Obter o carrinho através do CartController para manter consistência
        $cartController = app(CartController::class);
        $cart = $cartController->getOrCreateCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        DB::beginTransaction();

        try {
            // Verificar estoque final
            foreach ($cart->items as $item) {
                if (isset($item->product->productable) && isset($item->product->productable->vinylSec)) {
                    $vinylSec = $item->product->productable->vinylSec;
                    if ($vinylSec->quantity < $item->quantity) {
                        throw new \Exception("Estoque insuficiente para o produto: {$item->product->productable->title}");
                    }
                }
            }

            // Calcular valores
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            $shippingCost = session('selected_shipping_price', 0);
            $tax = 0; // Removido os impostos conforme solicitado
            $total = $subtotal + $shippingCost;

            // Obter informações de frete selecionado na sessão
            $shippingServiceId = session('selected_shipping_option');
            $shippingServiceName = session('selected_shipping_name');
            $shippingDeliveryTime = null;
            
            // Se temos um serviço de frete selecionado, vamos obter o tempo de entrega
            if ($shippingServiceId) {
                $postalCode = session('shipping_postal_code');
                $melhorEnvioService = app(\App\Services\MelhorEnvioService::class);
                $shippingOptions = $melhorEnvioService->calculateShipping($cart->items, $postalCode);
                
                if (!empty($shippingOptions)) {
                    $selectedShipping = collect($shippingOptions)->firstWhere('id', $shippingServiceId);
                    if ($selectedShipping) {
                        $shippingDeliveryTime = $selectedShipping['delivery_time'] ?? null;
                    }
                }
            }
            
            // Gerar um código de referência para o pedido
            $reference = 'INV-' . strtoupper(Str::random(8));
            
            // Criar o pedido
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total,
                'shipping_cost' => $shippingCost,
                'shipping_service_id' => $shippingServiceId,
                'shipping_service_name' => $shippingServiceName,
                'shipping_delivery_time' => $shippingDeliveryTime,
                'tax' => $tax,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->shipping_address_id, // Usando o mesmo endereço para faturamento
                'notes' => $request->notes ?? null,
                'transaction_code' => $reference
            ]);
            
            // Registrar o status inicial no histórico
            $order->statusHistory()->create([
                'status' => 'pending',
                'comment' => 'Pedido criado. Aguardando pagamento.',
                'created_by' => $request->user()->id
            ]);

            // Criar itens do pedido e atualizar estoque
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $item->quantity * $item->product->price
                ]);

                // Atualizar estoque apenas se for vinil secundário (que tem controle de estoque)
                if (isset($item->product->productable) && isset($item->product->productable->vinylSec)) {
                    $vinylSec = $item->product->productable->vinylSec;
                    $vinylSec->quantity -= $item->quantity;
                    $vinylSec->save();
                }
            }
            
            // Obter o endereço de entrega para processar o pagamento
            $shippingAddress = $request->user()->addresses()->find($request->shipping_address_id);
            
            if (!$shippingAddress) {
                throw new \Exception('Endereço de entrega inválido.');
            }

            // Processar pagamento de acordo com o método selecionado
            $paymentResult = null;

            switch ($request->payment_method) {
                case 'credit_card':
                    // Obter dados do cliente e endereço para processar o pagamento
                    $customer = [
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'document' => $request->user()->cpf ?? null,
                        'phone' => $request->user()->phone ?? null
                    ];
                    
                    $address = [
                        'street' => $shippingAddress->street,
                        'number' => $shippingAddress->number,
                        'complement' => $shippingAddress->complement,
                        'zip_code' => $shippingAddress->zip_code,
                        'city' => $shippingAddress->city,
                        'state' => $shippingAddress->state,
                        'country' => 'Brasil'
                    ];
                    
                    // Preparar dados para a API da Rede Itaú
                    $paymentData = [
                        'reference' => $reference,
                        'amount' => $total,
                        'card_holder_name' => $request->card_holder_name,
                        'card_number' => preg_replace('/\D/', '', $request->card_number),
                        'expiration_month' => $request->card_expiry_month,
                        'expiration_year' => $request->card_expiry_year,
                        'security_code' => $request->card_cvv,
                        'installments' => $request->installments,
                        'customer' => $customer,
                        'address' => $address,
                        'auto_capture' => true, // Capturar automaticamente
                        'soft_descriptor' => config('app.name')
                    ];
                    
                    $paymentResult = $this->redeItauService->authorize($paymentData);
                    break;

                case 'pix':
                    // Obter dados do cliente e endereço para processar o pagamento
                    $customer = [
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'document' => $request->user()->cpf ?? null,
                        'phone' => $request->user()->phone ?? null
                    ];
                    
                    // Preparar dados para a API da Rede Itaú
                    $pixData = [
                        'reference' => $reference,
                        'amount' => $total,
                        'customer' => $customer,
                        'soft_descriptor' => config('app.name'),
                        'expiration' => 3600 // Tempo em segundos (1 hora)
                    ];
                    
                    // Criar transação PIX via serviço da Rede Itaú
                    $paymentResult = $this->redeItauService->createPix($pixData);
                    
                    // Se bem-sucedido, extrair informações importantes
                    if ($paymentResult['success']) {
                        // Obter QR code e código PIX da resposta
                        $pixQrCode = $paymentResult['data']['pix']['qrcode'] ?? '';
                        $pixEmv = $paymentResult['data']['pix']['emv'] ?? '';
                        $pixId = $paymentResult['data']['pix']['id'] ?? '';
                        
                        // Armazenar na sessão para mostrar na página de pagamento
                        session([
                            'pix_qrcode' => $pixQrCode,
                            'pix_code' => $pixEmv,
                            'pix_id' => $pixId
                        ]);
                    }
                    break;

                default:
                    throw new \Exception('Método de pagamento não suportado.');
            }

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message'] ?? 'Erro ao processar o pagamento. Tente novamente.');
            }

            // Atualizar pedido com informações do pagamento
            if ($request->payment_method === 'credit_card' && isset($paymentResult['data']['tid'])) {
                $order->transaction_code = $paymentResult['data']['tid'];
                $order->payment_status = 'paid'; // Autorizado e capturado
                $order->payment_details = json_encode($paymentResult['data']);
            } elseif ($request->payment_method === 'pix') {
                $order->payment_status = 'pending'; // Aguardando pagamento
                $order->payment_details = json_encode(['pix_code' => $paymentResult['pix_code']]);
            }
            
            $order->save();

            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();
            
            // Limpar dados de frete da sessão
            session()->forget(['shipping_postal_code', 'selected_shipping_price', 'selected_shipping_option', 'selected_shipping_name']);

            DB::commit();

            // Redirecionar com base no método de pagamento
            if ($request->payment_method === 'pix') {
                return redirect()->route('site.payments.pix', ['order' => $order->id])
                    ->with('success', 'Pedido realizado com sucesso! Realize o pagamento via PIX para completar a compra.')
                    ->with('pix_code', $paymentResult['pix_code']);
            } else {
                return redirect()->route('site.orders.show', $order)
                    ->with('success', 'Pedido realizado com sucesso! Seu pagamento foi aprovado.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no checkout: ' . $e->getMessage());
            return redirect()->route('site.checkout.index')->with('error', $e->getMessage());
        }
    }
}
