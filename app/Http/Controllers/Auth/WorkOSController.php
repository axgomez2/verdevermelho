<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use WorkOS\WorkOS;

class WorkOSController extends Controller
{
    protected $workos;

    public function __construct(WorkOS $workos)
    {
        $this->workos = $workos;
    }

    /**
     * Redireciona para a página de login do WorkOS.
     */
    public function redirectToWorkOS(Request $request)
    {
        // Armazena a URL de retorno na sessão, se fornecida
        if ($request->has('redirect')) {
            session(['redirect_after_login' => $request->redirect]);
        }

        // Gera a URL de autorização
        $authorizationUrl = $this->workos->sso()->getAuthorizationURL(
            config('services.workos.client_id'),
            config('services.workos.redirect_uri'),
            [
                'organization' => config('services.workos.organization_id'),
                'state' => Str::random(40),
            ]
        );

        return redirect($authorizationUrl);
    }

    /**
     * Processa o callback do WorkOS após login bem-sucedido.
     */
    public function handleWorkOSCallback(Request $request)
    {
        try {
            // Verifica se temos um código de autorização
            if (!$request->has('code')) {
                return redirect()->route('login')->with('error', 'Autenticação falhou. Por favor, tente novamente.');
            }

            // Obtém o perfil do usuário do WorkOS
            $profile = $this->workos->sso()->getProfileAndToken(
                $request->code,
                config('services.workos.client_id')
            )->profile;

            // Busca o usuário pelo email ou cria um novo
            $user = User::firstOrNew(['email' => $profile->email]);

            if (!$user->exists) {
                // Novo usuário - vamos criar
                $user->fill([
                    'name' => $profile->firstName . ' ' . $profile->lastName,
                    'email' => $profile->email,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 10, // 10 = usuário comum
                ]);
                $user->save();
            }

            // Loga o usuário
            Auth::login($user, true);
            
            // Regenera a sessão para evitar ataques de fixação de sessão
            $request->session()->regenerate();
            
            // Mensagem de boas-vindas
            session()->flash('success', 'Bem-vindo, ' . $user->name . '! Login efetuado com sucesso.');

            // Redireciona com base no papel do usuário
            $redirectTo = session('redirect_after_login');
            if ($redirectTo) {
                session()->forget('redirect_after_login');
                return redirect($redirectTo);
            }

            $authUserRole = $user->role;
            if ($authUserRole == 66) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            } elseif ($authUserRole == 40) {
                return redirect()->intended(route('pdv.dashboard', absolute: false));
            } else {
                return redirect()->intended(route('site.home', absolute: false));
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erro durante a autenticação: ' . $e->getMessage());
        }
    }
}
