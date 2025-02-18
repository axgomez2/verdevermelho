<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem; // Certifique-se de que o modelo CartItem está importado
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.productable.vinylSec');

        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $user = Auth::user();
        $address = $user ? $user->addresses()->where('is_default', 1)->first() : null;

        $postalCode = null;
        $shippingOptions = null;
        $shipping = 0;

        if ($address && $address->zip_code) {
            $postalCode = $address->zip_code;
        } elseif(session('shipping_postal_code')) {
            $postalCode = session('shipping_postal_code');
        }

        if ($postalCode) {
            $shippingResponse = $this->shippingService->calculateShipping($cart->items, $postalCode);
            $shippingOptions = $shippingResponse['options'] ?? [];
            $shipping = collect($shippingOptions)->min('price') ?? 0;
        }

        $tax = $subtotal * 0.1; // 10% de imposto
        $total = $subtotal + $shipping + $tax;

        return view('site.cart.index', compact('cart', 'subtotal', 'shipping', 'tax', 'total', 'shippingOptions', 'address'));
    }

    public function addToCart(Request $request)
    {
        $cart = $this->getOrCreateCart();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return redirect()->route('site.cart.index')->with('success', 'Item added to cart.');
    }

    public function updatePostalCode(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string|size:8'
        ]);

        session(['shipping_postal_code' => $request->postal_code]);

        return redirect()->route('site.cart.index');
    }

    public function removeItem($itemId)
    {
        // Certifique-se que o modelo CartItem está importado
        CartItem::destroy($itemId);
        return redirect()->route('site.cart.index')->with('success', 'Item removed from cart.');
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
