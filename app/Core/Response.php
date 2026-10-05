<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Response — HTML, JSON, redirect e códigos de estado HTTP.
 */
final class Response
{
    private string $corpo;
    private int $estado;
    /** @var array<string, string> */
    private array $headers = [];

    public function __construct(string $corpo = '', int $estado = 200)
    {
        $this->corpo   = $corpo;
        $this->estado  = $estado;
    }

    /**
     * Resposta HTML a partir de uma view renderizada.
     */
    public static function html(string $conteudo, int $estado = 200): self
    {
        $resposta = new self($conteudo, $estado);
        $resposta->headers['Content-Type'] = 'text/html; charset=utf-8';

        return $resposta;
    }

    /**
     * Resposta JSON.
     */
    public static function json(mixed $dados, int $estado = 200): self
    {
        $resposta = new self(
            json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            $estado
        );
        $resposta->headers['Content-Type'] = 'application/json; charset=utf-8';

        return $resposta;
    }

    /**
     * Resposta de texto simples (robots.txt).
     */
    public static function txt(string $conteudo, int $estado = 200): self
    {
        $resposta = new self($conteudo, $estado);
        $resposta->headers['Content-Type'] = 'text/plain; charset=UTF-8';

        return $resposta;
    }

    /**
     * Resposta XML (sitemap.xml).
     */
    public static function xml(string $conteudo, int $estado = 200): self
    {
        $resposta = new self($conteudo, $estado);
        $resposta->headers['Content-Type'] = 'application/xml; charset=UTF-8';

        return $resposta;
    }

    /**
     * Redirect (302 por omissão; 301 para URLs permanentes/SEO).
     */
    public static function redirect(string $destino, int $estado = 302): self
    {
        $resposta = new self('', $estado);
        $resposta->headers['Location'] = $destino;

        return $resposta;
    }

    /**
     * Redirect 301 permanente (migração de URLs antigas).
     */
    public static function redirecionarPermanente(string $destino): self
    {
        return self::redirect($destino, 301);
    }

    public function comHeader(string $nome, string $valor): self
    {
        $this->headers[$nome] = $valor;

        return $this;
    }

    public function estado(): int
    {
        return $this->estado;
    }

    public function corpo(): string
    {
        return $this->corpo;
    }

    /**
     * Envia a resposta (headers + corpo) e termina.
     */
    public function enviar(): void
    {
        if (!headers_sent()) {
            http_response_code($this->estado);
            foreach ($this->headers as $nome => $valor) {
                header("{$nome}: {$valor}");
            }
        }

        // HEAD: só cabeçalhos, sem corpo (mesmo comportamento do GET)
        $metodo = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($metodo !== 'HEAD') {
            echo $this->corpo;
        }
        exit;
    }
}
