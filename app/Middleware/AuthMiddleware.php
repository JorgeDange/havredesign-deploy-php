<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

/**
 * AuthMiddleware — exige sessão autenticada ('auth').
 * Sem autenticação → redirect para /entrar com intenção guardada.
 */
final class AuthMiddleware
{
    public static function handle(Request $request): ?Response
    {
        Auth::tentarLembrar();

        if (!Auth::autenticado()) {
            \App\Core\Session::flash('erro', 'Faça login para aceder a essa página.');

            // Guardar o destino pretendido (intended)
            \App\Core\Session::colocar('_intended', $request->caminho());

            return Response::redirect('/entrar');
        }

        return null;
    }
}
