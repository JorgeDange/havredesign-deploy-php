<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Redefinir palavra-passe — formulário com token + gravação nova senha.
 */
final class RedefinirController
{
    public function formulario(Request $request): Response
    {
        $token = $request->parametro('token');
        if ($token === '') {
            // Último segmento do caminho (fallback)
            $pedacos = explode('/', trim($request->caminho(), '/'));

            $token = (string) end($pedacos);
        }

        return Response::html(View::render('auth/reset-password', ['token' => $token]));
    }

    public function redefinir(Request $request): Response
    {
        $token   = $request->input('token');
        $email   = $request->input('email');
        $senha   = (string) $request->bruto('password', '');
        $confirm = (string) $request->bruto('password_confirmation', '');

        $validado = Validator::fazer(
            ['token' => $token, 'email' => $email, 'password' => $senha, 'password_confirmation' => $confirm],
            [
                'token'                 => 'required',
                'email'                 => 'required|email|max:255',
                'password'              => 'required|min:8|max:100|confirmed',
                'password_confirmation' => 'required',
            ],
            ['email' => 'e-mail', 'password' => 'palavra-passe']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['email' => $email]);

            return Response::redirect('/redefinir-palavra-passe/' . rawurlencode($token));
        }

        // Procurar token válido (guardado como hash) com 24h de validade
        $registo = Database::um(
            'SELECT email, created_at FROM password_reset_tokens WHERE token = ? LIMIT 1',
            [hash('sha256', $token)]
        );

        $valido = $registo !== null
            && $registo['email'] === $email
            && $registo['created_at'] !== null
            && (time() - strtotime((string) $registo['created_at'])) < 86400;

        if (!$valido) {
            Session::flash('erro', 'O link de recuperação é inválido ou expirou. Peça um novo.');

            return Response::redirect('/recuperar-palavra-passe');
        }

        $utilizador = Database::um('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($utilizador === null) {
            Session::flash('erro', 'O link de recuperação é inválido ou expirou.');

            return Response::redirect('/recuperar-palavra-passe');
        }

        Auth::alterarSenha((string) $utilizador['id'], $senha);

        // Token de uso único — apagar
        Database::executar('DELETE FROM password_reset_tokens WHERE email = ?', [$email]);

        Session::flash('ok', 'Palavra-passe alterada com sucesso. Já pode entrar.');

        return Response::redirect('/entrar');
    }
}
