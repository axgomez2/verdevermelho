<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedeItauService
{
    protected $baseUrl;
    protected $merchantId;
    protected $merchantKey;
    protected $isSandbox;

    public function __construct()
    {
        $this->isSandbox = config('services.rede_itau.sandbox', true);
        $this->baseUrl = $this->isSandbox 
            ? 'https://sandbox.userede.com.br/desenvolvedores/api/v1/' 
            : 'https://api.userede.com.br/erede/v1/';
        $this->merchantId = config('services.rede_itau.merchant_id');
        $this->merchantKey = config('services.rede_itau.merchant_key');
    }

    /**
     * Autoriza uma transação de crédito
     */
    public function authorize($params)
    {
        try {
            $endpoint = $this->baseUrl . 'transactions';
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($endpoint, $this->formatCreditRequest($params));
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro na API da Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'params' => $params
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao processar pagamento',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção na API da Rede Itaú', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao processar pagamento: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }
    
    /**
     * Captura uma transação previamente autorizada
     */
    public function capture($paymentId, $amount = null)
    {
        try {
            $endpoint = $this->baseUrl . "transactions/{$paymentId}/capture";
            
            $data = [];
            if ($amount) {
                $data['amount'] = $amount;
            }
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->put($endpoint, $data);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro ao capturar transação na Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao capturar transação',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção ao capturar transação na Rede Itaú', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao capturar transação: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }
    
    /**
     * Cancela uma transação
     */
    public function cancel($paymentId, $amount = null)
    {
        try {
            $endpoint = $this->baseUrl . "transactions/{$paymentId}/refund";
            
            $data = [];
            if ($amount) {
                $data['amount'] = $amount;
            }
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($endpoint, $data);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro ao cancelar transação na Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao cancelar transação',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção ao cancelar transação na Rede Itaú', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao cancelar transação: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }
    
    /**
     * Consulta uma transação pelo ID
     */
    public function getTransaction($paymentId)
    {
        try {
            $endpoint = $this->baseUrl . "transactions/{$paymentId}";
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->get($endpoint);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro ao consultar transação na Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao consultar transação',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar transação na Rede Itaú', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao consultar transação: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }
    
    /**
     * Formata os dados para uma transação de crédito
     */
    /**
     * Consulta uma transação PIX pelo ID
     *
     * @param string $paymentId ID da transação PIX
     * @return array
     */
    public function getPixTransaction($paymentId)
    {
        try {
            $endpoint = $this->baseUrl . "transactions/{$paymentId}/pix";
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->get($endpoint);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro ao consultar transação PIX na Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao consultar transação PIX',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar transação PIX na Rede Itaú', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao consultar transação PIX: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }
    
    /**
     * Cria uma transação PIX
     *
     * @param array $params Parâmetros da transação
     * @return array
     */
    public function createPix($params)
    {
        try {
            $endpoint = $this->baseUrl . 'transactions/pix';
            
            $response = Http::withBasicAuth($this->merchantId, $this->merchantKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($endpoint, $this->formatPixRequest($params));
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            Log::error('Erro na API PIX da Rede Itaú', [
                'response' => $response->json() ?? $response->body(),
                'status' => $response->status(),
                'params' => $params
            ]);
            
            return [
                'success' => false,
                'message' => $response->json()['returnMessage'] ?? 'Erro ao processar pagamento PIX',
                'error_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Exceção na API PIX da Rede Itaú', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro interno ao processar pagamento PIX: ' . $e->getMessage(),
                'error_code' => 500
            ];
        }
    }

    /**
     * Formata os dados para uma transação PIX
     *
     * @param array $params Parâmetros da transação
     * @return array
     */
    protected function formatPixRequest($params)
    {
        $pixData = [
            'reference' => $params['reference'],
            'amount' => (int)($params['amount'] * 100), // Convertendo para centavos
            'calendar' => [
                'expiration' => $params['expiration'] ?? 86400 // 24 horas em segundos
            ],
            'softDescriptor' => $params['soft_descriptor'] ?? 'Ver&Vermelho',
        ];
        
        // Adicionar dados do comprador se fornecidos
        if (!empty($params['customer'])) {
            $pixData['customer'] = [
                'name' => $params['customer']['name'] ?? '',
                'email' => $params['customer']['email'] ?? '',
                'phone' => $params['customer']['phone'] ?? ''
            ];
            
            // Adicionar CPF/CNPJ se fornecido
            if (!empty($params['customer']['document'])) {
                $pixData['customer']['document'] = $params['customer']['document'];
            }
        }
        
        return $pixData;
    }

    /**
     * Formata os dados para uma transação de crédito
     */
    protected function formatCreditRequest($params)
    {
        $creditCardData = [
            'kind' => 'credit', // ou 'debit'
            'reference' => $params['reference'],
            'amount' => (int)($params['amount'] * 100), // Convertendo para centavos
            'cardholderName' => $params['card_holder_name'],
            'cardNumber' => $params['card_number'],
            'expirationMonth' => $params['expiration_month'],
            'expirationYear' => $params['expiration_year'],
            'securityCode' => $params['security_code'],
            'softDescriptor' => $params['soft_descriptor'] ?? 'Ver&Vermelho',
            'installments' => $params['installments'] ?? 1,
            'capture' => $params['auto_capture'] ?? true,
        ];
        
        // Adicionar dados do comprador se fornecidos
        if (!empty($params['customer'])) {
            $creditCardData['customer'] = [
                'name' => $params['customer']['name'] ?? '',
                'email' => $params['customer']['email'] ?? '',
                'phone' => $params['customer']['phone'] ?? ''
            ];
            
            // Adicionar CPF/CNPJ se fornecido
            if (!empty($params['customer']['document'])) {
                $creditCardData['customer']['document'] = $params['customer']['document'];
            }
        }
        
        // Adicionar dados do endereço se fornecidos
        if (!empty($params['address'])) {
            $creditCardData['customer']['address'] = [
                'street' => $params['address']['street'] ?? '',
                'number' => $params['address']['number'] ?? '',
                'complement' => $params['address']['complement'] ?? '',
                'zipCode' => $params['address']['zip_code'] ?? '',
                'city' => $params['address']['city'] ?? '',
                'state' => $params['address']['state'] ?? '',
                'country' => $params['address']['country'] ?? 'Brasil',
            ];
        }
        
        return $creditCardData;
    }
}
