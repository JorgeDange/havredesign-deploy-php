<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

/**
 * GuestMiddleware — utilizador autenticado não pode ver /entrar, /registar.
 */
final class GuestMiddleware
{
    public static function handle(Request $request): ?Response
    {
        Auth::tentarLembrar();

        if (Auth::autenticado()) {
            return Response::redirect(Auth::eAdmin() ? '/admin' : '/conta');
        }

        return null;
    }
}
