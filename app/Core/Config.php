<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Config — leitura centralizada do .env (sem framework).
 * Todos os valores são lidos uma única vez no arranque.
 */
final class Config
{
    /** @var array<string, string> */
    private static array $valores = [];

    private static bool $carregada = false;

    /**
     * Carrega o .env da raiz do projecto.
     * Regras: sem aspas comments, KEY=VALUE, ignora linhas vazias/comentários.
     */
    public static function carregar(string $raiz): void
    {
        if (self::$carregada) {
            return;
        }

        $ficheiro = $raiz . '/.env';
        if (is_file($ficheiro)) {
            $linhas = file($ficheiro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($linhas as $linha) {
                $linha = trim($linha);
                if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) {
                    continue;
                }

                [$chave, $valor] = explode('=', $linha, 2);
                $chave = trim($chave);
                $valor = trim($valor);

                // Remover aspas envolventes
                if (strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && $valor[0] === $valor[strlen($valor) - 1]) {
                    $valor = substr($valor, 1, -1);
                }

                self::$valores[$chave] = $valor;

                // Disponibilizar também ao getenv() para o front controller
                putenv("{$chave}={$valor}");
            }
        }

        self::$carregada = true;
    }

    /**
     * Obtém um valor por chave, com valor por omissão.
     */
    public static function obter(string $chave, string $padrao = ''): string
    {
        return self::$valores[$chave] ?? $padrao;
    }

    /**
     * Obtém um valor booleano.
     */
    public static function booleano(string $chave, bool $padrao = false): bool
    {
        $valor = self::$valores[$chave] ?? null;
        if ($valor === null) {
            return $padrao;
        }

        return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Obtém um valor inteiro.
     */
    public static function inteiro(string $chave, int $padrao = 0): int
    {
        $valor = self::$valores[$chave] ?? null;
        if ($valor === null || !is_numeric($valor)) {
            return $padrao;
        }

        return (int) $valor;
    }

    /**
     * Produção? (usado para HSTS, erro genérico, etc.)
     */
    public static function eProducao(): bool
    {
        return self::obter('APP_ENV') === 'production';
    }

    /**
     * Domínio do cookie de sessão (ex.: .havredesign.ao para www + não-www).
     * Vazio = domínio atual (comportamento padrão).
     */
    public static function sessionDomain(): ?string
    {
        $dominio = self::obter('SESSION_DOMAIN', '');
        return $dominio !== '' ? $dominio : null;
    }
}
