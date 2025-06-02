<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        
        // Verificar disponibilidade e estoque dos itens
        $availableItems = collect();
        $unavailableItems = collect();
        
        foreach ($cart->items as $item) {
            // Verifica se o produto existe, está em estoque (in_stock=1) e tem quantidade disponível
            if ($item->product && 
                $item->product->productable && 
                $item->product->productable->vinylSec && 
                $item->product->productable->vinylSec->in_stock == 1 && 
                $item->product->productable->vinylSec->quantity > 0) {
                
                // Ajusta a quantidade se for maior que o estoque disponível
                if ($item->quantity > $item->product->productable->vinylSec->quantity) {
                    $item->quantity = $item->product->productable->vinylSec->quantity;
                    $item->save();
                }
                
                $availableItems->push($item);
            } else {
                // Item indisponível (sem estoque, quantidade zero ou in_stock=0)
                $unavailableItems->push($item);
            }
        }
        
        // Calcular subtotal apenas com itens disponíveis
        $subtotal = $availableItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $user = Auth::user();
        $address = $user ? $user->addresses()->where('is_default', 1)->first() : null;
        $isLoggedIn = Auth::check();

        $postalCode = null;
        $shippingOptions = [];
        $shipping = 0;

        if ($address && $address->zip_code) {
            $postalCode = preg_replace('/[^0-9]/', '', $address->zip_code);
        } elseif(session('shipping_postal_code')) {
            $postalCode = session('shipping_postal_code');
        }

        if ($postalCode && !empty($cart->items)) {
            try {
                $shippingOptions = $this->shippingService->calculateShipping($cart->items, $postalCode);

                if (session('selected_shipping_price')) {
                    $shipping = session('selected_shipping_price');
                } elseif (!empty($shippingOptions)) {
                    $shipping = collect($shippingOptions)->min('price') ?? 0;
                }

                Log::info('Opções de frete calculadas:', ['options' => $shippingOptions, 'shipping' => $shipping]);
            } catch (\Exception $e) {
                Log::error('Erro ao calcular frete no carrinho:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Imposto removido conforme solicitado
        $tax = 0;
        $total = $subtotal + $shipping;

        // Armazenar o CEP do usuário na sessão se ele estiver logado e tiver um endereço
        if ($address && $address->zip_code && Auth::check()) {
            session(['shipping_postal_code' => preg_replace('/[^0-9]/', '', $address->zip_code)]);
        }

        return view('site.cart.index', compact(
            'cart', 
            'subtotal', 
            'shipping', 
            'tax', // Mantemos a variável para compatibilidade com a view, mas com valor zero
            'total', 
            'shippingOptions', 
            'address', 
            'isLoggedIn', 
            'postalCode',
            'availableItems',
            'unavailableItems'
        ));
    }

    public function addToCart(Request $request)
    {
        $cart = $this->getOrCreateCart();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);
        $vinylSec = $product->productable->vinylSec;

        if ($vinylSec->quantity < $quantity) {
            return redirect()->back()->with('error', 'Desculpe, não há estoque suficiente para este disco.');
        }

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            if ($vinylSec->quantity < ($cartItem->quantity + $quantity)) {
                return redirect()->back()->with('error', 'Desculpe, não há estoque suficiente para adicionar mais unidades deste disco.');
            }
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return redirect()->route('site.cart.index')->with('success', 'Item adicionado ao carrinho.');
    }

    public function updatePostalCode(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string'
        ]);

        $postalCode = preg_replace('/[^0-9]/', '', $request->postal_code);

        if (strlen($postalCode) !== 8) {
            return redirect()->back()->withErrors(['postal_code' => 'CEP inválido']);
        }

        session(['shipping_postal_code' => $postalCode]);
        session()->forget('selected_shipping_price');
        session()->forget('selected_shipping_option');

        return redirect()->route('site.cart.index');
    }

    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);
        return redirect()->route('site.cart.index')->with('success', 'Item removido do carrinho.');
    }

    /**
     * Calcula opções de frete para um determinado CEP
     * 
     * @param string $postalCode CEP de destino
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingOptions($postalCode)
    {
        // Limpar o CEP para garantir que tenha apenas números
        $postalCode = preg_replace('/[^0-9]/', '', $postalCode);
        
        if (strlen($postalCode) !== 8) {
            return response()->json(['error' => 'CEP inválido. O CEP deve ter 8 dígitos.', 'success' => false], 400);
        }
        
        // Salvar o CEP na sessão
        session(['shipping_postal_code' => $postalCode]);
        
        $cart = $this->getOrCreateCart();
        if ($cart->items->isEmpty()) {
            return response()->json(['error' => 'Carrinho vazio', 'success' => false], 400);
        }
        
        try {
            // Calcular opções de frete
            $options = $this->shippingService->calculateShipping($cart->items, $postalCode);
            
            if (empty($options)) {
                return response()->json(['error' => 'Nenhuma opção de frete disponível para este CEP.', 'success' => false], 404);
            }
            
            // Retornar opções de frete
            return response()->json(['success' => true, 'options' => $options]);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular opções de frete', [
                'message' => $e->getMessage(),
                'postal_code' => $postalCode
            ]);
            
            return response()->json(['error' => 'Erro ao calcular opções de frete. Tente novamente.', 'success' => false], 500);
        }
    }
    
    public function updateQuantity(CartItem $cartItem, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Verificar disponibilidade e estoque
        $product = $cartItem->product;
        
        if (!$product || !$product->productable || !$product->productable->vinylSec) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Produto indisponível'
                ], 400);
            }
            
            return redirect()->route('site.cart.index')
                ->with('error', 'Produto indisponível');
        }
        
        $vinylSec = $product->productable->vinylSec;
        $isInStock = $vinylSec->in_stock == 1;
        $availableStock = $vinylSec->quantity;
        
        if (!$isInStock || $availableStock <= 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Produto sem estoque'
                ], 400);
            }
            
            return redirect()->route('site.cart.index')
                ->with('error', 'Produto sem estoque');
        }
        
        // Limitar a quantidade ao estoque disponível
        $quantity = min($request->quantity, $availableStock);
        $cartItem->update(['quantity' => $quantity]);
        
        // Recalcular totais para resposta AJAX
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.productable.vinylSec');
        
        // Filtrar apenas itens disponíveis para cálculo
        $availableItems = $cart->items->filter(function($item) {
            return $item->product && 
                   $item->product->productable && 
                   $item->product->productable->vinylSec && 
                   $item->product->productable->vinylSec->in_stock == 1 && 
                   $item->product->productable->vinylSec->quantity > 0;
        });
        
        $subtotal = $availableItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        
        // Recuperar frete selecionado
        $shipping = session('selected_shipping_price', 0);
        
        // Imposto removido conforme solicitado
        $tax = 0;
        
        // Cálculo do total
        $total = $subtotal + $shipping;
        
        // Formatar valores para exibição
        $formattedItemTotal = number_format($product->price * $quantity, 2, ',', '.');
        $formattedSubtotal = number_format($subtotal, 2, ',', '.');
        $formattedShipping = number_format($shipping, 2, ',', '.');
        $formattedTotal = number_format($total, 2, ',', '.');
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Quantidade atualizada com sucesso',
                'quantity' => $quantity,
                'itemTotal' => $product->price * $quantity,
                'formattedItemTotal' => $formattedItemTotal,
                'subtotal' => $subtotal,
                'formattedSubtotal' => $formattedSubtotal,
                'shipping' => $shipping,
                'formattedShipping' => $formattedShipping,
                'total' => $total,
                'formattedTotal' => $formattedTotal,
                'availableStock' => $availableStock
            ]);
        }
        
        return redirect()->route('site.cart.index')
            ->with('success', 'Quantidade atualizada com sucesso');

    }

    public function updateShipping(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required'
        ]);

        $cart = $this->getOrCreateCart();
        $cart->load('items.product.productable.vinylSec');
        
        // Filtrar apenas itens disponíveis
        $availableItems = $cart->items->filter(function($item) {
            return $item->product && 
                   $item->product->productable && 
                   $item->product->productable->vinylSec && 
                   $item->product->productable->vinylSec->in_stock == 1 && 
                   $item->product->productable->vinylSec->quantity > 0;
        });
        
        $unavailableItems = $cart->items->filter(function($item) {
            return !$item->product || 
                   !$item->product->productable || 
                   !$item->product->productable->vinylSec || 
                   $item->product->productable->vinylSec->in_stock == 0 || 
                   $item->product->productable->vinylSec->quantity <= 0;
        });
        
        if ($availableItems->isEmpty()) {
            return response()->json(['error' => 'Não há itens disponíveis no carrinho'], 400);
        }

        $shippingOption = $request->shipping_option;
        $shippingOptions = [];
        
        // Recuperar o CEP de entrega da sessão
        $postalCode = session('shipping_postal_code');
        if (!$postalCode) {
            return response()->json(['error' => 'CEP não informado'], 400);
        }

        try {
            $shippingOptions = $this->shippingService->calculateShipping($cart->items, $postalCode);
            
            // Encontrar a opção de frete selecionada
            $selectedOption = collect($shippingOptions)->firstWhere('id', $shippingOption);
            
            if (!$selectedOption) {
                return response()->json(['error' => 'Opção de frete inválida'], 400);
            }
            
            // Armazenar na sessão
            session([
                'selected_shipping_option' => $shippingOption,
                'selected_shipping_price' => $selectedOption['price'],
                'selected_shipping_name' => $selectedOption['name']
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Frete atualizado com sucesso',
                'shipping' => $selectedOption['price'],
                'total' => $cart->total + $selectedOption['price']
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar o frete:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Erro ao calcular o frete'], 500);
        }
    }

    /**
     * Importa itens do carrinho local (localStorage) para o carrinho do usuário
     *
     * @param array $cartData Dados do carrinho local
     * @return Cart O carrinho do usuário
     */
    public function importLocalCart(array $cartData)
    {
        if (!Auth::check()) {
            return false;
        }
        
        $cart = $this->getOrCreateCart();
        
        // Importar itens do localStorage
        foreach ($cartData as $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                continue;
            }
            
            try {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                
                // Validar se o produto existe
                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }
                
                // Verificar estoque
                $availableQuantity = 0;
                if ($product->productable_type === 'App\\Models\\Vinyl' && $product->productable && $product->productable->vinylSec) {
                    $availableQuantity = $product->productable->vinylSec->quantity;
                }
                
                // Continuar mesmo se availableQuantity for 0, apenas limitando a quantidade
                // Isso permite que o usuário adicione produtos sem estoque ao carrinho
                // para visualização, mas a quantidade será limitada quando o produto estiver em estoque
                    // Verificar se já existe no carrinho do usuário
                    $existingItem = $cart->items()->where('product_id', $productId)->first();
                    if ($existingItem) {
                        // Atualizar quantidade respeitando o estoque disponível
                        $newQuantity = $existingItem->quantity + $quantity;
                        if ($availableQuantity > 0 && $newQuantity > $availableQuantity) {
                            $newQuantity = $availableQuantity;
                        }
                        $existingItem->quantity = $newQuantity;
                        $existingItem->save();
                    } else {
                        // Adicionar novo item respeitando o estoque disponível
                        $newQuantity = $quantity;
                        if ($availableQuantity > 0 && $newQuantity > $availableQuantity) {
                            $newQuantity = $availableQuantity;
                        }
                        $cart->items()->create([
                            'product_id' => $productId,
                            'quantity' => $newQuantity
                        ]);
                    }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erro ao importar item: ' . $e->getMessage(), [
                    'item' => $item,
                    'user_id' => Auth::id()
                ]);
            }
        }
        
        return $cart;
    }

    public function getOrCreateCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Verifica se existe um carrinho na sessão para migrar
            $sessionId = session()->get('cart_session_id');
            if ($sessionId) {
                $sessionCart = Cart::where('session_id', $sessionId)->first();
                if ($sessionCart) {
                    // Migra os itens do carrinho da sessão para o carrinho do usuário
                    foreach ($sessionCart->items as $item) {
                        $existingItem = $cart->items()->where('product_id', $item->product_id)->first();
                        if ($existingItem) {
                            $existingItem->quantity += $item->quantity;
                            $existingItem->save();
                        } else {
                            $item->cart_id = $cart->id;
                            $item->save();
                        }
                    }
                    $sessionCart->delete();
                }
                session()->forget('cart_session_id');
            }
            return $cart;
        } else {
            $sessionId = session()->get('cart_session_id');

            if (!$sessionId) {
                $sessionId = Str::uuid();
                session(['cart_session_id' => $sessionId]);
            }

            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }
    }
}
