<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

/**
 * AdminMiddleware — exige papel ADMIN ('admin').
 * Utilizador comum → 403; anónimo → redirect para /entrar.
 */
final class AdminMiddleware
{
    public static function handle(Request $request): ?Response
    {
        Auth::tentarLembrar();

        if (!Auth::autenticado()) {
            \App\Core\Session::flash('erro', 'Faça login para aceder ao painel.');
            \App\Core\Session::colocar('_intended', $request->caminho());

            return Response::redirect('/entrar');
        }

        if (!Auth::eAdmin()) {
            return Response::html(View::render('errors/403'), 403);
        }

        return null;
    }
}
