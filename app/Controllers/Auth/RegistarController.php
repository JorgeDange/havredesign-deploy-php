<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Registar — criação de conta de cliente.
 * DECISÃO B: sem verificação de e-mail (entra direto).
 */
final class RegistarController
{
    public function formulario(Request $request): Response
    {
        return Response::html(View::render('auth/register'));
    }

    public function registar(Request $request): Response
    {
        $nome   = $request->input('name');
        $email  = $request->input('email');
        $senha  = (string) $request->bruto('password', '');
        $confirm = (string) $request->bruto('password_confirmation', '');

        $validado = Validator::fazer(
            ['name' => $nome, 'email' => $email, 'password' => $senha, 'password_confirmation' => $confirm],
            [
                'name'                 => 'required|string|max:150',
                'email'                => 'required|email|max:255|unique:users,email',
                'password'             => 'required|min:8|max:100|confirmed',
                'password_confirmation' => 'required',
            ],
            ['name' => 'nome', 'email' => 'e-mail', 'password' => 'palavra-passe']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['name' => $nome, 'email' => $email]);

            return Response::redirect('/registar');
        }

        $erro = Auth::registar($nome, $email, $senha);
        if ($erro !== null) {
            Session::flash('erro', $erro);
            Session::colocar('_old_input', ['name' => $nome, 'email' => $email]);

            return Response::redirect('/registar');
        }

        // Login automático após registo
        Auth::tentarLogin($email, $senha, false, $request->ip());
        Session::flash('ok', 'Conta criada com sucesso. Bem-vindo!');

        return Response::redirect('/conta');
    }
}
