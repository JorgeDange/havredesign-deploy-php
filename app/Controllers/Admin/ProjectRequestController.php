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
 * ProjectRequestController (admin) — pedidos de orçamento no painel.
 *
 * Espelha App\Http\Controllers\Admin\ProjectRequestController do backend:
 * lista com filtro, detalhe + anexos, mudança de estado, download/preview
 * de ficheiros privados e apagamento (registos + ficheiros em disco).
 */
final class ProjectRequestController
{
    /** Estados possíveis (código => rótulo PT). */
    public const ESTADOS = [
        'NEW'         => 'Novo',
        'IN_REVIEW'   => 'Em análise',
        'APPROVED'    => 'Aprovado',
        'IN_PROGRESS' => 'Em curso',
        'COMPLETED'   => 'Concluído',
        'REJECTED'    => 'Recusado',
    ];

    // ------------------------------------------------------------------
    // GET /admin/pedidos — lista com filtro ?status=
    // ------------------------------------------------------------------
    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', '');
        if (!array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $onde     = '';
        $parametros = [];
        if ($status !== '') {
            $onde       = ' WHERE status = ?';
            $parametros = [$status];
        }

        $pedidos = Database::todos(
            'SELECT id, user_name, user_email, project_type, location, status, created_at
             FROM project_requests' . $onde . ' ORDER BY created_at DESC',
            $parametros
        );

        // Contagem de anexos por pedido (uma única query)
        $anexosPorPedido = [];
        foreach (Database::todos('SELECT request_id, COUNT(*) AS total FROM attachments GROUP BY request_id') as $linha) {
            $anexosPorPedido[(string) $linha['request_id']] = (int) $linha['total'];
        }

        $contagens = ['total' => (int) Database::escalar('SELECT COUNT(*) FROM project_requests')];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $contagens[$codigo] = (int) Database::escalar(
                'SELECT COUNT(*) FROM project_requests WHERE status = ?',
                [$codigo]
            );
        }

