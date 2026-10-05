<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Logger — registo de erros em storage/logs/ (níveis: info, warning, error).
 */
final class Logger
{
    private static string $directorio = '';

    public static function inicializar(): void
    {
        self::$directorio = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir(self::$directorio)) {
            @mkdir(self::$directorio, 0755, true);
        }
    }

    public static function info(string $mensagem, array $contexto = []): void
    {
        self::escrever('INFO', $mensagem, $contexto);
    }

    public static function aviso(string $mensagem, array $contexto = []): void
    {
        self::escrever('WARNING', $mensagem, $contexto);
    }

    public static function erro(string $mensagem, array $contexto = []): void
    {
        self::escrever('ERROR', $mensagem, $contexto);
    }

    private static function escrever(string $nivel, string $mensagem, array $contexto): void
    {
        if (self::$directorio === '') {
            self::inicializar();
        }

        $linha = sprintf(
            "[%s] %s %s%s\n",
            date('Y-m-d H:i:s'),
            $nivel,
            $mensagem,
            $contexto !== [] ? ' ' . json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
        );

        @file_put_contents(self::$directorio . '/app.log', $linha, FILE_APPEND | LOCK_EX);
    }
}
