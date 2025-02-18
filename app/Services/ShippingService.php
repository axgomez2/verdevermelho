<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ShippingService
{
    protected $apiToken;
    protected $baseUrl = 'https://sandbox.melhorenvio.com.br/api/v2/'; // Use a URL de produção quando estiver pronto

    public function __construct()
    {
        $this->apiToken = config('services.melhorenvio.token');
    }

    public function calculateShipping($cartItems, $zipCode)
    {
        Log::info('Calculating shipping for zip code: ' . $zipCode);
        Log::info('Cart items:', $cartItems->toArray());

        $payload = [
            'from' => [
                'postal_code' => config('services.melhorenvio.from_postal_code'),
            ],
            'to' => [
                'postal_code' => $zipCode,
            ],
            'products' => $this->formatCartItemsForShipping($cartItems),
        ];

        Log::info('Payload to Melhor Envio:', $payload);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiToken,
        ])->post($this->baseUrl . 'me/shipment/calculate', $payload);

        Log::info('Melhor Envio API Response:', $response->json());

        if ($response->failed()) {
            Log::error('Melhor Envio API request failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response->json();
    }



    protected function formatCartItemsForShipping($cartItems)
    {
        return $cartItems->map(function ($item) {
            return [
                'id' => $item->product->id,
                'width' => $item->product->width,
                'height' => $item->product->height,
                'length' => $item->product->length,
                'weight' => $item->product->weight,
                'insurance_value' => $item->product->price,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }
}