        return Response::html(View::render('admin/pedidos/index', [
            'pedidos'         => $pedidos,
            'status'          => $status,
            'estados'         => self::ESTADOS,
            'contagens'       => $contagens,
            'anexosPorPedido' => $anexosPorPedido,
        ]));
    }

    // ------------------------------------------------------------------
    // GET /admin/pedidos/{id} — detalhe + anexos
    // ------------------------------------------------------------------
    public function mostrar(Request $request): Response
    {
        $id     = (string) $request->parametro('id');
        $pedido = $this->pedido($id);

        if ($pedido === null) {
            return $this->naoEncontrado();
        }

        $servico = null;
        if (!empty($pedido['service_id'])) {
            $servico = Database::escalar('SELECT title FROM services WHERE id = ?', [$pedido['service_id']]);
        }

        $solucao = null;
        if (!empty($pedido['solution_id'])) {
            $solucao = Database::escalar('SELECT name FROM solutions WHERE id = ?', [$pedido['solution_id']]);
        }

        $conta = null;
        if (!empty($pedido['user_id'])) {
            $conta = Database::escalar('SELECT name FROM users WHERE id = ?', [$pedido['user_id']]);
        }

        $anejos = Database::todos(
            'SELECT * FROM attachments WHERE request_id = ? ORDER BY created_at, original_name',
            [$id]
        );

        return Response::html(View::render('admin/pedidos/mostrar', [
            'pedido'  => $pedido,
            'estados' => self::ESTADOS,
            'anejos'  => $anejos,
            'servico' => $servico,
            'solucao' => $solucao,
            'conta'   => $conta,
        ]));
    }

    // ------------------------------------------------------------------
    // POST /admin/pedidos/{id} — muda o estado
    // ------------------------------------------------------------------
    public function atualizar(Request $request): Response
    {
        $id     = (string) $request->parametro('id');
        $pedido = $this->pedido($id);

        if ($pedido === null) {
            return $this->naoEncontrado();
        }

        $status  = (string) $request->input('status', '');
        $validado = Validator::fazer(
            ['status' => $status],
            ['status' => 'required|in:' . implode(',', array_keys(self::ESTADOS))],
            ['status' => 'estado']
        );

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', ['status' => $status]);

            return Response::redirect(rota('admin.pedidos') . '/' . rawurlencode($id));
        }

        Database::executar('UPDATE project_requests SET status = ? WHERE id = ?', [$status, $id]);

        Logger::info('admin.pedido.estado_alterado', ['id' => $id, 'status' => $status]);

        Session::flash('ok', 'Estado do pedido atualizado.');

        return Response::redirect(rota('admin.pedidos') . '/' . rawurlencode($id));
    }

    // ------------------------------------------------------------------
    // GET /admin/pedidos/{id}/anexos/{anexo} — download (privado)
    // ------------------------------------------------------------------
    public function baixar(Request $request): Response
    {
        return $this->servirAnexo($request, 'attachment');
    }

    // ------------------------------------------------------------------
    // GET /admin/pedidos/{id}/anexos/{anexo}/ver — preview inline
    // ------------------------------------------------------------------
    public function ver(Request $request): Response
    {
        $id     = (string) $request->parametro('id');
        $anejo  = $this->anexo((string) $request->parametro('anexo'), $id);

        // Formatos não visíveis (zip, dwg, …) caem em download normal
        if ($anejo === null || !$this->visualizavel($anejo)) {
            return $this->servirAnexo($request, 'attachment');
        }

        return $this->servirAnexo($request, 'inline');
    }

    // ------------------------------------------------------------------
    // POST /admin/pedidos/{id}/apagar — apaga pedido + anexos
    // ------------------------------------------------------------------
    public function apagar(Request $request): Response
    {
        $id     = (string) $request->parametro('id');
        $pedido = $this->pedido($id);

        if ($pedido === null) {
            return $this->naoEncontrado();
        }

        $anejos = Database::todos('SELECT path FROM attachments WHERE request_id = ?', [$id]);

        try {
            Database::transacao(function () use ($id): void {
                Database::executar('DELETE FROM attachments WHERE request_id = ?', [$id]);
                Database::executar('DELETE FROM project_requests WHERE id = ?', [$id]);
            });
        } catch (\Throwable $e) {
            Logger::erro('admin.pedido.apagar_falhou', ['id' => $id, 'erro' => $e->getMessage()]);
            Session::flash('erro', 'Não foi possível apagar o pedido.');

            return Response::redirect(rota('admin.pedidos') . '/' . rawurlencode($id));
        }

        // Registos apagados — agora remove os ficheiros de disco (privado)
        foreach ($anejos as $linha) {
            $caminho = $this->ficheiroPrivado(['path' => (string) $linha['path']]);
            if ($caminho !== null) {
                @unlink($caminho);
            }
        }

        $this->removerDirectorioPedido($id);

        Logger::info('admin.pedido.apagado', ['id' => $id]);

        Session::flash('ok', 'Pedido apagado.');

        return Response::redirect(rota('admin.pedidos'));
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * @return array<string, mixed>|null
     */
    private function pedido(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        return Database::um('SELECT * FROM project_requests WHERE id = ?', [$id]);
    }

    /**
     * Anexo que pertence ao pedido (404 se não existir).
     *
     * @return array<string, mixed>|null
     */
    private function anexo(string $anejoId, string $pedidoId): ?array
    {
        if ($anejoId === '' || $pedidoId === '') {
            return null;
        }

        return Database::um(
            'SELECT * FROM attachments WHERE id = ? AND request_id = ?',
            [$anejoId, $pedidoId]
        );
    }

    /**
     * Resolve o caminho absoluto do anexo DENTRO de storage/app/private,
     * rejeitando qualquer tentativa de path traversal (realpath + prefixo).
     *
     * @param array<string, mixed> $anejo
     */
    private function ficheiroPrivado(array $anejo): ?string
    {
        $relativo = trim((string) ($anejo['path'] ?? ''));

        if ($relativo === '' || str_contains($relativo, "\0")) {
            return null;
        }

        $raiz = realpath(dirname(__DIR__, 3) . '/storage/app/private');
        if ($raiz === false) {
            return null;
        }

        $normalizado = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativo);
        $candidato   = $raiz . DIRECTORY_SEPARATOR . $normalizado;
        $real        = realpath($candidato);

        if ($real === false || !is_file($real)) {
            return null;
        }

        // O ficheiro tem de viver estritamente dentro da raiz privada
        if (!str_starts_with($real, $raiz . DIRECTORY_SEPARATOR)) {
            Logger::aviso('admin.anexo.path_traversal', ['path' => $relativo]);

            return null;
        }

        return $real;
    }

    /**
     * Lê e envia o ficheiro com o Content-Disposition pedido.
     */
    private function servirAnexo(Request $request, string $disposicao): Response
    {
        $id       = (string) $request->parametro('id');
        $anejoId  = (string) $request->parametro('anexo');
        $pedido   = $this->pedido($id);

        if ($pedido === null) {
            return $this->naoEncontrado();
        }

        $anejo = $this->anexo($anejoId, $id);
        if ($anejo === null) {
            return $this->naoEncontrado();
        }

        $caminho = $this->ficheiroPrivado($anejo);
        if ($caminho === null) {
            return $this->naoEncontrado();
        }

        $conteudo = @file_get_contents($caminho);
        if ($conteudo === false) {
            Logger::erro('admin.anexo.leitura_falhou', ['id' => $anejoId]);

            return $this->naoEncontrado();
        }

        $mime = trim((string) ($anejo['mime_type'] ?? ''));
        if ($mime === '') {
            $mime = 'application/octet-stream';
        }

        $nome      = $this->nomeSeguro((string) ($anejo['original_name'] ?? ''));
        $ascii     = preg_replace('/[^\x20-\x7E]/', '_', $nome) ?? 'anexo';
        $ascii     = str_replace(['"', '\\'], '_', $ascii);
        $dispo     = $disposicao === 'inline' ? 'inline' : 'attachment';
        $cabecalho = sprintf(
            '%s; filename="%s"; filename*=UTF-8\'\'%s',
            $dispo,
            $ascii,
            rawurlencode($nome)
        );

        return (new Response($conteudo, 200))
            ->comHeader('Content-Type', $mime)
            ->comHeader('Content-Disposition', $cabecalho)
            ->comHeader('Content-Length', (string) strlen($conteudo))
            ->comHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * O anexo pode ser mostrado em inline (imagem/PDF)?
     *
     * @param array<string, mixed> $anejo
     */
    private function visualizavel(array $anejo): bool
    {
        $mime = strtolower(trim((string) ($anejo['mime_type'] ?? '')));

        return str_starts_with($mime, 'image/') || $mime === 'application/pdf';
    }

    /** Nome de descarga sem caracteres que quebram o Content-Disposition. */
    private function nomeSeguro(string $nome): string
    {
        $nome = trim(str_replace(["\r", "\n", '%'], '-', $nome));

        return $nome !== '' ? $nome : 'anexo';
    }

    /** Remove o directório privado do pedido, se ficar vazio. */
    private function removerDirectorioPedido(string $id): void
    {
        if ($id === '' || str_contains($id, '..')) {
            return;
        }

        $directorio = dirname(__DIR__, 3) . '/storage/app/private/requests/' . $id;

        if (is_dir($directorio)) {
            $dentro = @scandir($directorio);
            if (is_array($dentro) && array_diff($dentro, ['.', '..']) === []) {
                @rmdir($directorio);
            }
        }
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }
}
