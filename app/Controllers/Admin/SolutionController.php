<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Soluções comerciais (tabela `solutions`) — CRUD do painel de administração.
 */
final class SolutionController
{
    // ------------------------------------------------------------------
    // Listagem e formulários
    // ------------------------------------------------------------------

    public function index(Request $request): Response
    {
        $solucoes = Database::todos(
            'SELECT * FROM solutions ORDER BY sort_order, name'
        );

        return Response::html(View::render('admin/solucoes/index', [
            'solucoes' => $solucoes,
        ]));
    }

    public function criar(Request $request): Response
    {
        return Response::html(View::render('admin/solucoes/criar'));
    }

    public function editar(Request $request): Response
    {
        $solucao = Database::um('SELECT * FROM solutions WHERE id = ?', [$request->parametro('id')]);

        if ($solucao === null) {
            return $this->naoEncontrado();
        }

        return Response::html(View::render('admin/solucoes/editar', [
            'solucao' => $solucao,
        ]));
    }

    // ------------------------------------------------------------------
    // Criação e atualização
    // ------------------------------------------------------------------

    public function guardar(Request $request): Response
    {
        $post  = $request->todos();
        $post  = $this->preencherSlug($post);
        $erros = $this->validar($post, null);

        if ($erros !== []) {
            return $this->comErros($erros, $post, $this->prefixo() . '/solucoes/nova');
        }

        $agora = date('Y-m-d H:i:s');
        $id    = novo_uuid();

        Database::executar(
            'INSERT INTO solutions
                (id, code, name, slug, description, cta_label, sort_order, active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                (string) ($post['code'] ?? ''),
                (string) ($post['name'] ?? ''),
                (string) ($post['slug'] ?? ''),
                (string) ($post['description'] ?? ''),
                $this->nulo((string) ($post['cta_label'] ?? '')),
                $this->inteiro($post['sort_order'] ?? '0'),
                $this->booleano($post['active'] ?? null),
                $agora,
                $agora,
            ]
        );

        Session::flash('ok', 'Solução criada com sucesso.');

        return Response::redirect($this->prefixo() . '/solucoes');
    }

    public function atualizar(Request $request): Response
    {
        $id       = $request->parametro('id');
        $solucao  = Database::um('SELECT id FROM solutions WHERE id = ?', [$id]);

        if ($solucao === null) {
            return $this->naoEncontrado();
        }

        $post  = $request->todos();
        $erros = $this->validar($post, $id);

        if ($erros !== []) {
            return $this->comErros($erros, $post, $this->prefixo() . '/solucoes/' . rawurlencode($id) . '/editar');
        }

        Database::executar(
            'UPDATE solutions
                SET code = ?, name = ?, slug = ?, description = ?, cta_label = ?,
                    sort_order = ?, active = ?, updated_at = ?
              WHERE id = ?',
            [
                (string) ($post['code'] ?? ''),
                (string) ($post['name'] ?? ''),
                (string) ($post['slug'] ?? ''),
                (string) ($post['description'] ?? ''),
                $this->nulo((string) ($post['cta_label'] ?? '')),
                $this->inteiro($post['sort_order'] ?? '0'),
                $this->booleano($post['active'] ?? null),
                date('Y-m-d H:i:s'),
                $id,
            ]
        );

        Session::flash('ok', 'Solução atualizada com sucesso.');

        return Response::redirect($this->prefixo() . '/solucoes');
    }

    public function apagar(Request $request): Response
    {
        $id       = $request->parametro('id');
        $solucao  = Database::um('SELECT id FROM solutions WHERE id = ?', [$id]);

        if ($solucao === null) {
            return $this->naoEncontrado();
        }

        Database::executar('DELETE FROM solutions WHERE id = ?', [$id]);

        Session::flash('ok', 'Solução apagada com sucesso.');

        return Response::redirect($this->prefixo() . '/solucoes');
    }

    // ------------------------------------------------------------------
    // Validação
    // ------------------------------------------------------------------

    /**
     * Slug em branco na criação é derivado do nome, antes da validação.
     *
     * @param  array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function preencherSlug(array $post): array
    {
        if (trim((string) ($post['slug'] ?? '')) !== '') {
            return $post;
        }

        $post['slug'] = $this->slugificar((string) ($post['name'] ?? ''));

        return $post;
    }

    /**
     * @param  array<string, mixed> $post
     * @return array<string, string> erros por campo (vazio = válido)
     */
    private function validar(array $post, ?string $idExcluir): array
    {
        $exclusao = $idExcluir !== null ? ',' . $idExcluir : '';

        $regras = [
            'code'        => 'required|string|max:20|unique:solutions,code' . $exclusao,
            'name'        => 'required|string|max:120',
            'slug'        => 'required|string|max:120|unique:solutions,slug' . $exclusao,
            'description' => 'required|string',
            'cta_label'   => 'nullable|string|max:60',
        ];

        $rotulos = [
            'code'        => 'código',
            'name'        => 'nome',
            'slug'        => 'slug',
            'description' => 'descrição',
            'cta_label'   => 'texto do botão',
        ];

        $validado = Validator::fazer($post, $regras, $rotulos);
        $erros    = $validado->erros();

        // A ordem é checada à mão: a regra "integer" do Validator usa
        // !filter_var(), que trata o valor 0 (por omissão do formulário) como falso.
        $ordem = trim((string) ($post['sort_order'] ?? ''));
        if ($ordem !== '' && !preg_match('/^-?\d+$/', $ordem)) {
            $erros['sort_order'] = 'O campo ordem deve ser um número inteiro.';
        } elseif ($ordem !== '' && (int) $ordem < 0) {
            $erros['sort_order'] = 'O campo ordem não pode ser negativo.';
        }

        return $erros;
    }

    // ------------------------------------------------------------------
    // Utilitários
    // ------------------------------------------------------------------

    private function prefixo(): string
    {
        return '/' . Config::obter('ADMIN_PATH', 'admin');
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }

    /**
     * @param array<string, string> $erros
     * @param array<string, mixed>  $post
     */
    private function comErros(array $erros, array $post, string $destino): Response
    {
        Session::colocar('_erros_validacao', $erros);
        Session::colocar('_old_input', array_filter($post, 'is_scalar'));

        return Response::redirect($destino);
    }

    private function slugificar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $texto = strtr($texto, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ]);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?? '';

        return trim($texto, '-');
    }

    private function nulo(string $valor): ?string
    {
        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }

    private function booleano(mixed $valor): int
    {
        return in_array((string) $valor, ['1', 'on', 'true', 'yes'], true) ? 1 : 0;
    }

    private function inteiro(mixed $valor): int
    {
        return is_numeric($valor) ? (int) $valor : 0;
    }
}
