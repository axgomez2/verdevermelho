<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        
        // Content Security Policy - Configuração ampliada para permitir todos os recursos necessários
        $cspHeader = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://www.googletagmanager.com https://unpkg.com https://www.youtube.com https://s.ytimg.com https://apis.google.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://unpkg.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; img-src 'self' data: https: blob:; media-src 'self' https:; connect-src 'self' https://*; frame-src 'self' https://www.youtube.com";
        
        // Em ambiente de desenvolvimento, desabilitar a CSP para facilitar o desenvolvimento
        if (app()->environment('local')) {
            // No ambiente local, não aplicamos a CSP para facilitar o desenvolvimento
            // $response->headers->set('Content-Security-Policy-Report-Only', $cspHeader); // Opcionalmente, pode usar modo de relatório
        } else {
            // Em outros ambientes (produção, staging), aplicamos a CSP estrita
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }
        
        return $response;
    }
}
