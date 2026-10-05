<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View — templates PHP com layouts, secções e partials.
 *
 * Equivalente ao Blade:
 *   @extends('layouts.app')  →  View::layout('layouts/app')   (no topo da página)
 *   @section('content')      →  View::secaoInicio('content')
 *   @endsection              →  View::secaoFim()
 *   @yield('content')        →  <?= View::sec('content') ?>   (no layout)
 *   @include('partials.x')   →  <?= View::parcial('partials/x', [...]) ?>
 *
 * Escape: use sempre e($variavel) para variáveis de utilizador.
 */
final class View
{
    private const DIRECTORIO = __DIR__ . '/../../resources/views';

    private static ?string $layout = null;

    /** @var array<string, string> secções capturadas */
    private static array $secacoes = [];

    /** @var array<int, string> pilha de nomes das secções em curso */
    private static array $buffers = [];

    // ------------------------------------------------------------------
    // API usada nos templates
    // ------------------------------------------------------------------

    /**
     * Define o layout da página actual (chamar no topo do template).
     */
    public static function layout(string $nome): void
    {
        self::$layout = $nome;
    }

    public static function secaoInicio(string $nome): void
    {
        self::$buffers[] = $nome;
        ob_start();
    }

    public static function secaoFim(): void
    {
        $nome = array_pop(self::$buffers);
        if ($nome === null) {
            return;
        }

        $conteudo = (string) ob_get_clean();
        self::$secacoes[$nome] = (self::$secacoes[$nome] ?? '') . $conteudo;
    }

    /**
     * Imprime o conteúdo de uma secção (HTML, não escapado — já é markup).
     */
    public static function sec(string $nome, string $padrao = ''): void
    {
        echo self::$secacoes[$nome] ?? $padrao;
    }

    /**
     * Renderiza uma partial (snippet reutilizável) e devolve-a como string.
     *
     * @param array<string, mixed> $dados
     */
    public static function parcial(string $nome, array $dados = []): string
    {
        $variaveis = null;

        return self::renderInterno($nome, $dados, $variaveis);
    }

    // ------------------------------------------------------------------
    // Renderização
    // ------------------------------------------------------------------

    /**
     * Renderiza uma view com layout (se a página definir) e devolve o HTML.
     *
     * @param array<string, mixed> $dados
     */
    public static function render(string $nome, array $dados = []): string
    {
        // Estado limpo a cada renderização (evita vazamento entre pedidos)
        self::$layout    = null;
        self::$secacoes  = [];
        self::$buffers   = [];

        $variaveis = [];
        $html = self::renderInterno($nome, $dados, $variaveis);

        if (self::$layout !== null) {
            // Se a página não criou secções, o conteúdo todo é a secção 'content'
            if (!array_key_exists('content', self::$secacoes)) {
                self::$secacoes['content'] = $html;
            }

            // Variáveis criadas na página ($titulo, $meta, …) chegam ao layout
            $layoutVariaveis = [];
            $html = self::renderInterno(self::$layout, array_merge($dados, $variaveis), $layoutVariaveis);
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, mixed> $variaveis  saída: variáveis definidas pela view
     */
    private static function renderInterno(string $nome, array $dados, ?array &$variaveis = null): string
    {
        $ficheiro = self::DIRECTORIO . '/' . $nome . '.php';

        if (!is_file($ficheiro)) {
            Logger::erro('view.nao_encontrada', ['view' => $nome]);
            throw new \RuntimeException("View inexistente: {$nome}");
        }

        ob_start();
        try {
            // $dados extraídos no scope do template
            extract($dados, EXTR_SKIP);
            include $ficheiro;
            if ($variaveis !== null) {
                // Captura o que a view definiu ($titulo, $meta, queries, …)
                $variaveis = get_defined_vars();
                unset($variaveis['dados'], $variaveis['ficheiro'], $variaveis['variaveis']);
            }
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }

    /**
     * Reset de estado — chamado pelo front controller se necessário.
     */
    public static function limpar(): void
    {
        // Fecha buffers órfãos (ex.: exceção no meio de uma secção)
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        self::$layout   = null;
        self::$secacoes = [];
        self::$buffers  = [];
    }
}
