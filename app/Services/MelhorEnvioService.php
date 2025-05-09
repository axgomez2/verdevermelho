<?php

namespace App\Services;

use App\Models\Order;
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

    /**
     * Gera uma etiqueta de envio para o pedido
     *
     * @param Order $order
     * @return array
     */
    public function generateShippingLabel(Order $order)
    {
        if (!$this->isEnabled) {
            Log::warning('MelhorEnvio: Serviço não configurado. Impossível gerar etiqueta.');
            return [
                'success' => false,
                'message' => 'Serviço de envio não configurado'
            ];
        }

        if (!$order->shippingAddress) {
            Log::error('MelhorEnvio: Não foi possível gerar etiqueta. Endereço de entrega ausente.');
            return [
                'success' => false,
                'message' => 'Endereço de entrega não encontrado'
            ];
        }

        try {
            // 1. Criar o pedido de etiqueta
            $cartId = $this->createCart($order);
            if (!$cartId) {
                return [
                    'success' => false,
                    'message' => 'Não foi possível criar o carrinho de envio'
                ];
            }

            // 2. Comprar a etiqueta
            $purchaseResult = $this->purchaseLabel($cartId);
            if (!$purchaseResult['success']) {
                return $purchaseResult;
            }

            // 3. Gerar o PDF da etiqueta
            $labelResult = $this->generateLabelPdf($cartId);
            
            return $labelResult;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta de envio:', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao gerar etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cria um carrinho na Melhor Envio para posteriormente gerar a etiqueta
     *
     * @param Order $order
     * @return string|null
     */
    protected function createCart(Order $order)
    {
        $shippingAddress = $order->shippingAddress;
        $serviceId = $this->getServiceIdFromOrder($order);
        
        // Obter todos os produtos do pedido
        $products = [];
        $totalWeight = 0;
        $totalValue = 0;
        $maxDimensions = ['width' => 11, 'height' => 11, 'length' => 11];
        
        foreach ($order->items as $item) {
            $product = $item->product;
            
            // Definir dimensões e peso padrão
            $width = 31;  // Padrão para vinyl
            $height = 31;
            $length = 1;
            $weight = 0.2; // 200g como padrão por disco
            
            // Atualizar dimensões máximas
            $maxDimensions = [
                'width' => max($maxDimensions['width'], $width),
                'height' => max($maxDimensions['height'], $height),
                'length' => max($maxDimensions['length'], $length)
            ];
            
            $totalWeight += $weight * $item->quantity;
            $totalValue += $product->price * $item->quantity;
            
            $products[] = [
                'name' => $product->name,
                'quantity' => $item->quantity,
                'unitary_value' => $product->price
            ];
        }
        
        // Garantir dimensões mínimas
        $maxDimensions['width'] = max(11, $maxDimensions['width']);
        $maxDimensions['height'] = max(11, $maxDimensions['height']);
        $maxDimensions['length'] = max(11, $maxDimensions['length']);
        $totalWeight = max(0.1, $totalWeight);
        
        // Prepara o payload para a API
        $payload = [
            'service' => $serviceId,
            'agency' => $this->config['agency_id'] ?? null, // ID da agência de envio (opcional)
            'from' => array_filter($this->fromData),
            'to' => [
                'name' => $order->user->name,
                'phone' => $order->user->phone ?? '00000000000',
                'email' => $order->user->email,
                'document' => $order->user->document ?? '', // CPF ou CNPJ
                'address' => $shippingAddress->street,
                'number' => $shippingAddress->number,
                'complement' => $shippingAddress->complement ?? '',
                'district' => $shippingAddress->neighborhood,
                'city' => $shippingAddress->city,
                'state_abbr' => $shippingAddress->state,
                'postal_code' => $shippingAddress->zip_code
            ],
            'products' => $products,
            'package' => [
                'width' => $maxDimensions['width'],
                'height' => $maxDimensions['height'],
                'length' => $maxDimensions['length'],
                'weight' => $totalWeight
            ],
            'options' => [
                'insurance_value' => $totalValue,
                'receipt' => false,
                'own_hand' => false,
                'reverse' => false,
                'non_commercial' => true
            ]
        ];
        
        Log::info('Criando carrinho no Melhor Envio:', ['payload' => json_encode($payload)]);
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/cart', $payload);
            
            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['id'])) {
                    Log::info('Carrinho criado com sucesso:', ['cart_id' => $result['id']]);
                    return $result['id'];
                }
            }
            
            Log::error('Erro ao criar carrinho:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao criar carrinho:', [
                'message' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Compra a etiqueta após criar o carrinho
     *
     * @param string $cartId
     * @return array
     */
    protected function purchaseLabel($cartId)
    {
        if (empty($cartId)) {
            return [
                'success' => false,
                'message' => 'ID do carrinho inválido'
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/checkout', [
                'orders' => [$cartId]
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('Etiqueta comprada com sucesso:', ['result' => $result]);
                
                return [
                    'success' => true,
                    'message' => 'Etiqueta comprada com sucesso',
                    'data' => $result
                ];
            }
            
            Log::error('Erro ao comprar etiqueta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao comprar etiqueta: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao comprar etiqueta:', [
                'message' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao comprar etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gera o PDF da etiqueta
     *
     * @param string $cartId
     * @return array
     */
    protected function generateLabelPdf($cartId)
    {
        if (empty($cartId)) {
            return [
                'success' => false,
                'message' => 'ID do carrinho inválido'
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post($this->baseUrl . 'me/shipment/generate', [
                'orders' => [$cartId]
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result[0]['status']) && $result[0]['status'] === false) {
                    $message = $result[0]['message'] ?? 'Erro ao gerar PDF da etiqueta';
                    Log::error('Erro ao gerar PDF:', ['message' => $message]);
                    
                    return [
                        'success' => false,
                        'message' => $message
                    ];
                }

                // Acesso ao link da etiqueta
                $labelUrl = null;
                $tracking = null;
                
                if (isset($result[0]['tracking'])) {
                    $tracking = $result[0]['tracking'];
                    $labelUrl = $this->baseUrl . 'me/shipment/print?orders=' . $cartId;
                }
                
                Log::info('PDF da etiqueta gerado com sucesso:', [
                    'label_url' => $labelUrl,
                    'tracking' => $tracking
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Etiqueta gerada com sucesso',
                    'label_url' => $labelUrl,
                    'tracking' => $tracking
                ];
            }
            
            Log::error('Erro ao gerar PDF da etiqueta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao gerar PDF da etiqueta: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao gerar PDF da etiqueta:', [
                'message' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao gerar PDF da etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Determina o ID do serviço de envio a partir dos dados do pedido
     * 
     * @param Order $order
     * @return int
     */
    protected function getServiceIdFromOrder(Order $order)
    {
        // Se temos o service_id no pedido, usamos ele
        if (!empty($order->shipping_service_id)) {
            return $order->shipping_service_id;
        }
        
        // Mapeamento básico baseado no nome do método de envio
        $methodNameToServiceId = [
            'PAC' => 1, // Correios PAC
            'SEDEX' => 2, // Correios SEDEX
            'JADLOG_PACKAGE' => 3, // Jadlog Package
            'JADLOG_COM' => 4 // Jadlog .Com
        ];
        
        // Extrai o método de envio
        $shippingMethod = strtoupper($order->shipping_method);
        
        foreach ($methodNameToServiceId as $name => $id) {
            if (strpos($shippingMethod, $name) !== false) {
                return $id;
            }
        }
        
        // Default para PAC se não conseguir identificar
        return 1;
    }
}
