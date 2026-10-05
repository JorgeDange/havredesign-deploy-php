<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Database;
use App\Core\Logger;

/**
 * Site — settings da BD ($site do Laravel: AppServiceProvider → View::share).
 * Chave:valor da tabela `settings`, com cache em ficheiro por request.
 */
final class Site
{
    /** @var array<string, string>|null */
    private static ?array $cache = null;

    /**
     * Obtém TODAS as settings como array (chaves com ponto: 'contact.email').
     *
     * @return array<string, string>
     */
    public static function todas(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            $linhas = Database::todos('SELECT `key`, `value` FROM settings');
            $cache  = [];
            foreach ($linhas as $linha) {
                $cache[(string) $linha['key']] = (string) $linha['value'];
            }
            self::$cache = $cache;
        } catch (\Throwable $e) {
            Logger::erro('settings.ler_falhou', ['mensagem' => $e->getMessage()]);
            self::$cache = [];
        }

        return self::$cache;
    }

    /**
     * Obtém uma setting: Site::obter('contact.email').
     */
    public static function obter(string $chave, string $padrao = ''): string
    {
        $todas = self::todas();

        return $todas[$chave] ?? $padrao;
    }

    /**
     * Setting JSON (ex.: 'agenda') decodificada.
     */
    public static function json(string $chave, array $padrao = []): array
    {
        $bruto = self::obter($chave);
        if ($bruto === '') {
            return $padrao;
        }

        $dados = json_decode($bruto, true);

        return is_array($dados) ? $dados : $padrao;
    }

    /**
     * Array no formato do `$site` do Laravel (para os templates).
     *
     * @return array<string, string>
     */
    public static function site(): array
    {
        return self::todas();
    }

    /**
     * URL das imagens (assets do site).
     */
    public static function imagem(string $caminho, string $fallback = ''): string
    {
        $relativo = ltrim($caminho, '/');
        $absoluto = dirname(__DIR__, 2) . '/public/' . $relativo;

        if (is_file($absoluto)) {
            return '/' . $relativo;
        }

        if ($fallback !== '' && is_file(dirname(__DIR__, 2) . '/public/' . ltrim($fallback, '/'))) {
            return '/' . ltrim($fallback, '/');
        }

        return '/' . $relativo;
    }
}
