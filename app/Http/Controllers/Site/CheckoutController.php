<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->cart ?? Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        return view('site.checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
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

            // Criar pedido
            $order = Order::create([
                'user_id' => $request->user()->id ?? null,
                'total' => $cart->total,
                // Adicione outros campos necessários para o pedido
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

            // Limpar o carrinho
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('site.orders.show', $order)->with('success', 'Pedido realizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('site.checkout.index')->with('error', $e->getMessage());
        }
    }
}
