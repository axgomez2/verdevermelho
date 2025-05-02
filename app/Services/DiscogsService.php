<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscogsService
{
    /**
     * Busca discos no Discogs
     *
     * @param string $query Query de busca
     * @return array Resultados da busca
     * @throws \Exception
     */
    public function search(string $query): array
    {
        try {
            $response = Http::get('https://api.discogs.com/database/search', [
                'q' => $query,
                'type' => 'release',
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                throw new \Exception('Falha ao buscar dados da API do Discogs: ' . $response->body());
            }

            return $response->json()['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar no Discogs: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtém detalhes de um lançamento específico do Discogs
     *
     * @param string $releaseId ID do lançamento no Discogs
     * @return array|null Dados do lançamento ou null se não encontrado
     */
    public function getRelease(string $releaseId): ?array
    {
        try {
            $response = Http::get("https://api.discogs.com/releases/{$releaseId}", [
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                return null;
            }

            $releaseData = $response->json();

            // Buscar informações de preço
            $marketResponse = Http::get("https://api.discogs.com/marketplace/stats/{$releaseId}", [
                'token' => config('services.discogs.token'),
            ]);

            if ($marketResponse->successful()) {
                $marketData = $marketResponse->json();
                
                // Garantir que lowestPrice seja um número antes de usá-lo em cálculos
                $lowestPrice = 0;
                if (isset($marketData['lowest_price'])) {
                    // Converter para float para evitar problemas de tipo
                    $lowestPrice = is_numeric($marketData['lowest_price']) 
                        ? (float)$marketData['lowest_price'] 
                        : 0;
                }

                if ($lowestPrice > 0) {
                    $releaseData['lowest_price'] = $lowestPrice;
                    $releaseData['median_price'] = $lowestPrice * 1.5;
                    $releaseData['highest_price'] = $lowestPrice * 2;

                    Log::info('Preços calculados:', [
                        'menor' => $releaseData['lowest_price'],
                        'médio' => $releaseData['median_price'],
                        'maior' => $releaseData['highest_price']
                    ]);
                }
            }

            return $releaseData;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do Discogs: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca e faz download de uma imagem do Discogs
     *
     * @param string $imageUrl URL da imagem
     * @return string|null Conteúdo da imagem ou null em caso de erro
     */
    public function fetchImage(string $imageUrl): ?string
    {
        try {
            $response = Http::get($imageUrl);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar imagem do Discogs: ' . $e->getMessage());
            return null;
        }
    }
}
