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
 * ServiceController — CRUD dos serviços (tabelas services + service_includes).
 *
 * Espelha Admin\ServiceController do backend Laravel: validação server-side,
 * carregamento de imagem para public/uploads/servicos/ e sincronização da
 * textarea «inclui» (um item por linha) com a tabela service_includes.
 */
final class ServiceController
{
    /** Extensões de imagem admitidas. */
    private const EXTENSOES_IMAGEM = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** Tamanho máximo da imagem em bytes (5 MB). */
    private const TAMANHO_MAX_IMAGEM = 5 * 1024 * 1024;

    /** Directório público onde ficam as imagens dos serviços. */
    private const DIRECTORIO_IMAGENS = '/uploads/servicos';

    // ------------------------------------------------------------------
    // Leitura
    // ------------------------------------------------------------------

    public function index(Request $r): Response
    {
        $servicos = Database::todos(
            'SELECT * FROM services ORDER BY sort_order ASC, title ASC'
        );

        return Response::html(View::render('admin/servicos/index', [
            'servicos' => $servicos,
        ]));
    }

    public function criar(Request $r): Response
    {
        return Response::html(View::render('admin/servicos/criar'));
    }

    public function editar(Request $r): Response
    {
        $servico = $this->servicoPor($r);
        if ($servico === null) {
            return Response::html(View::render('errors/404'), 404);
        }

        return Response::html(View::render('admin/servicos/editar', [
            'servico' => $servico,
            'inclui'  => $this->itensDe($servico['id']),
        ]));
    }

    // ------------------------------------------------------------------
    // Escrita
    // ------------------------------------------------------------------

