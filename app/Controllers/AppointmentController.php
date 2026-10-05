<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Support\Agenda;

/**
 * AppointmentController — POST /agendar e GET /agendar/disponibilidade.
 *
 * Espelha StoreAppointmentRequest + AgendaService do backend (§7).
 */
final class AppointmentController
{
    /**
     * GET /agendar/disponibilidade?mes=YYYY-MM — JSON de dias com vagas.
     */
    public function disponibilidade(Request $request): Response
    {
        $mes = $request->query('mes', date('Y-m'));

        if (!preg_match('/^(\d{4})-(\d{2})$/', $mes, $m)) {
            return Response::json(['error' => 'Formato de mês inválido.'], 422);
        }

        $ano = (int) $m[1];
        $numero = (int) $m[2];

        if ($numero < 1 || $numero > 12) {
            return Response::json(['error' => 'Mês inválido.'], 422);
        }

        return Response::json([
            'mes'  => $mes,
            'dias' => Agenda::mes($ano, $numero),
        ]);
    }

    /**
     * POST /agendar — valida e grava a marcação.
     */
    public function store(Request $request): Response
    {
        // Throttle por IP (4.8)
        $bloqueio = \App\Core\Throttle::verificar('agendar', $request->ip());
        if ($bloqueio !== null) {
            Session::flash('erro', $bloqueio);

            return Response::redirect('/agendar');
        }

        $nome    = $request->input('user_name');
        $email   = $request->input('user_email');
        $telefone = $request->input('user_phone');
        $tipo    = $request->input('type');
        $morada  = $request->input('address');
        $data    = $request->input('appt_date');
        $hora    = $request->input('appt_time');
        $notas   = (string) $request->bruto('notes', '');
        $armadilha = (string) $request->bruto('homepage', '');

        $validado = Validator::fazer(
            [
                'type' => $tipo, 'address' => $morada, 'appt_date' => $data, 'appt_time' => $hora,
                'user_name' => $nome, 'user_email' => $email, 'user_phone' => $telefone,
                'notes' => $notas, 'homepage' => $armadilha,
            ],
            [
                'homepage'   => 'honeypot',
                'type'       => 'required|in:SITE,ONLINE',
                'address'    => 'nullable|string|max:500',
                'appt_date'  => 'required|date_format:Y-m-d',
                'appt_time'  => 'required|date_format:H:i',
                'user_name'  => 'required|string|max:150',
                'user_email' => 'required|email|max:255',
                'user_phone' => 'required|string|max:30',
                'notes'      => 'nullable|string|max:1000',
            ],
            [
                'type'       => 'tipo de reunião',
                'address'    => 'endereço',
                'appt_date'  => 'data',
                'appt_time'  => 'horário',
                'user_name'  => 'nome',
                'user_email' => 'e-mail',
                'user_phone' => 'telefone',
                'notes'      => 'observações',
            ]
        );

        // after_or_equal:today + regras de negócio da agenda
        if ($validado->passou()) {
            if ($data < date('Y-m-d')) {
                $validado->erro('appt_date', 'A data tem de ser hoje ou posterior.');
            } elseif ($tipo === 'SITE' && $morada === '') {
                $validado->erro('address', 'Informe o endereço do local para a visita.');
            } elseif (!in_array($hora, Agenda::livres($data), true)) {
                $validado->erro('appt_time', 'Esse horário já não está disponível. Escolha outro.');
            }
        }

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'user_name' => $nome, 'user_email' => $email, 'user_phone' => $telefone,
                'type' => $tipo, 'address' => $morada, 'appt_date' => $data,
                'appt_time' => $hora, 'notes' => $notas,
            ]);

            return Response::redirect('/agendar');
        }

        $id = novo_uuid();

        Database::executar(
            'INSERT INTO appointments
             (id, user_id, user_name, user_email, user_phone, type, address, meeting_link,
              appt_date, appt_time, notes, timezone, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, \'Africa/Luanda\', \'PENDING\', NOW(), NOW())',
            [
                $id,
                Auth::id(),
                $nome,
                $email,
                $telefone,
                $tipo,
                $tipo === 'SITE' && $morada !== '' ? $morada : null,
                $data,
                $hora,
                $notas !== '' ? $notas : null,
            ]
        );

        $rotulo = Agenda::rotuloTipo($tipo);
        $resumo = [
            'cliente' => $nome . ' (' . $email . ')',
            'data'    => (new \DateTimeImmutable($data))->format('d/m/Y'),
            'hora'    => substr($hora, 0, 5),
            'tipo'    => $rotulo,
            'local'   => $tipo === 'ONLINE' ? 'Online' : $morada,
            'estado'  => 'Por confirmar',
        ];

        try {
            Mailer::enviarAdmin('Novo pedido de agendamento: ' . $resumo['data'] . ' ' . $resumo['hora'], 'appointment-received', ['a' => $resumo, 'notas' => $notas]);
        } catch (\Throwable $e) {
            Logger::erro('appointment.received_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        try {
            Mailer::enviar($email, 'Pedido de agendamento — HAVREDESIGN', 'appointment-ack', ['a' => $resumo]);
        } catch (\Throwable $e) {
            Logger::erro('appointment.ack_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        Logger::info('appointment.received', ['id' => $id, 'data' => $data, 'hora' => $hora]);

        \App\Core\Throttle::registar('agendar', $request->ip());

        Session::flash('ok', 'Agendamento pedido com sucesso! Iremos confirmar por e-mail.');
        Session::flash('appointment_submitted', $resumo);

        return Response::redirect('/agendar');
    }
}
