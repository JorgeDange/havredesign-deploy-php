<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Support\Agenda;

/**
 * AppointmentController (admin) — agendamentos no painel.
 *
 * Espelha App\Http\Controllers\Admin\AppointmentController do backend:
 * lista com filtros, detalhe e mudança de estado (PENDING/CONFIRMED/CANCELLED)
 * com aviso ao cliente por e-mail — o envio nunca pode rebentar o pedido.
 */
final class AppointmentController
{
    /** Estados possíveis (código => rótulo PT). */
    public const ESTADOS = [
        'PENDING'   => 'Por confirmar',
        'CONFIRMED' => 'Confirmado',
        'CANCELLED' => 'Cancelado',
    ];

    // ------------------------------------------------------------------
    // GET /admin/agendamentos — lista com filtros ?status= e ?data=
    // ------------------------------------------------------------------
    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', '');
        if (!array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $data = (string) $request->query('data', '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $data = '';
        }

        $base       = $data === '' ? '' : ' WHERE appt_date = ?';
        $parametros = $data === '' ? [] : [$data];

        $contagens = ['total' => (int) Database::escalar('SELECT COUNT(*) FROM appointments' . $base, $parametros)];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $sql = $base === ''
                ? 'SELECT COUNT(*) FROM appointments WHERE status = ?'
                : 'SELECT COUNT(*) FROM appointments' . $base . ' AND status = ?';
            $contagens[$codigo] = (int) Database::escalar($sql, array_merge($parametros, [$codigo]));
        }

        $filtros   = $base === '' ? [] : ['appt_date = ?'];
        $filtrosP  = $parametros;
        if ($status !== '') {
            $filtros[]  = 'status = ?';
            $filtrosP[] = $status;
        }

        $agendamentos = Database::todos(
            'SELECT * FROM appointments'
            . ($filtros === [] ? '' : ' WHERE ' . implode(' AND ', $filtros))
            . ' ORDER BY appt_date, appt_time',
            $filtrosP
        );

        return Response::html(View::render('admin/agendamentos/index', [
            'agendamentos' => $agendamentos,
            'status'       => $status,
            'data'         => $data,
            'estados'      => self::ESTADOS,
            'contagens'    => $contagens,
            'tipos'        => $this->tipos(),
        ]));
    }

    // ------------------------------------------------------------------
    // GET /admin/agendamentos/{id} — detalhe + formulário de estado
    // ------------------------------------------------------------------
    public function mostrar(Request $request): Response
    {
        $id           = (string) $request->parametro('id');
        $agendamento  = $this->agendamento($id);

        if ($agendamento === null) {
            return $this->naoEncontrado();
        }

        $conta = null;
        if (!empty($agendamento['user_id'])) {
            $conta = Database::escalar('SELECT name FROM users WHERE id = ?', [$agendamento['user_id']]);
        }

        return Response::html(View::render('admin/agendamentos/mostrar', [
            'agendamento' => $agendamento,
            'estados'     => self::ESTADOS,
            'tipos'       => $this->tipos(),
            'conta'       => $conta,
        ]));
    }

    // ------------------------------------------------------------------
    // POST /admin/agendamentos/{id} — estado (+ nota/taxa) e e-mail ao cliente
    // ------------------------------------------------------------------
    public function atualizar(Request $request): Response
    {
        $id          = (string) $request->parametro('id');
        $agendamento = $this->agendamento($id);

        if ($agendamento === null) {
            return $this->naoEncontrado();
        }

        $status = (string) $request->input('status', '');
        $nota   = $request->bruto('fee_note', '');
        $nota   = is_string($nota) ? trim($nota) : '';

        $validado = Validator::fazer(
            ['status' => $status, 'fee_note' => $nota],
            [
                'status'   => 'required|in:' . implode(',', array_keys(self::ESTADOS)),
                'fee_note' => 'nullable|string|max:500',
            ],
            ['status' => 'estado', 'fee_note' => 'nota/taxa']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['status' => $status, 'fee_note' => $nota]);

            return Response::redirect(rota('admin.agendamentos') . '/' . rawurlencode($id));
        }

        $novoEstado = $status;
        $mudou      = $novoEstado !== (string) $agendamento['status'];
        $notaLimpa  = $nota !== '' ? $nota : null;

        $sql       = 'UPDATE appointments SET status = ?, fee_note = ?';
        $parametros = [$novoEstado, $notaLimpa];

        if ($mudou && $novoEstado === 'CONFIRMED') {
            $sql .= ', confirmed_at = NOW()';
        }
        if ($mudou && $novoEstado === 'CANCELLED') {
            $sql .= ', cancelled_at = NOW()';
        }

        $sql         .= ' WHERE id = ?';
        $parametros[] = $id;

        Database::executar($sql, $parametros);

        Logger::info('admin.agendamento.estado_alterado', ['id' => $id, 'status' => $novoEstado]);

        // Aviso ao cliente — nunca pode rebentar o pedido
        $email = trim((string) ($agendamento['user_email'] ?? ''));
        if ($mudou && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $resumo = [
                'codigo'   => $novoEstado,
                'cliente'  => (string) $agendamento['user_name'],
                'telefone' => (string) $agendamento['user_phone'],
                'data'     => formatar_data((string) $agendamento['appt_date']),
                'hora'     => substr((string) $agendamento['appt_time'], 0, 5),
                'tipo'     => Agenda::rotuloTipo((string) $agendamento['type']),
                'morada'   => $agendamento['address'],
                'ligacao'  => $agendamento['meeting_link'],
                'nota'     => $notaLimpa,
                'estado'   => self::ESTADOS[$novoEstado] ?? $novoEstado,
            ];

            $assunto = match ($novoEstado) {
                'CONFIRMED' => 'O seu agendamento foi confirmado — HAVREDESIGN',
                'CANCELLED' => 'O seu agendamento foi cancelado — HAVREDESIGN',
                default     => 'O seu agendamento foi atualizado — HAVREDESIGN',
            };

            try {
                Mailer::enviar($email, $assunto, 'appointment-status', ['a' => $resumo]);
            } catch (\Throwable $e) {
                Logger::erro('appointment.status_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
            }
        }

        Session::flash('ok', 'Agendamento atualizado.');

        return Response::redirect(rota('admin.agendamentos') . '/' . rawurlencode($id));
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * @return array<string, mixed>|null
     */
    private function agendamento(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        return Database::um('SELECT * FROM appointments WHERE id = ?', [$id]);
    }

    /**
     * Rótulos PT dos tipos (SITE/ONLINE).
     *
     * @return array<string, string>
     */
    private function tipos(): array
    {
        $padrao = [
            'SITE'   => 'No local do cliente',
            'ONLINE' => 'Online',
        ];

        $tipos = [];
        foreach ($padrao as $codigo => $rotulo) {
            $definido  = Agenda::rotuloTipo($codigo);
            $tipos[$codigo] = $definido === $codigo ? $rotulo : $definido;
        }

        return $tipos;
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }
}
