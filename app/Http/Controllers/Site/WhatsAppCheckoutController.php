<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppCheckoutController extends Controller
{
    /**
     * Registra um pedido finalizado via WhatsApp
     */
    public function registerOrder(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address_id' => 'required|exists:addresses,id',
            'shipping_cost' => 'required|numeric',
            'shipping_service_name' => 'required|string',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            // Obter o usuário autenticado
            $user = Auth::user();
            
            // Criar o pedido
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $request->total,
                'shipping_cost' => $request->shipping_cost,
                'shipping_service_name' => $request->shipping_service_name,
                'shipping_delivery_time' => $request->shipping_delivery_time ?? null,
                'tax' => 0, // Impostos não são aplicados conforme solicitado
                'status' => 'pending',
                'payment_method' => 'whatsapp',
                'payment_status' => 'pending',
                'notes' => 'Pedido realizado via WhatsApp',
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->shipping_address_id, // Usando o mesmo endereço para faturamento
            ]);

            // Adicionar os itens ao pedido
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);
                
                // Atualizar o estoque do produto
                $product = \App\Models\Product::find($item['id']);
                if ($product && $product->productable_type === 'App\\Models\\VinylSec') {
                    $product->productable->decrement('quantity', $item['quantity']);
                }
            }

            // Limpar o carrinho do usuário
            $cartController = app(CartController::class);
            $cart = $cartController->getOrCreateCart();
            $cart->items()->delete();
            
            // Limpar dados de frete da sessão
            session()->forget(['shipping_postal_code', 'selected_shipping_price', 'selected_shipping_option', 'selected_shipping_name']);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'message' => 'Pedido registrado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar pedido via WhatsApp:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar o pedido. Por favor, tente novamente.'
            ], 500);
        }
    }
}
