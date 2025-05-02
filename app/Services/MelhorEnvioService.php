<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MelhorEnvioService
{
    protected $apiToken;
    protected $baseUrl;
    protected $fromPostalCode;
    protected $fromData;
    protected $config;
    protected $isEnabled = false;

    public function __construct()
    {
        $this->config = config('melhorenvio');
        
        // Verifica se as configurações necessárias estão disponíveis
        if (isset($this->config['token']) && !empty($this->config['token']) && 
            isset($this->config['from']['postal_code']) && !empty($this->config['from']['postal_code'])) {
            $this->isEnabled = true;
            $this->apiToken = $this->config['token'];
            $this->baseUrl = $this->config['sandbox']
                ? 'https://sandbox.melhorenvio.com.br/api/v2/'
                : 'https://api.melhorenvio.com.br/v2/';

            $this->fromPostalCode = $this->config['from']['postal_code'];
            $this->fromData = $this->config['from'];
        } else {
            $this->apiToken = '';
            $this->baseUrl = 'https://sandbox.melhorenvio.com.br/api/v2/';
            $this->fromPostalCode = '';
            $this->fromData = [];
            
            Log::warning('MelhorEnvio: Configuração incompleta. O serviço de frete está desativado.');
        }
    }

    public function calculateShipping($cartItems, $toPostalCode)
    {
        // Se o serviço não estiver configurado, retorna um array vazio
        if (!$this->isEnabled) {
            Log::info('MelhorEnvio: Serviço não configurado. Retornando opções de frete vazias.');
            return [];
        }
        
        $cacheKey = "shipping_calc_{$toPostalCode}_" . md5(json_encode($cartItems));

        // Desabilitar o cache temporariamente para debug
        //return Cache::remember($cacheKey, now()->addMinutes($this->config['cache_time'] ?? 30), function () use ($cartItems, $toPostalCode) {
        try {
            $products = $this->formatCartItemsForShipping($cartItems);
            
            if (empty($products)) {
                Log::warning('Nenhum produto válido para cálculo de frete');
                return [];
            }
            
            // Garantir que o peso total nunca seja zero
            $totalWeight = max(0.1, collect($products)->sum(function ($product) {
                return $product['weight'] * $product['quantity'];
            }));
            
            // Obter as maiores dimensões entre todos os produtos
            $maxDimensions = collect($products)->reduce(function ($carry, $product) {
                return [
                    'width' => max($carry['width'], $product['width']),
                    'height' => max($carry['height'], $product['height']),
                    'length' => max($carry['length'], $product['length'])
                ];
            }, ['width' => 11, 'height' => 11, 'length' => 11]); // Dimensões mínimas seguras

            // Garantir que todas as dimensões sejam pelo menos 11cm (mínimo exigido)
            $maxDimensions['width'] = max(11, $maxDimensions['width']);
            $maxDimensions['height'] = max(11, $maxDimensions['height']);
            $maxDimensions['length'] = max(11, $maxDimensions['length']);
            
            $totalValue = max(1, collect($products)->sum(function ($product) {
                return $product['insurance_value'] * $product['quantity'];
            }));

            // Tratar CEP
            $toPostalCode = preg_replace('/[^0-9]/', '', $toPostalCode);
            
            $payload = [
                'from' => array_filter($this->fromData),
                'to' => [
                    'postal_code' => $toPostalCode,
                ],
                'package' => [
                    'width' => $maxDimensions['width'],
                    'height' => $maxDimensions['height'],
                    'length' => $maxDimensions['length'],
                    'weight' => $totalWeight
                ],
                'options' => [
                    'insurance_value' => $totalValue,
                    'receipt' => false, // Simplificando as opções
                    'own_hand' => false,
                    'collect' => false
                ],
                'services' => implode(',', array_keys($this->config['services']))
            ];

            Log::info('Calculando frete Melhor Envio:', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/calculate', $payload);

            if ($response->successful()) {
                $result = $this->formatShippingOptions($response->json());
                Log::info('Opções de frete calculadas com sucesso', ['count' => count($result)]);
                return $result;
            }

            Log::error('Erro na API do Melhor Envio:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // Em ambiente de desenvolvimento/sandbox, retorna opções fictícias caso a API falhe
            if ($this->config['sandbox']) {
                Log::info('Retornando opções fictícias de frete (ambiente sandbox)');
                return [
                    [
                        'id' => 1,
                        'name' => 'PAC (Simulado)',
                        'price' => 25.90,
                        'delivery_time' => '5 a 7 dias úteis',
                        'company' => 'Correios'
                    ],
                    [
                        'id' => 2,
                        'name' => 'SEDEX (Simulado)',
                        'price' => 45.50,
                        'delivery_time' => '1 a 3 dias úteis',
                        'company' => 'Correios'
                    ]
                ];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
        //});
    }

    protected function formatCartItemsForShipping($cartItems)
    {
        $defaults = $this->config['defaults']['dimensions'];
        $formattedItems = [];
        
        Log::info('Formatando itens do carrinho para envio:', ['item_count' => count($cartItems)]);
        
        foreach ($cartItems as $item) {
            try {
                $product = $item->product;
                
                // Definir valores padrão para dimensões e peso
                $width = $defaults['width'];
                $height = $defaults['height']; 
                $length = $defaults['length'];
                $weight = $defaults['weight'];
                
                // Tentar obter as dimensões a partir do produto ou vinylSec
                if (isset($product->productable) && isset($product->productable->vinylSec)) {
                    // Dimensões padrão para discos de vinil
                    $width = 31;  // 12 polegadas = ~31cm
                    $height = 31;
                    $length = 1;   // espessura típica
                    $weight = 0.2; // peso típico em kg
                }
                
                // Valores seguros
                $formattedItems[] = [
                    'id' => $product->id,
                    'width' => $width,
                    'height' => $height, 
                    'length' => $length,
                    'weight' => $weight,
                    'insurance_value' => $product->price,
                    'quantity' => $item->quantity,
                ];
                
                Log::info('Item formatado com sucesso', [
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $item->quantity
                ]);
            } catch (\Exception $e) {
                Log::error('Erro ao formatar item para envio:', [
                    'message' => $e->getMessage(),
                    'item' => $item
                ]);
                
                // Adicionar um item com dimensões padrão para que o cálculo não falhe
                $formattedItems[] = [
                    'id' => 'fallback',
                    'width' => $defaults['width'],
                    'height' => $defaults['height'],
                    'length' => $defaults['length'],
                    'weight' => $defaults['weight'],
                    'insurance_value' => 100, // valor padrão
                    'quantity' => 1,
                ];
            }
        }
        
        return $formattedItems;
    }

    protected function formatShippingOptions($response)
    {
        $options = [];
        $services = $this->config['services'];

        foreach ($response as $option) {
            if (isset($option['price']) && $option['price'] > 0) {
                $serviceId = $option['id'];
                $serviceName = $services[$serviceId]['name'] ?? $option['name'];
                $companyName = $services[$serviceId]['company'] ?? $option['company']['name'] ?? 'Correios';

                $options[] = [
                    'id' => $serviceId,
                    'name' => $serviceName,
                    'price' => $option['price'],
                    'delivery_time' => $option['delivery_time'],
                    'company' => $companyName,
                    'custom_delivery_time' => $this->formatDeliveryTime($option['delivery_time']),
                    'custom_price' => 'R$ ' . number_format($option['price'], 2, ',', '.'),
                ];
            }
        }
        return $options;
    }

    protected function formatDeliveryTime($days)
    {
        return $days . ' ' . ($days > 1 ? 'dias úteis' : 'dia útil');
    }
}
