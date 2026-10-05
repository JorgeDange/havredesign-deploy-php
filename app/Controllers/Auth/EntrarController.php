<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Entrar — formulário e autenticação (login).
 */
final class EntrarController
{
    public function formulario(Request $request): Response
    {
        return Response::html(View::render('auth/login'));
    }

    public function entrar(Request $request): Response
    {
        $email    = $request->input('email');
        $senha    = (string) $request->bruto('password', '');
        $lembrar  = (string) $request->bruto('remember', '') !== '';
        $validado = Validator::fazer(
            ['email' => $email, 'password' => $senha],
            ['email' => 'required|email|max:255', 'password' => 'required|max:100'],
            ['email' => 'e-mail', 'password' => 'palavra-passe']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['email' => $email]);

            return Response::redirect('/entrar');
        }

        $erro = Auth::tentarLogin($email, $senha, $lembrar, $request->ip());

        if ($erro !== null) {
            Session::flash('erro', $erro);
            Session::colocar('_old_input', ['email' => $email]);

            return Response::redirect('/entrar');
        }

        // Intenção guardada ou destino por papel
        $destino = Session::obter('_intended', null, true);
        if (!is_string($destino) || $destino === '' || $destino === '/sair') {
            $destino = Auth::eAdmin() ? '/' . Config::obter('ADMIN_PATH', 'admin') : '/conta';
        }

        return Response::redirect($destino);
    }
}