    public function guardar(Request $r): Response
    {
        $titulo     = $r->input('title');
        $slug       = $r->input('slug');
        $grupo      = $r->input('group');
        $descricao  = $r->input('description');
        $icone      = $r->input('icon');
        $ordem      = $r->input('sort_order');
        $ativo      = $r->input('active', '0');

        $validado = Validator::fazer(
            [
                'title'      => $titulo,
                'slug'       => $slug,
                'group'      => $grupo,
                'description' => $descricao,
                'icon'       => $icone,
                'sort_order' => $ordem,
                'active'     => $ativo,
            ],
            [
                'title'       => 'required|string|max:200',
                'slug'        => 'required|string|max:200|unique:services,slug',
                'group'       => 'required|in:principal,complementar',
                'description' => 'required|string|max:20000',
                'icon'        => 'nullable|string|max:50',
                'sort_order'  => 'nullable|numeric',
                'active'      => 'nullable|in:0,1',
            ],
            [
                'title'       => 'título',
                'slug'        => 'slug',
                'group'       => 'grupo',
                'description' => 'descrição',
                'icon'        => 'ícone',
                'sort_order'  => 'ordem',
                'active'      => 'ativo',
            ]
        );

        $imagem = $r->ficheiro('image');
        if ($imagem !== null) {
            $erroImagem = self::validarImagem($imagem);
            if ($erroImagem !== null) {
                $validado->erro('image', $erroImagem);
            }
        }

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'title'       => $titulo,
                'slug'        => $slug,
                'group'       => $grupo,
                'description' => $descricao,
                'icon'        => $icone,
                'sort_order'  => $ordem,
                'active'      => $ativo,
            ]);

            return Response::redirect(rota('admin.servicos') . '/novo');
        }

        $caminhoImagem = $imagem !== null ? self::guardarImagem($imagem) : null;

        Database::executar(
            'INSERT INTO services (id, `group`, title, slug, description, icon, image_url, active, sort_order, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                novo_uuid(),
                $grupo,
                $titulo,
                $slug,
                $descricao,
                $icone,
                $caminhoImagem,
                $ativo === '1' ? 1 : 0,
                max(0, (int) $ordem),
            ]
        );

        Session::flash('ok', 'Serviço criado.');

        return Response::redirect(rota('admin.servicos'));
    }

    public function atualizar(Request $r): Response
    {
        $id       = $r->parametro('id');
        $servico  = $this->servicoPor($r);
        if ($servico === null) {
            return Response::html(View::render('errors/404'), 404);
        }

        $titulo     = $r->input('title');
        $slug       = $r->input('slug');
        $grupo      = $r->input('group');
        $descricao  = $r->input('description');
        $icone      = $r->input('icon');
        $ordem      = $r->input('sort_order');
        $ativo      = $r->input('active', '0');
        $inclui     = $r->bruto('inclui');

        $validado = Validator::fazer(
            [
                'title'       => $titulo,
                'slug'        => $slug,
                'group'       => $grupo,
                'description' => $descricao,
                'icon'        => $icone,
                'sort_order'  => $ordem,
                'active'      => $ativo,
                'inclui'      => $inclui,
            ],
            [
                'title'       => 'required|string|max:200',
                'slug'        => 'required|string|max:200|unique:services,slug,' . $id,
                'group'       => 'required|in:principal,complementar',
                'description' => 'required|string|max:20000',
                'icon'        => 'nullable|string|max:50',
                'sort_order'  => 'nullable|numeric',
                'active'      => 'nullable|in:0,1',
                'inclui'      => 'nullable|string|max:40000',
            ],
            [
                'title'       => 'título',
                'slug'        => 'slug',
                'group'       => 'grupo',
                'description' => 'descrição',
                'icon'        => 'ícone',
                'sort_order'  => 'ordem',
                'active'      => 'ativo',
                'inclui'      => 'inclui',
            ]
        );

        $imagem = $r->ficheiro('image');
        if ($imagem !== null) {
            $erroImagem = self::validarImagem($imagem);
            if ($erroImagem !== null) {
                $validado->erro('image', $erroImagem);
            }
        }

        // Cada linha da textarea «inclui» é uma linha da tabela (máx. 500)
        $itensInclui = is_string($inclui) ? self::parsearInclui($inclui) : null;
        if ($itensInclui !== null) {
            foreach ($itensInclui as $textoItem) {
                if (mb_strlen($textoItem) > 500) {
                    $validado->erro('inclui', 'Cada item de «O que inclui» não pode exceder 500 caracteres.');
                    break;
                }
            }
        }

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'title'       => $titulo,
                'slug'        => $slug,
                'group'       => $grupo,
                'description' => $descricao,
                'icon'        => $icone,
                'sort_order'  => $ordem,
                'active'      => $ativo,
                'inclui'      => is_string($inclui) ? $inclui : '',
            ]);

            return Response::redirect(rota('admin.servicos') . '/' . $id . '/editar');
        }

        $imagemAntiga = (string) ($servico['image_url'] ?? '');
        $caminhoNovo  = $imagem !== null ? self::guardarImagem($imagem) : null;

        Database::executar(
            'UPDATE services
                SET `group` = ?, title = ?, slug = ?, description = ?, icon = ?,
                    image_url = ?, active = ?, sort_order = ?, updated_at = NOW()
              WHERE id = ?',
            [
                $grupo,
                $titulo,
                $slug,
                $descricao,
                $icone,
                $caminhoNovo ?? ($imagemAntiga !== '' ? $imagemAntiga : null),
                $ativo === '1' ? 1 : 0,
                max(0, (int) $ordem),
                $id,
            ]
        );

        if ($caminhoNovo !== null && $imagemAntiga !== '') {
            self::apagarImagem($imagemAntiga);
        }

        if ($itensInclui !== null) {
            $this->sincronizarInclui($id, $itensInclui);
        }

        Session::flash('ok', 'Serviço atualizado.');

        return Response::redirect(rota('admin.servicos') . '/' . $id . '/editar');
    }

    public function alternar(Request $r): Response
    {
        $servico = $this->servicoPor($r);
        if ($servico === null) {
            return Response::html(View::render('errors/404'), 404);
        }

        $ativado = empty($servico['active']) ? 1 : 0;

        Database::executar(
            'UPDATE services SET active = ?, updated_at = NOW() WHERE id = ?',
            [$ativado, $servico['id']]
        );

        Session::flash('ok', $ativado ? 'Serviço ativado.' : 'Serviço desativado.');

        return Response::redirect(rota('admin.servicos'));
    }

    public function apagar(Request $r): Response
    {
        $servico = $this->servicoPor($r);
        if ($servico === null) {
            return Response::html(View::render('errors/404'), 404);
        }

        $imagem = (string) ($servico['image_url'] ?? '');

        Database::transacao(function () use ($servico): void {
            Database::executar('DELETE FROM service_includes WHERE service_id = ?', [$servico['id']]);
            Database::executar('DELETE FROM services WHERE id = ?', [$servico['id']]);
        });

        self::apagarImagem($imagem);

        Session::flash('ok', 'Serviço apagado.');

        return Response::redirect(rota('admin.servicos'));
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * Serviço do parâmetro de rota {id} ou null (responder 404).
     *
     * @return array<string, mixed>|null
     */
    private function servicoPor(Request $r): ?array
    {
        $id = $r->parametro('id');
        if ($id === '') {
            return null;
        }

        return Database::um('SELECT * FROM services WHERE id = ?', [$id]);
    }

    /**
     * Itens «inclui» de um serviço, um por linha (para a textarea de edição).
     */
    private function itensDe(string $servicoId): string
    {
        $linhas = Database::todos(
            'SELECT item FROM service_includes WHERE service_id = ? ORDER BY sort_order ASC, id ASC',
            [$servicoId]
        );

        $itens = [];
        foreach ($linhas as $linha) {
            $itens[] = (string) $linha['item'];
        }

        return implode("\n", $itens);
    }

    /**
     * Textarea «inclui» → lista de itens (linhas vazias e duplicados fora).
     *
     * @return array<int, string>
     */
    private static function parsearInclui(string $texto): array
    {
        $itens = [];
        $partes = preg_split('/\r\n|\r|\n/', $texto) ?: [];

        foreach ($partes as $parte) {
            $linha = trim($parte);
            if ($linha !== '' && !in_array($linha, $itens, true)) {
                $itens[] = $linha;
            }
        }

        return $itens;
    }

    /**
     * Sincroniza a textarea «inclui» com service_includes: apaga os que saíram,
     * insere os novos e renumera sort_order pela ordem de apresentação.
     *
     * @param array<int, string> $itens
     */
    private function sincronizarInclui(string $servicoId, array $itens): void
    {
        Database::transacao(function () use ($servicoId, $itens): void {
            $existentes = Database::todos(
                'SELECT id, item FROM service_includes WHERE service_id = ?',
                [$servicoId]
            );

            $porItem = [];
            foreach ($existentes as $existente) {
                $porItem[(string) $existente['item']] = (int) $existente['id'];
            }

            foreach ($porItem as $textoItem => $linhaId) {
                if (!in_array($textoItem, $itens, true)) {
                    Database::executar('DELETE FROM service_includes WHERE id = ?', [$linhaId]);
                }
            }

            $ordem = 1;
            foreach ($itens as $textoItem) {
                if (isset($porItem[$textoItem])) {
                    Database::executar(
                        'UPDATE service_includes SET sort_order = ? WHERE id = ?',
                        [$ordem, $porItem[$textoItem]]
                    );
                } else {
                    Database::executar(
                        'INSERT INTO service_includes (service_id, item, sort_order) VALUES (?, ?, ?)',
                        [$servicoId, $textoItem, $ordem]
                    );
                }

                $ordem++;
            }
        });
    }

    /**
     * Valida o ficheiro carregado (formato + tamanho + conteúdo real de imagem).
     *
     * @param array<string, mixed> $ficheiro
     */
    private static function validarImagem(array $ficheiro): ?string
    {
        if ((int) ($ficheiro['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return 'Falha no carregamento da imagem.';
        }

        if ((int) ($ficheiro['size'] ?? 0) > self::TAMANHO_MAX_IMAGEM) {
            return 'A imagem não pode exceder 5 MB.';
        }

        $extensao = strtolower(pathinfo((string) $ficheiro['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, self::EXTENSOES_IMAGEM, true)) {
            return 'Formato de imagem não permitido (permitidos: jpg, jpeg, png, webp, gif).';
        }

        $info = @getimagesize((string) $ficheiro['tmp_name']);
        if ($info === false) {
            return 'O ficheiro enviado não é uma imagem válida.';
        }

        return null;
    }

    /**
     * Grava a imagem em public/uploads/servicos/ com nome aleatório e devolve
     * o caminho público (/uploads/servicos/…). Null se falhar o guardamento.
     *
     * @param array<string, mixed> $ficheiro
     */
    private static function guardarImagem(array $ficheiro): ?string
    {
        $extensao  = strtolower(pathinfo((string) $ficheiro['name'], PATHINFO_EXTENSION));
        $nome      = bin2hex(random_bytes(10)) . '.' . $extensao;
        $directorio = dirname(__DIR__, 3) . '/public' . self::DIRECTORIO_IMAGENS;

        if (!is_dir($directorio) && !@mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            Logger::erro('services.imagem_mkdir_falhou', ['directorio' => $directorio]);

            return null;
        }

        $destino = $directorio . '/' . $nome;

        if (!@move_uploaded_file((string) $ficheiro['tmp_name'], $destino)) {
            // Fallback para testes fora do SAPI web
            if (!@rename((string) $ficheiro['tmp_name'], $destino)) {
                Logger::erro('services.imagem_move_falhou', ['nome' => $nome]);

                return null;
            }
        }

        @chmod($destino, 0644);

        return self::DIRECTORIO_IMAGENS . '/' . $nome;
    }

    /**
     * Apaga um ficheiro de imagem — só se estiver no nosso directório.
     */
    private static function apagarImagem(string $caminho): void
    {
        if ($caminho === '' || !str_starts_with($caminho, self::DIRECTORIO_IMAGENS . '/')) {
            return;
        }

        $ficheiro = dirname(__DIR__, 3) . '/public' . $caminho;
        if (is_file($ficheiro)) {
            @unlink($ficheiro);
        }
    }
}
