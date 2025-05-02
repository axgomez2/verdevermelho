<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RefreshMelhorEnvioToken extends Command
{
    protected $signature = 'melhorenvio:refresh-token';
    protected $description = 'Refresh the Melhor Envio token using refresh_token';

    public function handle()
    {
        $this->info('Refreshing Melhor Envio token...');

        $clientId = config('melhorenvio.client_id') ?? env('MELHOR_ENVIO_CLIENT_ID');
        $clientSecret = config('melhorenvio.client_secret') ?? env('MELHOR_ENVIO_CLIENT_SECRET');
        $refreshToken = config('melhorenvio.refresh_token') ?? env('MELHOR_ENVIO_REFRESH_TOKEN');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            $this->error('Missing required credentials. Please check your .env file for MELHOR_ENVIO_CLIENT_ID, MELHOR_ENVIO_CLIENT_SECRET, and MELHOR_ENVIO_REFRESH_TOKEN.');
            return 1;
        }

        $this->info('Requesting new access token...');

        $isSandbox = config('melhorenvio.sandbox', true);
        $baseUrl = $isSandbox 
            ? 'https://sandbox.melhorenvio.com.br'
            : 'https://www.melhorenvio.com.br';

        try {
            $response = Http::post($baseUrl . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->info('New token obtained successfully!');
                $this->info('Access Token: ' . $data['access_token']);
                $this->info('Refresh Token: ' . $data['refresh_token']);
                $this->info('Expires In: ' . $data['expires_in'] . ' seconds');

                // Save the new tokens to .env file
                $this->updateEnvFile([
                    'MELHOR_ENVIO_TOKEN' => $data['access_token'],
                    'MELHOR_ENVIO_REFRESH_TOKEN' => $data['refresh_token']
                ]);

                $this->info('.env file updated with new tokens.');
                return 0;
            } else {
                $this->error('Failed to refresh token: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Update the .env file with new values
     */
    protected function updateEnvFile(array $values)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            if (strpos($envContent, $key . '=') !== false) {
                $envContent = preg_replace(
                    "/^{$key}=.*$/m",
                    "{$key}=\"{$value}\"",
                    $envContent
                );
            } else {
                $envContent .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
