<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Auth;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;

/**
 * Sair — destruição completa da sessão.
 */
final class SairController
{
    public function sair(Request $request): Response
    {
        Auth::sair();
        Logger::info('auth.logout', ['ip' => $request->ip()]);

        return Response::redirect('/');
    }
}
