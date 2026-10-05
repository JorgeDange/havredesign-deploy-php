<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

/**
 * Utilizadores — lista de contas e alternância do papel USER/ADMIN.
 *
 * Segurança: NUNCA se mostra o hash da password, NUNCA se altera a password
 * e um admin não se pode rebaixar a si próprio (fica preso fora do painel).
 */
final class UserController
{
    /**
     * GET /admin/utilizadores — contas registadas (sem password).
     */
    public function index(Request $request): Response
    {
        $utilizadores = Database::todos(
            'SELECT id, name, email, phone, role, created_at, updated_at
             FROM users
             ORDER BY name ASC'
        );

        return Response::html(View::render('admin/utilizadores/index', [
            'utilizadores' => $utilizadores,
        ]));
    }

    /**
     * POST /admin/utilizadores/{id}/papel — alterna USER ↔ ADMIN.
     */
    public function papel(Request $request): Response
    {
        $id          = $request->parametro('id');
        $destino     = rota('admin.utilizadores');
        $utilizador  = $id === '' ? null : Database::um(
            'SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1',
            [$id]
        );

        if ($utilizador === null) {
            return Response::html(View::render('errors/404'), 404);
        }

        // Regra inamovível: não é permitido alterar o próprio papel
        if ($id === Auth::id()) {
            Session::flash('erro', 'Não pode alterar o próprio papel.');

            return Response::redirect($destino);
        }

        $novoPapel = ((string) $utilizador['role']) === 'ADMIN' ? 'USER' : 'ADMIN';

        Database::executar(
            'UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?',
            [$novoPapel, $id]
        );

        Session::flash('ok', 'Papel actualizado para ' . $novoPapel . '.');

        return Response::redirect($destino);
    }
}
