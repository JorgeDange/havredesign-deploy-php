<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Throttle — rate limiting simples por IP + acção, para formulários públicos.
 * Mesmo padrão do Auth (contadores em ficheiros em storage/app/private/throttle).
 */
final class Throttle
{
    private const JANELA_PADRAO = 900;      // 15 minutos
    private const MAX_PADRAO    = 10;       // submissões por janela

    /**
     * Devolve null se dentro do limite, ou a mensagem de bloqueio.
     */
    public static function verificar(string $acao, string $ip): ?string
    {
        $dados = self::ler($acao, $ip);
        $max   = Config::inteiro('FORM_MAX_SUBMISSOES', self::MAX_PADRAO);
        $janela = Config::inteiro('FORM_BLOQUEIO_SEGUNDOS', self::JANELA_PADRAO);

        if ($dados['total'] >= $max && (time() - $dados['ultima']) < $janela) {
            $minutos = (int) ceil(($janela - (time() - $dados['ultima'])) / 60);

            return "Demasiadas submissões. Tente novamente em {$minutos} minuto(s).";
        }

        if ((time() - $dados['ultima']) >= $janela) {
            self::gravar($acao, $ip, ['total' => 0, 'ultima' => 0]);
        }

        return null;
    }

    /**
     * Regista uma submissão (chamar após gravação bem-sucedida).
     */
    public static function registar(string $acao, string $ip): void
    {
        $dados = self::ler($acao, $ip);
        $dados['total']++;
        $dados['ultima'] = time();
        self::gravar($acao, $ip, $dados);
    }

    // ------------------------------------------------------------------

    private static function directorio(): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/app/private/throttle';

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return $dir;
    }

    private static function ficheiro(string $acao, string $ip): string
    {
        return self::directorio() . '/' . sha1($acao . '|' . $ip) . '.json';
    }

    /** @return array{total: int, ultima: int} */
    private static function ler(string $acao, string $ip): array
    {
        $caminho = self::ficheiro($acao, $ip);

        if (is_file($caminho)) {
            $json = json_decode((string) @file_get_contents($caminho), true);

            if (is_array($json)) {
                return [
                    'total'  => (int) ($json['total'] ?? 0),
                    'ultima' => (int) ($json['ultima'] ?? 0),
                ];
            }
        }

        return ['total' => 0, 'ultima' => 0];
    }

    private static function gravar(string $acao, string $ip, array $dados): void
    {
        $caminho = self::ficheiro($acao, $ip);
        @file_put_contents(
            $caminho,
            json_encode(['total' => (int) $dados['total'], 'ultima' => (int) $dados['ultima']]),
            LOCK_EX
        );
    }
}
