<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router — GET/POST/PUT/DELETE, parâmetros dinâmicos, middleware por rota,
 * 404 personalizado.
 */
final class Router
{
    /**
     * @var array<int, array{
     *   metodo: string,
     *   padrao: string,
     *   regex: string,
     *   parametros: array<int, string>,
     *   acao: callable|array{0: class-string, 1: string},
     *   middlewares: array<int, string>
     * }>
     */
    private array $rotas = [];

    /** @var array<int, string> middlewares activos para as próximas rotas */
    private array $grupoMiddlewares = [];

    private string $prefixoGrupo = '';

    // ------------------------------------------------------------------
    // Registo
    // ------------------------------------------------------------------

    public function get(string $caminho, callable|array $acao, array $middlewares = []): void
    {
        $this->registar('GET', $caminho, $acao, $middlewares);
    }

    public function post(string $caminho, callable|array $acao, array $middlewares = []): void
    {
        $this->registar('POST', $caminho, $acao, $middlewares);
    }

    public function put(string $caminho, callable|array $acao, array $middlewares = []): void
    {
        $this->registar('PUT', $caminho, $acao, $middlewares);
    }

    public function delete(string $caminho, callable|array $acao, array $middlewares = []): void
    {
        $this->registar('DELETE', $caminho, $acao, $middlewares);
    }

    /**
     * Registo de uma rota estática que devolve uma view (Route::view).
     */
    public function view(string $caminho, string $vista): void
    {
        $this->get($caminho, function () use ($vista): Response {
            return Response::html(View::render($vista));
        });
    }

    /**
     * Agrupamento de rotas com middlewares comuns (ex.: admin).
     *
     * @param callable(self): void $bloco
     */
    public function grupo(array $middlewares, callable $bloco, string $prefixo = ''): void
    {
        $anteriores         = $this->grupoMiddlewares;
        $prefixoAnterior    = $this->prefixoGrupo;

        $this->grupoMiddlewares = array_merge($this->grupoMiddlewares, $middlewares);
        $this->prefixoGrupo     = $prefixoAnterior . $prefixo;

        $bloco($this);

        $this->grupoMiddlewares = $anteriores;
        $this->prefixoGrupo     = $prefixoAnterior;
    }

    private function registar(string $metodo, string $caminho, callable|array $acao, array $middlewares): void
    {
        $caminho      = $this->prefixoGrupo . '/' . trim($caminho, '/');
        $caminho      = $caminho === '/' ? '/' : rtrim($caminho, '/');
        $parametros   = [];
        $partes       = [];

        foreach (explode('/', $caminho) as $pedaco) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $pedaco, $m)) {
                // {slug} → parâmetro obrigatório (uma segmento sem /)
                $parametros[] = $m[1];
                $partes[]     = '([^/]+)';
            } elseif (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\?\}$/', $pedaco, $m)) {
                // {id?} → parâmetro opcional
                $parametros[] = $m[1];
                $partes[]     = '([^/]*)';
            } else {
                $partes[] = preg_quote($pedaco, '#');
            }
        }

        $this->rotas[] = [
            'metodo'      => $metodo,
            'padrao'      => $caminho,
            'regex'       => '#^' . implode('/', $partes) . '$#',
            'parametros'  => $parametros,
            'acao'        => $acao,
            'middlewares' => array_values(array_unique(array_merge($this->grupoMiddlewares, $middlewares))),
        ];
    }

    // ------------------------------------------------------------------
    // Resolução
    // ------------------------------------------------------------------

    /**
     * Encontra a rota, corre middlewares e devolve a resposta.
     */
    public function resolver(Request $request = null): void
    {
        $request ??= new Request();
        $caminho  = $request->caminho();
        $metodo   = $request->metodo();

        // HEAD resolve como GET (o Response envia só os cabeçalhos)
        if ($metodo === 'HEAD') {
            $metodo = 'GET';
        }

        $correspondeu = false;

        foreach ($this->rotas as $rota) {
            if (!preg_match($rota['regex'], $caminho, $coincidencias)) {
                continue;
            }

            $correspondeu = true;

            if ($rota['metodo'] !== $metodo) {
                continue; // caminho existe noutro método → 405, não 404
            }

            // Liga os parâmetros {slug} aos valores
            $params = [];
            foreach ($rota['parametros'] as $i => $nome) {
                $params[$nome] = rawurldecode($coincidencias[$i + 1] ?? '');
            }
            $request->definirParametros($params);

            // Middlewares da rota (podem negar com resposta própria)
            foreach ($rota['middlewares'] as $middleware) {
                $resposta = $this->correrMiddleware($middleware, $request);
                if ($resposta instanceof Response) {
                    $resposta->enviar();
                }
            }

            $resultado = $this->correrAcao($rota['acao'], $request);

            if ($resultado instanceof Response) {
                $resultado->enviar();
            }
            if (is_string($resultado)) {
                Response::html($resultado)->enviar();
            }

            // A acção não devolveu nada (enviou e saiu por si só)
            exit;
        }

        if ($correspondeu) {
            // Caminho existe, método não — 405
            Response::html(View::render('errors/405', ['caminho' => $caminho]), 405)->enviar();
        }

        // 404 personalizado
        Response::html(View::render('errors/404'), 404)->enviar();
    }

    /**
     * Corre um middleware identificado pelo nome.
     * Devolve Response para interromper (ex.: 302 para /entrar).
     */
    private function correrMiddleware(string $middleware, Request $request): ?Response
    {
        return match ($middleware) {
            'csrf'   => \App\Middleware\CsrfMiddleware::handle($request),
            'auth'   => \App\Middleware\AuthMiddleware::handle($request),
            'admin'  => \App\Middleware\AdminMiddleware::handle($request),
            'guest'  => \App\Middleware\GuestMiddleware::handle($request),
            default  => null,
        };
    }

    /**
     * Executa a acção: closure ou [Controller, metodo].
     */
    private function correrAcao(callable|array $acao, Request $request): mixed
    {
        if (is_array($acao)) {
            [$classe, $metodo] = $acao;
            $controlador = new $classe();

            return $controlador->{$metodo}($request);
        }

        return $acao($request);
    }
}
