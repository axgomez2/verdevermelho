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

    public function __construct()
    {
        $this->config = config('melhorenvio');
        $this->apiToken = $this->config['token'];
        $this->baseUrl = $this->config['sandbox']
            ? 'https://sandbox.melhorenvio.com.br/api/v2/'
            : 'https://api.melhorenvio.com.br/v2/';

        $this->fromPostalCode = $this->config['from']['postal_code'];
        $this->fromData = $this->config['from'];
    }

    public function calculateShipping($cartItems, $toPostalCode)
    {
        $cacheKey = "shipping_calc_{$toPostalCode}_" . md5(json_encode($cartItems));

        return Cache::remember($cacheKey, now()->addMinutes($this->config['cache_time']), function () use ($cartItems, $toPostalCode) {
            try {
                $products = $this->formatCartItemsForShipping($cartItems);
                $totalWeight = collect($products)->sum(function ($product) {
                    return $product['weight'] * $product['quantity'];
                });
                $maxDimensions = collect($products)->reduce(function ($carry, $product) {
                    return [
                        'width' => max($carry['width'], $product['width']),
                        'height' => max($carry['height'], $product['height']),
                        'length' => max($carry['length'], $product['length'])
                    ];
                }, ['width' => 0, 'height' => 0, 'length' => 0]);

                $totalValue = collect($products)->sum(function ($product) {
                    return $product['insurance_value'] * $product['quantity'];
                });

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
                        'receipt' => $this->config['defaults']['receipt'],
                        'own_hand' => $this->config['defaults']['own_hand'],
                        'collect' => $this->config['defaults']['collect']
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
                    return $this->formatShippingOptions($response->json());
                }

                Log::error('Erro na API do Melhor Envio:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Erro ao calcular frete:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return [];
            }
        });
    }

    protected function formatCartItemsForShipping($cartItems)
    {
        $defaults = $this->config['defaults']['dimensions'];

        return $cartItems->map(function ($item) use ($defaults) {
            $product = $item->product;
            $dimensions = $product->dimensions;
            $weight = $product->weight;

            return [
                'id' => $product->id,
                'width' => $dimensions ? $dimensions->width : $defaults['width'],
                'height' => $dimensions ? $dimensions->height : $defaults['height'],
                'length' => $dimensions ? $dimensions->length : $defaults['length'],
                'weight' => $weight ? $weight->value : $defaults['weight'],
                'insurance_value' => $product->price,
                'quantity' => $item->quantity,
            ];
        })->toArray();
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
