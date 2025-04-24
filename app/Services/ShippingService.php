<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\MelhorEnvioService;

class ShippingService
{
    protected $melhorEnvioService;
    protected $config;

    public function __construct(MelhorEnvioService $melhorEnvioService)
    {
        $this->melhorEnvioService = $melhorEnvioService;
        $this->config = config('melhorenvio');
    }

    public function calculateShipping($cartItems, $zipCode)
    {
        Log::info('Calculando frete para CEP: ' . $zipCode);

        try {
            // Delegamos o cálculo para o serviço especializado do Melhor Envio
            $shippingOptions = $this->melhorEnvioService->calculateShipping($cartItems, $zipCode);

            if (!empty($shippingOptions)) {
                Log::info('Opções de frete calculadas com sucesso', ['options_count' => count($shippingOptions)]);
                return $shippingOptions;
            } else {
                Log::error('Nenhuma opção de frete disponível para o CEP: ' . $zipCode);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    public function generateShippingLabel($order)
    {
        try {
            Log::info('Gerando etiqueta para o pedido #' . $order->id);

            // Obter o endereço do pedido
            $address = $order->shippingAddress;
            if (!$address) {
                return ['error' => 'Endereço de entrega não encontrado'];
            }

            // Formatar os itens do pedido para a API
            $items = $this->formatOrderItemsForShipping($order->items);

            // Preparar o payload para a API do Melhor Envio
            $payload = [
                'service' => session('selected_shipping_option', 1), // Usar a opção selecionada ou PAC como padrão
                'agency' => 49, // ID da agência dos Correios (pode ser configurável)
                'from' => array_filter($this->config['from']),
                'to' => [
                    'name' => $order->user->name,
                    'phone' => $address->phone ?? '',
                    'email' => $order->user->email,
                    'document' => $address->document ?? '',
                    'address' => $address->street,
                    'number' => $address->number,
                    'complement' => $address->complement ?? '',
                    'district' => $address->district ?? '',
                    'city' => $address->city,
                    'state_abbr' => $address->state,
                    'postal_code' => $address->zip_code,
                ],
                'products' => $items,
                'options' => [
                    'insurance_value' => $order->total,
                    'receipt' => $this->config['defaults']['receipt'],
                    'own_hand' => $this->config['defaults']['own_hand'],
                    'reverse' => false,
                    'non_commercial' => $this->config['defaults']['non_commercial'],
                ],
            ];

            Log::info('Payload para geração de etiqueta:', $payload);

            // Fazer a requisição para a API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['token'],
            ])->post($this->melhorEnvioService->baseUrl . 'me/shipment/generate', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Etiqueta gerada com sucesso:', $data);
                return $data;
            } else {
                Log::error('Erro ao gerar etiqueta:', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return ['error' => 'Erro ao gerar etiqueta de envio'];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => 'Erro ao gerar etiqueta de envio'];
        }
    }

    protected function formatOrderItemsForShipping($orderItems)
    {
        return $orderItems->map(function ($item) {
            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unitary_value' => $item->price,
                'weight' => $item->product->weight,
                'width' => $item->product->width,
                'height' => $item->product->height,
                'length' => $item->product->length,
            ];
        })->toArray();
    }
}
