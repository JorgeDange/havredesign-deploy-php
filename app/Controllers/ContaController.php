<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

/**
 * Conta — área de cliente (pedidos e agendamentos do próprio).
 */
final class ContaController
{
    public function index(Request $request): Response
    {
        $userId = Auth::id();

        $pedidos = Database::todos(
            'SELECT id, project_type, location, budget, status, created_at FROM project_requests
             WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
            [$userId]
        );

        $agendamentos = Database::todos(
            'SELECT id, type, appt_date, appt_time, status, notes, address FROM appointments
             WHERE user_id = ? ORDER BY appt_date DESC, appt_time DESC LIMIT 50',
            [$userId]
        );

        // Tipos de reunião (settings.agenda): código => ['label' => ...]
        $tipos = [];
        foreach (\App\Support\Site::json('agenda', [])['tipos'] ?? [] as $codigo => $info) {
            $tipos[$codigo] = ['label' => (string) ($info['label'] ?? $codigo)];
        }

        return Response::html(View::render('conta/index', [
            'user'         => Auth::utilizador(),
            'pedidos'      => $pedidos,
            'agendamentos' => $agendamentos,
            'tipos'        => $tipos,
        ]));
    }
}
