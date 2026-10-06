<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

/**
 * CsrfMiddleware — valida o token de todos os POST ('csrf').
 * Falhou → 419 (não redireccionar: o utilizador não deve reenviar o form às cegas).
 */
final class CsrfMiddleware
{
    public static function handle(Request $request): ?Response
    {
        if (!$request->ePost()) {
            return null;
        }

        $token = $request->bruto('_token');
        $token = is_string($token) ? $token : ($request->bruto('_csrf') ?? null);
        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        $token = $token ?? (is_string($header) ? $header : null);

        if (!\App\Core\Csrf::validar($token)) {
            \App\Core\Logger::aviso('csrf.token_invalido', [
                'ip'            => $request->ip(),
                'rota'          => $request->caminho(),
                'session_id'    => session_id(),
                'session_status'=> session_status(),
                'cookie_params' => session_get_cookie_params(),
                'token_recebido'=> $token ? substr($token, 0, 8) . '...' : 'vazio',
                'token_esperado'=> \App\Core\Session::obter('_csrf_token') ? substr(\App\Core\Session::obter('_csrf_token'), 0, 8) . '...' : 'vazio',
                'cookies'       => array_keys($_COOKIE),
            ]);

            return Response::html(\App\Core\View::render('errors/419'), 419);
        }

        return null;
    }
}
