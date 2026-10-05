<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

/**
 * Painel de administração — resumo da operação.
 */
final class DashboardController
{
    public function index(Request $request): Response
    {
        $hoje = date('Y-m-d');

        $contagens = [
            'servicos'             => (int) Database::escalar('SELECT COUNT(*) FROM services'),
            'portfolio'            => (int) Database::escalar('SELECT COUNT(*) FROM portfolio_items'),
            'solucoes'             => (int) Database::escalar('SELECT COUNT(*) FROM solutions'),
            'pedidosNovos'         => (int) Database::escalar("SELECT COUNT(*) FROM project_requests WHERE status = 'NEW'"),
            'agendamentosPendentes' => (int) Database::escalar(
                "SELECT COUNT(*) FROM appointments WHERE status = 'PENDING' AND appt_date >= ?",
                [$hoje]
            ),
            'mensagensNovas'       => (int) Database::escalar("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"),
            'testemunhosOcultos'   => (int) Database::escalar("SELECT COUNT(*) FROM testimonials WHERE status = 'hidden'"),
            'utilizadores'         => (int) Database::escalar('SELECT COUNT(*) FROM users'),
        ];

        $pedidosRecentes = Database::todos(
            'SELECT id, project_type, user_name, status, created_at FROM project_requests
             ORDER BY created_at DESC LIMIT 5'
        );

        $agendamentosProximos = Database::todos(
            'SELECT id, appt_date, appt_time, user_name, status FROM appointments
             WHERE appt_date >= ? ORDER BY appt_date, appt_time LIMIT 6',
            [$hoje]
        );

        $mensagensRecentes = Database::todos(
            'SELECT id, name, message, status, created_at FROM contact_messages
             ORDER BY created_at DESC LIMIT 5'
        );

        return Response::html(View::render('admin/dashboard', [
            'contagens'            => $contagens,
            'pedidosRecentes'      => $pedidosRecentes,
            'agendamentosProximos' => $agendamentosProximos,
            'mensagensRecentes'    => $mensagensRecentes,
        ]));
    }
}
