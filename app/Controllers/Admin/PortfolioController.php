<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

/**
 * Portefólio — CRUD do painel de administração.
 *
 * A capa e as imagens da galeria são guardadas em public/storage/portfolio/
 * e o caminho gravado na BD segue o formato das linhas já existentes:
 * storage/portfolio/NOME.ext (as páginas públicas usam asset($image_url)).
 */
final class PortfolioController
{
    /** Categorias do enum `portfolio_items.category`. */
    private const CATEGORIAS = ['Residencial', 'Comercial', 'Corporativo', 'Outro'];

    /** Estados do enum `portfolio_items.status`. */
    private const ESTADOS = ['draft', 'published'];

    /** Segmentos do enum `portfolio_items.segment` (acentuação igual à BD). */
    private const SEGMENTOS = [
        'Investimento imobiliário residencial',
        'Habitação própria',
        'Comércio e serviços',
        'Outro',
    ];

    /** Extensão permitida => MIME real esperado (nunca aceitar .php). */
    private const EXTENSOES = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
    ];

    /** Tamanho máximo de cada imagem (5 MB). */
    private const TAMANHO_MAXIMO = 5 * 1024 * 1024;

    /** Máximo de imagens por envio à galeria. */
    private const MAXIMO_GALERIA = 10;

    // ------------------------------------------------------------------
    // Listagem e formulários
    // ------------------------------------------------------------------

    public function index(Request $request): Response
    {
        $projetos = Database::todos(
            'SELECT * FROM portfolio_items ORDER BY sort_order, title'
        );

        return Response::html(View::render('admin/portfolio/index', [
            'projetos' => $projetos,
        ]));
    }

    public function criar(Request $request): Response
    {
        return Response::html(View::render('admin/portfolio/criar'));
    }

    public function editar(Request $request): Response
    {
        $id       = $request->parametro('id');
        $projeto  = Database::um('SELECT * FROM portfolio_items WHERE id = ?', [$id]);

        if ($projeto === null) {
            return $this->naoEncontrado();
        }

        $galeria = Database::todos(
            'SELECT * FROM portfolio_gallery WHERE portfolio_id = ? ORDER BY sort_order, id',
            [$id]
        );

        return Response::html(View::render('admin/portfolio/editar', [
            'projeto' => $projeto,
            'galeria' => $galeria,
        ]));
    }

    // ------------------------------------------------------------------
    // Criação e atualização
    // ------------------------------------------------------------------

    public function guardar(Request $request): Response
    {
        $post   = $request->todos();
        $erros  = $this->validar($post, null);
        $imagem = $request->ficheiro('image');
        $tmp    = null;

        if ($imagem !== null) {
            $tmp = $this->validarImagem($imagem, $erros, 'image', 'imagem de capa');
        }

        if ($erros !== []) {
            return $this->comErros($erros, $post, $this->prefixo() . '/portfolio/novo');
        }

        $id   = novo_uuid();
        $slug = $this->resolverSlug((string) ($post['slug'] ?? ''), (string) ($post['title'] ?? ''), null);
        $caminho = $tmp !== null ? $this->moverImagem($tmp, (string) ($imagem['name'] ?? '')) : null;

        if ($tmp !== null && $caminho === null) {
            return $this->comErros(
                ['image' => 'Não foi possível guardar a imagem de capa. Tente novamente.'],
                $post,
                $this->prefixo() . '/portfolio/novo'
            );
        }

        Database::executar(
            'INSERT INTO portfolio_items
                (id, title, slug, category, status, segment, description, area, year, location, image_url, featured, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                (string) ($post['title'] ?? ''),
                $slug,
                (string) ($post['category'] ?? ''),
                (string) ($post['status'] ?? 'published'),
                $this->nulo((string) ($post['segment'] ?? '')),
                (string) ($post['description'] ?? ''),
                $this->nulo((string) ($post['area'] ?? '')),
                $this->nulo((string) ($post['year'] ?? '')),
                $this->nulo((string) ($post['location'] ?? '')),
                $caminho,
                $this->booleano($post['featured'] ?? null),
                $this->inteiro($post['sort_order'] ?? '0'),
            ]
        );

        Session::flash('ok', 'Projeto criado com sucesso.');

        return Response::redirect($this->prefixo() . '/portfolio/' . rawurlencode($id) . '/editar');
    }

    public function atualizar(Request $request): Response
    {
        $id      = $request->parametro('id');
        $projeto = Database::um('SELECT * FROM portfolio_items WHERE id = ?', [$id]);

        if ($projeto === null) {
            return $this->naoEncontrado();
        }

        $destino = $this->prefixo() . '/portfolio/' . rawurlencode($id) . '/editar';
        $post    = $request->todos();
        $erros   = $this->validar($post, $id);
        $imagem  = $request->ficheiro('image');
        $tmp     = null;

        if ($imagem !== null) {
            $tmp = $this->validarImagem($imagem, $erros, 'image', 'imagem de capa');
        }

        if ($erros !== []) {
            return $this->comErros($erros, $post, $destino);
        }

        // Slug em branco mantém o URL actual (preserva ligações existentes).
        $slugSubmetido = trim((string) ($post['slug'] ?? ''));
        $slug = $slugSubmetido === ''
            ? (string) $projeto['slug']
            : $this->resolverSlug($slugSubmetido, (string) ($post['title'] ?? ''), $id);

        $caminho = (string) ($projeto['image_url'] ?? '');

        if ($tmp !== null) {
            $novo = $this->moverImagem($tmp, (string) ($imagem['name'] ?? ''));

            if ($novo === null) {
                return $this->comErros(
                    ['image' => 'Não foi possível guardar a imagem de capa. Tente novamente.'],
                    $post,
                    $destino
                );
            }

            // Substituição: apagar o ficheiro antigo depois de o novo estar no disco.
            $this->apagarFicheiro($caminho);
            $caminho = $novo;
        }

        Database::executar(
            'UPDATE portfolio_items
                SET title = ?, slug = ?, category = ?, status = ?, segment = ?, description = ?,
                    area = ?, year = ?, location = ?, image_url = ?, featured = ?, sort_order = ?
              WHERE id = ?',
            [
                (string) ($post['title'] ?? ''),
                $slug,
                (string) ($post['category'] ?? ''),
                (string) ($post['status'] ?? 'published'),
                $this->nulo((string) ($post['segment'] ?? '')),
                (string) ($post['description'] ?? ''),
                $this->nulo((string) ($post['area'] ?? '')),
                $this->nulo((string) ($post['year'] ?? '')),
                $this->nulo((string) ($post['location'] ?? '')),
                $this->nulo($caminho),
                $this->booleano($post['featured'] ?? null),
                $this->inteiro($post['sort_order'] ?? '0'),
                $id,
            ]
        );

        Session::flash('ok', 'Projeto atualizado com sucesso.');

        return Response::redirect($destino);
    }

    // ------------------------------------------------------------------
    // Apagar projeto
    // ------------------------------------------------------------------

    public function apagar(Request $request): Response
    {
        $id      = $request->parametro('id');
        $projeto = Database::um('SELECT id, image_url FROM portfolio_items WHERE id = ?', [$id]);

        if ($projeto === null) {
            return $this->naoEncontrado();
        }

        $galeria = Database::todos(
            'SELECT id, image_url FROM portfolio_gallery WHERE portfolio_id = ?',
            [$id]
        );

        // Primeiro os ficheiros (senão ficam órfãos no disco), depois as linhas.
        foreach ($galeria as $imagem) {
            $this->apagarFicheiro((string) ($imagem['image_url'] ?? ''));
        }
        Database::executar('DELETE FROM portfolio_gallery WHERE portfolio_id = ?', [$id]);

        $this->apagarFicheiro((string) ($projeto['image_url'] ?? ''));
        Database::executar('DELETE FROM portfolio_items WHERE id = ?', [$id]);

        Session::flash('ok', 'Projeto removido do portefólio.');

        return Response::redirect($this->prefixo() . '/portfolio');
    }

    // ------------------------------------------------------------------
    // Galeria
    // ------------------------------------------------------------------

    /**
     * Upload múltiplo de imagens da galeria (página de edição).
     */
    public function galeria(Request $request): Response
    {
        $id       = $request->parametro('id');
        $projeto  = Database::um('SELECT id FROM portfolio_items WHERE id = ?', [$id]);

        if ($projeto === null) {
            return $this->naoEncontrado();
        }

        $destino  = $this->prefixo() . '/portfolio/' . rawurlencode($id) . '/editar#galeria';
        $ficheiros = $request->ficheiros('imagens');
        $erros     = [];
        $aprovados = [];

        if ($ficheiros === []) {
            $erros['imagens'] = 'Selecione pelo menos uma imagem para enviar.';
        } elseif (count($ficheiros) > self::MAXIMO_GALERIA) {
            $erros['imagens'] = 'Até ' . self::MAXIMO_GALERIA . ' imagens por envio.';
        } else {
            foreach ($ficheiros as $indice => $ficheiro) {
                $tmp = $this->validarImagem($ficheiro, $erros, 'imagens', 'imagem ' . ($indice + 1));
                if ($tmp !== null) {
                    $aprovados[] = ['tmp' => $tmp, 'nome' => (string) ($ficheiro['name'] ?? '')];
                }
            }
        }

        if ($erros !== []) {
            return $this->comErros($erros, [], $destino);
        }

        $caminhos = [];
        foreach ($aprovados as $aprovado) {
            $caminho = $this->moverImagem($aprovado['tmp'], $aprovado['nome']);
            if ($caminho === null) {
                // Falhou a meio: desfazer o que já foi movido para não deixar órfãos.
                foreach ($caminhos as $jaMovido) {
                    $this->apagarFicheiro($jaMovido);
                }

                return $this->comErros(
                    ['imagens' => 'Não foi possível guardar uma das imagens. Tente novamente.'],
                    [],
                    $destino
                );
            }
            $caminhos[] = $caminho;
        }

        $ordem = (int) Database::escalar(
            'SELECT COALESCE(MAX(sort_order), 0) FROM portfolio_gallery WHERE portfolio_id = ?',
            [$id]
        );

        foreach ($caminhos as $caminho) {
            $ordem++;
            Database::executar(
                'INSERT INTO portfolio_gallery (portfolio_id, image_url, sort_order) VALUES (?, ?, ?)',
                [$id, $caminho, $ordem]
            );
        }

        Session::flash('ok', 'Imagens adicionadas à galeria.');

        return Response::redirect($destino);
    }

    /**
     * Remove uma imagem da galeria. {id} é o id da linha de `portfolio_gallery`.
     */
    public function apagarGaleria(Request $request): Response
    {
        $idGaleria = $request->parametro('id');
        $imagem    = Database::um(
            'SELECT id, portfolio_id, image_url FROM portfolio_gallery WHERE id = ?',
            [$idGaleria]
        );

        if ($imagem === null) {
            return $this->naoEncontrado();
        }

        $this->apagarFicheiro((string) ($imagem['image_url'] ?? ''));
        Database::executar('DELETE FROM portfolio_gallery WHERE id = ?', [$idGaleria]);

        Session::flash('ok', 'Imagem removida da galeria.');

        return Response::redirect(
            $this->prefixo() . '/portfolio/' . rawurlencode((string) $imagem['portfolio_id']) . '/editar#galeria'
        );
    }

    // ------------------------------------------------------------------
    // Validação
    // ------------------------------------------------------------------

    /**
     * Valida os campos do formulário de portefólio.
     *
     * @param  array<string, mixed> $post
     * @return array<string, string> erros por campo (vazio = válido)
     */
    private function validar(array $post, ?string $idExcluir): array
    {
        $regras = [
            'title'       => 'required|string|max:200',
            'slug'        => 'nullable|string|max:200|unique:portfolio_items,slug'
                . ($idExcluir !== null ? ',' . $idExcluir : ''),
            'category'    => 'required|in:' . implode(',', self::CATEGORIAS),
            'status'      => 'required|in:' . implode(',', self::ESTADOS),
            'segment'     => 'nullable|in:' . implode(',', self::SEGMENTOS),
            'description' => 'required|string',
            'area'        => 'nullable|string|max:50',
            'year'        => 'nullable|integer',
            'location'    => 'nullable|string|max:255',
        ];

        $rotulos = [
            'title'       => 'título',
            'slug'        => 'slug',
            'category'    => 'categoria',
            'status'      => 'estado',
            'segment'     => 'segmento',
            'description' => 'descrição',
            'area'        => 'área',
            'year'        => 'ano',
            'location'    => 'localização',
            'sort_order'  => 'ordem',
        ];

        $validado = Validator::fazer($post, $regras, $rotulos);
        $erros    = $validado->erros();

        // O Validator não conhece "between" — o intervalo do ano é checado aqui.
        $ano = trim((string) ($post['year'] ?? ''));
        if ($ano !== '' && !isset($erros['year']) && ((int) $ano < 2000 || (int) $ano > 2030)) {
            $erros['year'] = 'O campo ano deve estar entre 2000 e 2030.';
        }

        // A ordem também é checada à mão: a regra "integer" do Validator usa
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
    // Uploads (capa e galeria)
    // ------------------------------------------------------------------

    /**
     * Valida um ficheiro enviado (sem o mover).
     *
     * @param  array<string, mixed> $ficheiro entrada de $_FILES
     * @param  array<string, string> $erros    erros por referência
     * @return string|null caminho temporário válido, ou null
     */
    private function validarImagem(array $ficheiro, array &$erros, string $chave, string $rotulo): ?string
    {
        $tmp = (string) ($ficheiro['tmp_name'] ?? '');

        if ($tmp === '' || !is_uploaded_file($tmp)) {
            $erros[$chave] = "A {$rotulo} não foi carregada corretamente.";

            return null;
        }

        $codigo = (int) ($ficheiro['error'] ?? UPLOAD_ERR_OK);
        if ($codigo !== UPLOAD_ERR_OK) {
            $erros[$chave] = "Falha no envio da {$rotulo} (erro {$codigo}).";

            return null;
        }

        $tamanho = (int) ($ficheiro['size'] ?? 0);
        if ($tamanho <= 0) {
            $erros[$chave] = "A {$rotulo} está vazia.";

            return null;
        }

        if ($tamanho > self::TAMANHO_MAXIMO) {
            $erros[$chave] = 'A ' . $rotulo . ' ultrapassa os 5 MB.';

            return null;
        }

        $extensao = strtolower(pathinfo((string) ($ficheiro['name'] ?? ''), PATHINFO_EXTENSION));
        if (!isset(self::EXTENSOES[$extensao])) {
            $erros[$chave] = "A {$rotulo} tem de ser JPG, PNG ou WEBP.";

            return null;
        }

        // MIME real do ficheiro — a extensão sozinha não prova nada.
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($tmp);

        // Variantes de nomenclatura que a mesma imagem pode devolver.
        if ($mime === 'image/x-png') {
            $mime = 'image/png';
        }
        if ($mime === 'image/jpg') {
            $mime = 'image/jpeg';
        }

        if ($mime !== self::EXTENSOES[$extensao]) {
            $erros[$chave] = "O ficheiro da {$rotulo} não é uma imagem válida.";

            return null;
        }

        return $tmp;
    }

    /**
     * Move um upload já validado para public/storage/portfolio/ com nome aleatório.
     * Devolve o caminho guardado na BD (storage/portfolio/NOME.ext) ou null.
     */
    private function moverImagem(string $tmp, string $nomeOriginal): ?string
    {
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        if (!isset(self::EXTENSOES[$extensao])) {
            return null;
        }

        $directorio = self::directorio();
        if (!is_dir($directorio) && !@mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            Logger::erro('portfolio.directorio_indisponivel', ['directorio' => $directorio]);

            return null;
        }

        $nome  = bin2hex(random_bytes(20)) . '.' . $extensao;
        $destino = $directorio . DIRECTORY_SEPARATOR . $nome;

        if (!move_uploaded_file($tmp, $destino)) {
            Logger::erro('portfolio.upload_falhou', ['destino' => $destino]);

            return null;
        }

        @chmod($destino, 0644);

        return 'storage/portfolio/' . $nome;
    }

    /**
     * Apaga um ficheiro de imagem — só caminhos dentro de public/storage/portfolio/.
     */
    private function apagarFicheiro(?string $caminho): void
    {
        if ($caminho === null || $caminho === '') {
            return;
        }

        if (!str_starts_with($caminho, 'storage/portfolio/')) {
            return;
        }

        $directorio = realpath(self::directorio());
        $alvo       = realpath(dirname(__DIR__, 3) . '/public/' . $caminho);

        if ($directorio === false || $alvo === false) {
            return;
        }

        // Nunca sair do directório de uploads (bloqueia ../ e ligações soltas).
        if (!str_starts_with($alvo, $directorio . DIRECTORY_SEPARATOR)) {
            return;
        }

        if (is_file($alvo)) {
            @unlink($alvo);
        }
    }

    // ------------------------------------------------------------------
    // Utilitários
    // ------------------------------------------------------------------

    private static function directorio(): string
    {
        return dirname(__DIR__, 3) . '/public/storage/portfolio';
    }

    private function prefixo(): string
    {
        return '/' . Config::obter('ADMIN_PATH', 'admin');
    }

    private function naoEncontrado(): Response
    {
        return Response::html(View::render('errors/404'), 404);
    }

    /**
     * Guarda erros + input antigo na sessão e devolve o redirect de volta.
     *
     * @param array<string, string> $erros
     * @param array<string, mixed>  $post
     */
    private function comErros(array $erros, array $post, string $destino): Response
    {
        Session::colocar('_erros_validacao', $erros);
        Session::colocar('_old_input', array_filter($post, 'is_scalar'));

        return Response::redirect($destino);
    }

    /**
     * Slug: vazio deriva do título; nunca repete um slug já existente.
     */
    private function resolverSlug(?string $slug, string $titulo, ?string $idExcluir): string
    {
        $base = trim((string) $slug) !== '' ? $this->slugificar($slug) : $this->slugificar($titulo);

        if ($base === '') {
            $base = 'projeto';
        }

        $final  = $base;
        $sufixo = 2;

        while (true) {
            $sql    = 'SELECT COUNT(*) FROM portfolio_items WHERE slug = ?';
            $params = [$final];

            if ($idExcluir !== null && $idExcluir !== '') {
                $sql   .= ' AND id != ?';
                $params[] = $idExcluir;
            }

            if ((int) Database::escalar($sql, $params) === 0) {
                break;
            }

            $final  = $base . '-' . $sufixo;
            $sufixo++;
        }

        return $final;
    }

    /**
     * Forma "slug" de um texto (minúsculas, sem acentos, sem caracteres soltos).
     */
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
