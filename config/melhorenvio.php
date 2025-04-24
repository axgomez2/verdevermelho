<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Melhor Envio Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com o Melhor Envio
    |
    */

    'token' => env('MELHOR_ENVIO_TOKEN'),
    'sandbox' => env('MELHOR_ENVIO_SANDBOX', true),

    // Informações do remetente
    'from' => [
        'name' => env('MELHOR_ENVIO_FROM_NAME', 'Sá Sem Baixada'),
        'phone' => env('MELHOR_ENVIO_FROM_PHONE'),
        'email' => env('MELHOR_ENVIO_FROM_EMAIL'),
        'document' => env('MELHOR_ENVIO_FROM_DOCUMENT'),
        'address' => env('MELHOR_ENVIO_FROM_ADDRESS'),
        'number' => env('MELHOR_ENVIO_FROM_NUMBER'),
        'complement' => env('MELHOR_ENVIO_FROM_COMPLEMENT'),
        'district' => env('MELHOR_ENVIO_FROM_DISTRICT'),
        'city' => env('MELHOR_ENVIO_FROM_CITY'),
        'state' => env('MELHOR_ENVIO_FROM_STATE'),
        'postal_code' => env('MELHOR_ENVIO_FROM_POSTAL_CODE'),
    ],

    // Configurações de serviços disponíveis
    'services' => [
        1 => ['name' => 'PAC', 'company' => 'Correios'],
        2 => ['name' => 'SEDEX', 'company' => 'Correios'],
        3 => ['name' => 'Mini Envios', 'company' => 'Correios'],
        4 => ['name' => 'SEDEX 12', 'company' => 'Correios'],
    ],

    // Configurações padrão para cálculo de frete
    'defaults' => [
        'insurance' => true,
        'receipt' => false,
        'own_hand' => false,
        'collect' => false,
        'non_commercial' => true,
        'dimensions' => [
            'width' => 15,
            'height' => 15,
            'length' => 15,
            'weight' => 0.5,
        ],
    ],

    // Tempo de cache para cálculos de frete (em minutos)
    'cache_time' => 30,
];
