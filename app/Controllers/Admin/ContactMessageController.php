<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * ContactMessageController (admin) — mensagens de contacto no painel.
 *
 * Espelha App\Http\Controllers\Admin\ContactMessageController do backend:
 * lista com filtro, detalhe, mudança de estado (new/replied/closed) e
 * apagamento.
 */
final class ContactMessageController
{
    /** Estados possíveis (código => rótulo PT). */
    public const ESTADOS = [
        'new'     => 'Nova',
        'replied' => 'Respondida',
        'closed'  => 'Fechada',
    ];

    // ------------------------------------------------------------------
    // GET /admin/mensagens — lista com filtro ?status=
    // ------------------------------------------------------------------
    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', '');
        if (!array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $onde       = '';
        $parametros = [];
        if ($status !== '') {
            $onde       = ' WHERE status = ?';
            $parametros = [$status];
        }

        $mensagens = Database::todos(
            'SELECT id, name, email, subject, message, status, created_at
             FROM contact_messages' . $onde . ' ORDER BY created_at DESC',
            $parametros
        );

        $contagens = ['total' => (int) Database::escalar('SELECT COUNT(*) FROM contact_messages')];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $contagens[$codigo] = (int) Database::escalar(
                'SELECT COUNT(*) FROM contact_messages WHERE status = ?',
                [$codigo]
            );
        }

        return Response::html(View::render('admin/mensagens/index', [
            'mensagens' => $mensagens,
            'status'    => $status,
            'estados'   => self::ESTADOS,
            'contagens' => $contagens,
        ]));
    }

    // ------------------------------------------------------------------
    // GET /admin/mensagens/{id} — mensagem integral
    // ------------------------------------------------------------------
    public function mostrar(Request $request): Response
    {
        $id       = (string) $request->parametro('id');
        $mensagem = $this->mensagem($id);

        if ($mensagem === null) {
            return $this->naoEncontrado();
        }

        return Response::html(View::render('admin/mensagens/mostrar', [
            'mensagem' => $mensagem,
            'estados'  => self::ESTADOS,
        ]));
    }

    // ------------------------------------------------------------------
    // POST /admin/mensagens/{id} — muda o estado
    // ------------------------------------------------------------------
    public function atualizar(Request $request): Response
    {
        $id       = (string) $request->parametro('id');
        $mensagem = $this->mensagem($id);

        if ($mensagem === null) {
            return $this->naoEncontrado();
        }

        $status   = (string) $request->input('status', '');
        $validado = Validator::fazer(
            ['status' => $status],
            ['status' => 'required|in:' . implode(',', array_keys(self::ESTADOS))],
            ['status' => 'estado']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['status' => $status]);

            return Response::redirect(rota('admin.mensagens') . '/' . rawurlencode($id));
        }

        Database::executar('UPDATE contact_messages SET status = ? WHERE id = ?', [$status, $id]);

        Logger::info('admin.mensagem.estado_alterado', ['id' => $id, 'status' => $status]);

        Session::flash('ok', 'Estado da mensagem atualizado.');

        return Response::redirect(rota('admin.mensagens') . '/' . rawurlencode($id));
    }

    // ------------------------------------------------------------------
    // POST /admin/mensagens/{id}/apagar — apaga a mensagem
    // ------------------------------------------------------------------
    public function apagar(Request $request): Response
    {
        $id       = (string) $request->parametro('id');
        $mensagem = $this->mensagem($id);

        if ($mensagem === null) {
            return $this->naoEncontrado();
        }

        Database::executar('DELETE FROM contact_messages WHERE id = ?', [$id]);

        Logger::info('admin.mensagem.apagada', ['id' => $id]);

        Session::flash('ok', 'Mensagem apagada.');

        return Response::redirect(rota('admin.mensagens'));
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * @return array<string, mixed>|null
     */
    private function mensagem(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        return Database::um('SELECT * FROM contact_messages WHERE id = ?', [$id]);
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }
}
