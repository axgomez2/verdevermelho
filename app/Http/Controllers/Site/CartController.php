<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.productable.vinylSec');
        return view('site.cart.index', compact('cart'));
    }



    public function update(Request $request, Cart $cart)
    {
        // Atualizar o carrinho (se necessário)
        return redirect()->route('site.cart.index')->with('success', 'Carrinho atualizado.');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return redirect()->route('site.cart.index')->with('success', 'Carrinho esvaziado.');
    }

    public function getOrCreateCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::uuid();
                session()->put('cart_session_id', $sessionId);
            }
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }
    }
}
