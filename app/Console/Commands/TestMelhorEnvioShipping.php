<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MelhorEnvioService;
use App\Services\ShippingService;
use App\Models\Product;

class TestMelhorEnvioShipping extends Command
{
    protected $signature = 'melhorenvio:test-shipping {postal_code}';
    protected $description = 'Test Melhor Envio shipping calculation with sample products';

    protected $melhorEnvioService;
    protected $shippingService;

    public function __construct(MelhorEnvioService $melhorEnvioService, ShippingService $shippingService)
    {
        parent::__construct();
        $this->melhorEnvioService = $melhorEnvioService;
        $this->shippingService = $shippingService;
    }

    public function handle()
    {
        $postalCode = $this->argument('postal_code');
        
        if (!$postalCode || strlen(preg_replace('/\D/', '', $postalCode)) !== 8) {
            $this->error('Por favor, forneça um CEP válido com 8 dígitos.');
            return 1;
        }
        
        $this->info('Testando cálculo de frete para o CEP: ' . $postalCode);
        
        // Criar itens de teste simulando produtos do carrinho
        $testItems = $this->createTestItems();
        
        if (empty($testItems)) {
            $this->error('Não foi possível criar itens de teste. Verifique se existem produtos no banco de dados.');
            return 1;
        }
        
        $this->info('Criados ' . count($testItems) . ' itens de teste para simulação');
        
        // Testar cálculo direto via MelhorEnvioService
        $this->info('Calculando frete via MelhorEnvioService...');
        try {
            $options = $this->melhorEnvioService->calculateShipping($testItems, $postalCode);
            
            if (!empty($options)) {
                $this->info('Opções de frete encontradas: ' . count($options));
                $this->table(
                    ['ID', 'Serviço', 'Empresa', 'Preço', 'Prazo'],
                    array_map(function($option) {
                        return [
                            $option['id'],
                            $option['name'],
                            $option['company'],
                            'R$ ' . number_format($option['price'], 2, ',', '.'),
                            $option['delivery_time']
                        ];
                    }, $options)
                );
            } else {
                $this->warn('Nenhuma opção de frete disponível para este CEP.');
            }
        } catch (\Exception $e) {
            $this->error('Erro ao calcular via MelhorEnvioService: ' . $e->getMessage());
        }
        
        // Testar cálculo via ShippingService (abordagem mais genérica)
        $this->info('Calculando frete via ShippingService...');
        try {
            $options = $this->shippingService->calculateShipping($testItems, $postalCode);
            
            if (!empty($options)) {
                $this->info('Opções de frete encontradas: ' . count($options));
                $this->table(
                    ['ID', 'Serviço', 'Empresa', 'Preço', 'Prazo'],
                    array_map(function($option) {
                        return [
                            $option['id'],
                            $option['name'],
                            $option['company'],
                            'R$ ' . number_format($option['price'], 2, ',', '.'),
                            $option['delivery_time']
                        ];
                    }, $options)
                );
            } else {
                $this->warn('Nenhuma opção de frete disponível para este CEP via ShippingService.');
            }
        } catch (\Exception $e) {
            $this->error('Erro ao calcular via ShippingService: ' . $e->getMessage());
        }
        
        // Verificar configuração Melhor Envio
        $this->info('Verificando configuração do Melhor Envio...');
        
        $config = config('melhorenvio');
        $this->info('Ambiente: ' . ($config['sandbox'] ? 'Sandbox (teste)' : 'Produção'));
        
        if (empty($config['token'])) {
            $this->error('Token não configurado! Adicione MELHOR_ENVIO_TOKEN no arquivo .env');
        } else {
            $this->info('Token configurado: ' . substr($config['token'], 0, 10) . '...' . substr($config['token'], -10));
        }
        
        if (empty($config['from']['postal_code'])) {
            $this->error('CEP de origem não configurado! Adicione MELHOR_ENVIO_FROM_POSTAL_CODE no arquivo .env');
        } else {
            $this->info('CEP de origem: ' . $config['from']['postal_code']);
        }
        
        return 0;
    }
    
    /**
     * Cria itens de teste para simular produtos no carrinho
     */
    protected function createTestItems()
    {
        $testItems = collect();
        
        // Tentar obter alguns produtos reais do banco de dados
        $products = Product::take(3)->get();
        
        if ($products->isNotEmpty()) {
            foreach ($products as $product) {
                $item = new \stdClass();
                $item->product = $product;
                $item->quantity = 1;
                $testItems->push($item);
            }
        } else {
            // Criar produtos fictícios se não houver produtos no banco
            for ($i = 1; $i <= 2; $i++) {
                $product = new \stdClass();
                $product->id = $i;
                $product->name = "Produto Teste $i";
                $product->price = $i * 100;
                
                $item = new \stdClass();
                $item->product = $product;
                $item->quantity = 1;
                $testItems->push($item);
            }
        }
        
        return $testItems;
    }
}
