<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartItemController extends Controller
{
    protected $cartController;

    public function __construct(CartController $cartController)
    {
        $this->cartController = $cartController;
    }
    
    /**
     * Verifica quais produtos estão no carrinho do usuário atual
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCartItems()
    {
        try {
            $cart = $this->cartController->getOrCreateCart();
            
            // Obtém os IDs de produtos no carrinho atual
            $productIds = $cart->items()->pluck('product_id')->toArray();
            
            return response()->json([
                'success' => true,
                'inCart' => $productIds
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar itens no carrinho: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar itens no carrinho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Requisição recebida para adicionar item ao carrinho', $request->all());

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $cart = $this->cartController->getOrCreateCart();
            Log::info('Carrinho obtido/criado', ['cart_id' => $cart->id]);

            $product = Product::findOrFail($request->product_id);
            Log::info('Produto encontrado', ['product_id' => $product->id]);
            
            // Verificar estoque disponível em vinylSec
            $vinylSec = $product->productable->vinylSec;
            $requestedQuantity = $request->quantity;
            
            // Verificar se o produto está disponível (in_stock = 1)
            if ($vinylSec->in_stock == 0) {
                DB::rollBack();
                throw new \Exception('Produto indisponível para compra.');
            }
            
            // Verificar se o item já está no carrinho
            $existingItem = $cart->items()->where('product_id', $product->id)->first();
            if ($existingItem) {
                $totalRequestedQuantity = $existingItem->quantity + $requestedQuantity;
            } else {
                $totalRequestedQuantity = $requestedQuantity;
            }
            
            // Verificar se há estoque suficiente
            if ($vinylSec->quantity < $totalRequestedQuantity) {
                DB::rollBack();
                throw new \Exception('Estoque insuficiente. Disponível: ' . $vinylSec->quantity);
            }
            
            $cartItem = $cart->items()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => DB::raw('quantity + ' . $request->quantity)]
            );
            Log::info('Item do carrinho atualizado/criado', ['cart_item_id' => $cartItem->id]);

            DB::commit();

            $message = 'Item adicionado ao carrinho com sucesso.';
            $success = true;
            $cartCount = $cart->items->sum('quantity');

            Log::info('Item adicionado ao carrinho com sucesso', [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar item ao carrinho: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Mensagem de erro personalizada para estoque insuficiente
            if (strpos($e->getMessage(), 'Estoque insuficiente') !== false) {
                $message = $e->getMessage();
            } else {
                $message = 'Ocorreu um erro ao adicionar o item ao carrinho. Por favor, tente novamente.';
            }
            
            $success = false;
            $cartCount = null;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'cartCount' => $cartCount
            ]);
        }

        if ($success) {
            return redirect()->route('site.cart.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', $message);
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Verificar estoque disponível em vinylSec
            $product = Product::findOrFail($cartItem->product_id);
            $vinylSec = $product->productable->vinylSec;
            
            // Verificar se o produto está disponível (in_stock = 1)
            if ($vinylSec->in_stock == 0) {
                DB::rollBack();
                return redirect()->route('site.cart.index')
                    ->with('error', "Produto indisponível para compra.");
            }
            
            // Verificar se há estoque suficiente para a nova quantidade
            if ($vinylSec->quantity < $request->quantity) {
                DB::rollBack();
                return redirect()->route('site.cart.index')
                    ->with('error', "Estoque insuficiente. Disponível: {$vinylSec->quantity} unidades");
            }
            
            $cartItem->update(['quantity' => $request->quantity]);
            DB::commit();
            
            return redirect()->route('site.cart.index')->with('success', 'Item atualizado no carrinho.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar item no carrinho: ' . $e->getMessage());
            return redirect()->route('site.cart.index')
                ->with('error', 'Erro ao atualizar item no carrinho. Por favor, tente novamente.');
        }
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return redirect()->route('site.cart.index')->with('success', 'Item removed from cart.');
    }


    public function checkStock(Request $request)
    {
        $cartItems = $request->input('items');
        $stockStatus = [];

        foreach ($cartItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $vinylSec = $product->productable->vinylSec;
            $availableStock = $vinylSec->quantity;

            $stockStatus[$item['product_id']] = [
                'available' => $availableStock,
                'requested' => $item['quantity'],
                'status' => $availableStock >= $item['quantity'] ? 'ok' : 'insufficient'
            ];
        }

        return response()->json($stockStatus);
    }
}
