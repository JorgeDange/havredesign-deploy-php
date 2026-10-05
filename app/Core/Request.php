<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Request — encapsula GET, POST, FILES e SERVER com sanitização.
 */
final class Request
{
    private string $metodo;
    private string $caminho;

    /** @var array<string, mixed> */
    private array $get;

    /** @var array<string, mixed> */
    private array $post;

    /** @var array<string, array<string, mixed>> */
    private array $files;

    /** @var array<string, mixed> */
    private array $server;

    /** @var array<string, string> parâmetros de rota (/portfolio/{slug}) */
    private array $parametros = [];

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get    = $_GET;
        $this->post   = $_POST;
        $this->files  = $_FILES;

        $metodo = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        // Method spoofing: formulários HTML só fazem GET/POST
        if ($metodo === 'POST' && isset($this->post['_method'])) {
            $spoof = strtoupper((string) $this->post['_method']);
            if (in_array($spoof, ['PUT', 'PATCH', 'DELETE'], true)) {
                $metodo = $spoof;
            }
        }

        $this->metodo = $metodo;
        $this->caminho = self::normalizarCaminho($this->server['REQUEST_URI'] ?? '/');
    }

    /**
     * Limpa o caminho: sem query string, sem barras duplicadas, sem barra final.
     */
    public static function normalizarCaminho(string $uri): string
    {
        $caminho = parse_url($uri, PHP_URL_PATH) ?: '/';
        $caminho = rawurldecode($caminho);
        $caminho = '/' . trim($caminho, '/');

        return $caminho === '/' ? '/' : rtrim($caminho, '/');
    }

    public function metodo(): string
    {
        return $this->metodo;
    }

    public function caminho(): string
    {
        return $this->caminho;
    }

    public function eGet(): bool
    {
        return $this->metodo === 'GET';
    }

    public function ePost(): bool
    {
        return $this->metodo === 'POST';
    }

    // ------------------------------------------------------------------
    // Acesso a dados
    // ------------------------------------------------------------------

    /**
     * Valor do POST com sanitização de string.
     */
    public function input(string $chave, string $padrao = ''): string
    {
        $valor = $this->post[$chave] ?? $padrao;
        if (is_array($valor)) {
            return $padrao;
        }

        return self::sanitizar((string) $valor);
    }

    /**
     * Valor do POST sem alterar (para validação de tamanho real).
     */
    public function bruto(string $chave, mixed $padrao = null): mixed
    {
        return $this->post[$chave] ?? $padrao;
    }

    public function query(string $chave, string $padrao = ''): string
    {
        $valor = $this->get[$chave] ?? $padrao;
        if (is_array($valor)) {
            return $padrao;
        }

        return self::sanitizar((string) $valor);
    }

    public function queryInteiro(string $chave, int $padrao = 0): int
    {
        $valor = $this->get[$chave] ?? null;

        return is_numeric($valor) ? (int) $valor : $padrao;
    }

    /**
     * Parâmetro de rota ({slug}).
     */
    public function parametro(string $chave, string $padrao = ''): string
    {
        return $this->parametros[$chave] ?? $padrao;
    }

    public function definirParametros(array $parametros): void
    {
        $this->parametros = $parametros;
    }

    /**
     * @return array<string, string>
     */
    public function parametros(): array
    {
        return $this->parametros;
    }

    /**
     * Todos os dados do POST (para validação em lote).
     *
     * @return array<string, mixed>
     */
    public function todos(): array
    {
        return $this->post;
    }

    /**
     * Ficheiro carregado (ou null).
     *
     * @return array<string, mixed>|null
     */
    public function ficheiro(string $chave): ?array
    {
        $ficheiro = $this->files[$chave] ?? null;
        if (!is_array($ficheiro) || ($ficheiro['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $ficheiro;
    }

    /**
     * Vários ficheiros (array de inputs[]).
     *
     * @return array<int, array<string, mixed>>
     */
    public function ficheiros(string $chave): array
    {
        $ficheiro = $this->files[$chave] ?? null;
        if (!is_array($ficheiro) || !isset($ficheiro['name'][0])) {
            return [];
        }

        $lista = [];
        $total = count($ficheiro['name']);
        for ($i = 0; $i < $total; $i++) {
            $lista[] = [
                'name'     => $ficheiro['name'][$i],
                'type'     => $ficheiro['type'][$i],
                'tmp_name' => $ficheiro['tmp_name'][$i],
                'error'    => $ficheiro['error'][$i],
                'size'     => $ficheiro['size'][$i],
            ];
        }

        return $lista;
    }

    /**
     * IP do cliente (para throttle). Usa REMOTE_ADDR — nunca X-Forwarded-For
     * sem proxy de confiança (spoofable).
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * É um pedido AJAX?
     */
    public function eAjax(): bool
    {
        return strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    /**
     * Sanitização básica de string (não substitui validação nem escape).
     */
    private static function sanitizar(string $valor): string
    {
        // Remove bytes de controlo (exceto tab, newline, cr)
        $valor = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $valor) ?? '';

        return trim($valor);
    }
}
