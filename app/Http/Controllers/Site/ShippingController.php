<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected $melhorEnvioService;

    public function __construct(MelhorEnvioService $melhorEnvioService)
    {
        $this->melhorEnvioService = $melhorEnvioService;
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
    
    /**
     * API: Calcula opções de frete baseado no CEP informado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiCalculate(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string'
        ]);

        $postalCode = preg_replace('/[^0-9]/', '', $request->postal_code);

        if (strlen($postalCode) !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido'
            ], 400);
        }

        $cart = app(\App\Http\Controllers\Site\CartController::class)->getOrCreateCart();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Carrinho vazio'
            ], 400);
        }

        try {
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cart, $postalCode);
            
            // Salvar o CEP na sessão
            session(['shipping_postal_code' => $postalCode]);
            
            // Remover opção de frete selecionada anteriormente
            session()->forget('selected_shipping_option');
            session()->forget('selected_shipping_price');
            session()->forget('selected_shipping_name');

            return response()->json([
                'success' => true,
                'shipping_options' => $shippingOptions,
                'postal_code' => $postalCode
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete via API:', [
                'message' => $e->getMessage(),
                'postal_code' => $postalCode,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao calcular o frete. Por favor, tente novamente.'
            ], 500);
        }
    }

    /**
     * API: Seleciona uma opção de frete
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSelectOption(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required|string',
            'shipping_price' => 'required|numeric',
            'shipping_name' => 'required|string'
        ]);

        $shippingOption = $request->shipping_option;
        $shippingPrice = $request->shipping_price;
        $shippingName = $request->shipping_name;

        try {
            // Salvar a opção de frete na sessão
            session([
                'selected_shipping_option' => $shippingOption,
                'selected_shipping_price' => $shippingPrice,
                'selected_shipping_name' => $shippingName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Opção de frete selecionada com sucesso.',
                'shipping_option' => $shippingOption,
                'shipping_price' => $shippingPrice,
                'shipping_name' => $shippingName
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao selecionar frete via API:', [
                'message' => $e->getMessage(),
                'shipping_option' => $shippingOption,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao selecionar o frete. Por favor, tente novamente.'
            ], 500);
        }
    }

    public function updateShipping(Request $request)
    {
        $request->validate([
            'shipping_option' => 'required'
        ]);

        $cart = app(\App\Http\Controllers\Site\CartController::class)->getOrCreateCart();
        $postalCode = session('shipping_postal_code');

        if (!$postalCode) {
            return response()->json(['error' => 'CEP não informado'], 400);
        }

        try {
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cart->items, $postalCode);

            if (empty($shippingOptions)) {
                return response()->json(['error' => 'Não foi possível calcular o frete. Tente novamente.'], 400);
            }

            $selectedOption = collect($shippingOptions)
                ->firstWhere('id', $request->shipping_option);

            if (!$selectedOption) {
                return response()->json(['error' => 'Opção de frete inválida'], 400);
            }

            session([
                'selected_shipping_option' => $request->shipping_option,
                'selected_shipping_price' => $selectedOption['price'],
                'selected_shipping_name' => $selectedOption['name']
            ]);

            return response()->json([
                'success' => true,
                'shipping' => $selectedOption['price'],
                'delivery_time' => $selectedOption['delivery_time'],
                'name' => $selectedOption['name']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar frete:', [
                'error' => $e->getMessage(),
                'shipping_option' => $request->shipping_option
            ]);

            return response()->json([
                'error' => 'Erro ao atualizar frete. Tente novamente.'
            ], 500);
        }
    }
    
    public function getShippingOptions($postalCode)
    {
        if (strlen($postalCode) !== 8 && strlen($postalCode) !== 9) {
            return response()->json(['error' => 'CEP inválido. O CEP deve ter 8 dígitos.'], 400);
        }
        
        // Remover qualquer caractere não numérico (como hífen)
        $postalCode = preg_replace('/[^0-9]/', '', $postalCode);
        
        $cart = app(\App\Http\Controllers\Site\CartController::class)->getOrCreateCart();
        
        if ($cart->items->isEmpty()) {
            return response()->json(['error' => 'Carrinho vazio. Adicione produtos para calcular o frete.'], 400);
        }
        
        try {
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cart->items, $postalCode);
            
            if (empty($shippingOptions)) {
                return response()->json([
                    'error' => 'Não foi possível obter opções de frete para este CEP.'
                ], 404);
            }
            
            // Salvar o CEP na sessão
            session(['shipping_postal_code' => $postalCode]);
            
            return response()->json([
                'success' => true,
                'options' => $shippingOptions
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar opções de frete:', [
                'error' => $e->getMessage(),
                'postal_code' => $postalCode
            ]);
            
            return response()->json([
                'error' => 'Erro ao buscar opções de frete. Tente novamente.'
            ], 500);
        }
    }
}
