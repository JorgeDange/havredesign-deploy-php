<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

/**
 * ContactController — POST /contacto (mensagem de contacto).
 *
 * Regras espelhadas de StoreContactMessageRequest (backend). A falha de envio
 * de e-mail nunca perde a mensagem: grava na BD e regista no log.
 */
final class ContactController
{
    public function store(Request $request): Response
    {
        // Throttle por IP (4.8) — antes de qualquer processamento
        $bloqueio = \App\Core\Throttle::verificar('contacto', $request->ip());
        if ($bloqueio !== null) {
            Session::flash('erro', $bloqueio);

            return Response::redirect('/contacto');
        }

        $nome    = $request->input('name');
        $email   = $request->input('email');
        $telefone = $request->input('phone');
        $assunto = $request->input('subject');
        $mensagem = (string) $request->bruto('message', '');
        $armadilha = (string) $request->bruto('homepage', '');

        $validado = Validator::fazer(
            [
                'name'      => $nome,
                'email'     => $email,
                'phone'     => $telefone,
                'subject'   => $assunto,
                'message'   => $mensagem,
                'homepage'  => $armadilha,
            ],
            [
                'homepage' => 'honeypot',
                'name'     => 'required|string|max:150',
                'email'    => 'required|email|max:255',
                'phone'    => 'nullable|string|max:30',
                'subject'  => 'required|string|max:200',
                'message'  => 'required|string|max:5000',
            ],
            [
                'name'    => 'nome',
                'email'   => 'e-mail',
                'phone'   => 'contacto telefónico',
                'subject' => 'assunto',
                'message' => 'mensagem',
            ]
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'name' => $nome, 'email' => $email, 'phone' => $telefone,
                'subject' => $assunto, 'message' => $mensagem,
            ]);

            return Response::redirect('/contacto');
        }

        $id = novo_uuid();

        Database::executar(
            'INSERT INTO contact_messages (id, name, email, phone, subject, message, privacy_consented_at, status, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, NULL, \'new\', ?)',
            [
                $id,
                $nome,
                $email,
                $telefone !== '' ? $telefone : null,
                $assunto,
                $mensagem,
                $request->ip(),
            ]
        );

        $contacto = [
            'id'      => $id,
            'name'    => $nome,
            'email'   => $email,
            'phone'   => $telefone,
            'subject' => $assunto,
            'message' => $mensagem,
            'ip'      => $request->ip(),
        ];

        // 6.3: falha de e-mail nunca perde a mensagem — grava e regista no log
        try {
            Mailer::enviarAdmin('Novo contacto: ' . $assunto, 'contact-received', ['m' => $contacto]);
        } catch (\Throwable $e) {
            Logger::erro('contact.received_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        try {
            Mailer::enviar($email, 'Mensagem recebida — HAVREDESIGN', 'contact-ack', ['m' => $contacto]);
        } catch (\Throwable $e) {
            Logger::erro('contact.ack_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        Logger::info('contact.received', ['id' => $id, 'ip' => $request->ip()]);

        \App\Core\Throttle::registar('contacto', $request->ip());

        Session::flash('ok', 'Mensagem enviada com sucesso. Entraremos em contacto o mais breve possível.');

        return Response::redirect('/contacto');
    }
}
