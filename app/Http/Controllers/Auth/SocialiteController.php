<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do provedor.
     *
     * @param string $provider
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        \Log::info("Redirecionando para o provedor: {$provider}");
        \Log::info("URI de redirecionamento configurado: " . config('services.google.redirect'));
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtém as informações do usuário do provedor.
     *
     * @param string $provider
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            \Log::info("Iniciando autenticação social com: {$provider}");
            $socialUser = Socialite::driver($provider)->user();
            \Log::info("Dados recebidos do provedor: " . json_encode([
                "email" => $socialUser->getEmail(), 
                "name" => $socialUser->getName(),
                "id" => $socialUser->getId()
            ]));

            // Primeiro verifica se o usuário já existe pelo google_id
            $user = User::where('google_id', $socialUser->getId())->first();

            // Se não encontrar pelo google_id, busca pelo email
            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            // Se não existir, cria um novo usuário
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'google_id' => $socialUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Senha aleatória
                    'email_verified_at' => now(), // Usuário já verificado pelo provedor
                ]);
            } 
            // Se o usuário existe mas não tem google_id, atualiza
            elseif (!$user->google_id) {
                $user->google_id = $socialUser->getId();
                $user->save();
            }

            // Autentica o usuário
            Auth::login($user, true);

            return redirect()->intended('/dashboard');

        } catch (Exception $e) {
            \Log::error("Erro na autenticação social: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect('/login')->with('error', 'Ocorreu um erro durante a autenticação social: ' . $e->getMessage());
        }
    }
}
