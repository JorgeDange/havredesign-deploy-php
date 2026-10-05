<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Database;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Recuperar palavra-passe — envia link por e-mail.
 */
final class RecuperarController
{
    public function formulario(Request $request): Response
    {
        return Response::html(View::render('auth/forgot-password'));
    }

    public function enviar(Request $request): Response
    {
        // Throttle por IP (4.8) — evita spam de e-mails de recuperação
        $bloqueio = \App\Core\Throttle::verificar('recuperar', $request->ip());
        if ($bloqueio !== null) {
            Session::flash('erro', $bloqueio);

            return Response::redirect('/recuperar-palavra-passe');
        }

        $email = $request->input('email');

        $validado = Validator::fazer(
            ['email' => $email],
            ['email' => 'required|email|max:255'],
            ['email' => 'e-mail']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['email' => $email]);

            return Response::redirect('/recuperar-palavra-passe');
        }

        $utilizador = Database::um('SELECT id, name, email FROM users WHERE email = ? LIMIT 1', [$email]);

        if ($utilizador !== null) {
            // Token opaco; na BD guarda-se só o hash (se a BD vazar, o token não serve)
            $token = bin2hex(random_bytes(32));

            Database::executar(
                'INSERT INTO password_reset_tokens (email, token, created_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at)',
                [$email, hash('sha256', $token)]
            );

            $link = \App\Core\Config::obter('APP_URL') . '/redefinir-palavra-passe/' . $token;

            Mailer::enviar(
                $email,
                'Recuperação de palavra-passe — HAVREDESIGN',
                'password-reset',
                ['nome' => (string) $utilizador['name'], 'link' => $link]
            );
        }

        // Mensagem igual quer exista ou não (não revelar quem tem conta)
        \App\Core\Throttle::registar('recuperar', $request->ip());

        Session::flash('ok', 'Se esse e-mail estiver registado, receberá um link para redefinir a palavra-passe.');

        return Response::redirect('/recuperar-palavra-passe');
    }
}
