<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Testemunhos — aprovação (hidden/published), ordenação e remoção.
 * Espelho do TestimonialController do backend Laravel (tabela `testimonials`).
 */
final class TestimonialController
{
    /**
     * GET /admin/testemunhos — lista ordenada por `sort_order`.
     */
    public function index(Request $request): Response
    {
        $testemunhos = Database::todos(
            'SELECT id, name, role, content, status, sort_order, created_at, updated_at
             FROM testimonials
             ORDER BY sort_order ASC, created_at ASC'
        );

        return Response::html(View::render('admin/testemunhos/index', [
            'testemunhos' => $testemunhos,
        ]));
    }

    /**
     * GET /admin/testemunhos/{id}/editar — formulário de edição.
     */
    public function editar(Request $request): Response
    {
        $testemunho = $this->buscar($request->parametro('id'));
        if ($testemunho === null) {
            return $this->naoEncontrado();
        }

        return Response::html(View::render('admin/testemunhos/editar', [
            'testemunho' => $testemunho,
        ]));
    }

    /**
     * POST /admin/testemunhos/{id} — actualiza nome, cargo, texto, estado e ordem.
     */
    public function atualizar(Request $request): Response
    {
        $id          = $request->parametro('id');
        $testemunho  = $this->buscar($id);
        if ($testemunho === null) {
            return $this->naoEncontrado();
        }

        $nome    = $request->input('name');
        $cargo   = (string) $request->bruto('role', '');
        $texto   = (string) $request->bruto('content', '');
        $ordem   = (string) $request->bruto('sort_order', '0');
        $estado  = (string) $request->bruto('status', (string) $testemunho['status']);

        $validado = Validator::fazer(
            [
                'name'       => $nome,
                'role'       => $cargo,
                'content'    => $texto,
                'sort_order' => $ordem,
                'status'     => $estado,
            ],
            [
                'name'       => 'required|string|max:150',
                'role'       => 'nullable|string|max:150',
                'content'    => 'required|string|max:5000',
                'sort_order' => 'nullable|numeric',
                'status'     => 'nullable|in:hidden,published',
            ],
            [
                'name'       => 'nome',
                'role'       => 'cargo',
                'content'    => 'testemunho',
                'sort_order' => 'ordem',
                'status'     => 'estado',
            ]
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'name'       => $nome,
                'role'       => $cargo,
                'content'    => $texto,
                'sort_order' => $ordem,
                'status'     => $estado,
            ]);

            return Response::redirect($this->rotaEditar($id));
        }

        $cargoFinal = trim($cargo) !== '' ? trim($cargo) : null;
        $estadoFinal = in_array($estado, ['hidden', 'published'], true)
            ? $estado
            : (string) $testemunho['status'];
        $ordemFinal = max(0, (int) $ordem);

        Database::executar(
            'UPDATE testimonials
             SET name = ?, role = ?, content = ?, status = ?, sort_order = ?, updated_at = NOW()
             WHERE id = ?',
            [$nome, $cargoFinal, $texto, $estadoFinal, $ordemFinal, $id]
        );

        Session::flash('ok', 'Testemunho actualizado.');

        return Response::redirect(rota('admin.testemunhos'));
    }

    /**
     * POST /admin/testemunhos/{id}/alternar — alterna hidden ↔ published.
     */
    public function alternar(Request $request): Response
    {
        $id         = $request->parametro('id');
        $testemunho = $this->buscar($id);
        if ($testemunho === null) {
            return $this->naoEncontrado();
        }

        $publicado = ((string) $testemunho['status']) !== 'published';

        Database::executar(
            'UPDATE testimonials SET status = ?, updated_at = NOW() WHERE id = ?',
            [$publicado ? 'published' : 'hidden', $id]
        );

        Session::flash('ok', $publicado ? 'Testemunho publicado.' : 'Testemunho ocultado.');

        return Response::redirect(rota('admin.testemunhos'));
    }

    /**
     * POST /admin/testemunhos/{id}/apagar — remove o registo.
     */
    public function apagar(Request $request): Response
    {
        $id         = $request->parametro('id');
        $testemunho = $this->buscar($id);
        if ($testemunho === null) {
            return $this->naoEncontrado();
        }

        Database::executar('DELETE FROM testimonials WHERE id = ?', [$id]);

        Session::flash('ok', 'Testemunho apagado.');

        return Response::redirect(rota('admin.testemunhos'));
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * @return array<string, mixed>|null
     */
    private function buscar(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        return Database::um(
            'SELECT id, name, role, content, status, sort_order, created_at, updated_at
             FROM testimonials WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }

    private function rotaEditar(string $id): string
    {
        return rota('admin.testemunhos') . '/' . rawurlencode($id) . '/editar';
    }
}
