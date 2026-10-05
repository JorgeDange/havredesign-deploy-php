<?php

declare(strict_types=1);

/**
 * Apoio a testes — HTTP via curl, login, asserções e limpeza.
 * Uso: require __DIR__ . '/Apoio.php';
 * O servidor tem de estar a correr (por omissão http://127.0.0.1:8090).
 */

final class Apoio
{
    public static string $base = 'http://127.0.0.1:8090';

    private static string $ficheiroCookies = '';

    private static int $passaram = 0;

    private static int $falharam = 0;

    private static string $suite = '';

    /** @var array<int, string> mensagens de falha */
    private static array $mensagens = [];

    // ------------------------------------------------------------------
    // Ciclo de vida
    // ------------------------------------------------------------------

    public static function iniciarSuite(string $nome): void
    {
        self::$suite = $nome;
        echo "\n== {$nome} ==\n";
        self::$ficheiroCookies = tempnam(sys_get_temp_dir(), 'cktest');
    }

    public static function terminarSuite(): void
    {
        if (self::$ficheiroCookies !== '' && is_file(self::$ficheiroCookies)) {
            @unlink(self::$ficheiroCookies);
        }
        self::$ficheiroCookies = '';
    }

    public static function resumo(): int
    {
        echo "\n----------------------------------------\n";
        echo 'Total: ' . (self::$passaram + self::$falharam)
            . ' | OK: ' . self::$passaram
            . ' | Falhas: ' . self::$falharam . "\n";

        foreach (self::$mensagens as $m) {
            echo "  FALHA: {$m}\n";
        }

        return self::$falharam === 0 ? 0 : 1;
    }

    public static function verificar(string $descricao, bool $condicao): void
    {
        if ($condicao) {
            self::$passaram++;
            echo "  ok  {$descricao}\n";
        } else {
            self::$falharam++;
            self::$mensagens[] = self::$suite . ' → ' . $descricao;
            echo "  FALHA {$descricao}\n";
        }
    }

    // ------------------------------------------------------------------
    // HTTP
    // ------------------------------------------------------------------

    /**
     * @return array{status: int, corpo: string, headers: string}
     */
    public static function get(string $caminho, bool $comSessao = true): array
    {
        return self::pedir('GET', $caminho, null, $comSessao);
    }

    /**
     * @param array<string, mixed>|null $dados
     * @return array{status: int, corpo: string, headers: string}
     */
    public static function post(string $caminho, ?array $dados = null, bool $comSessao = true): array
    {
        return self::pedir('POST', $caminho, $dados, $comSessao);
    }

    /**
     * @param array<string, mixed>|null $dados
     * @return array{status: int, corpo: string, headers: string}
     */
    public static function pedir(string $metodo, string $caminho, ?array $dados, bool $comSessao): array
    {
        $ck = self::$ficheiroCookies !== '' ? self::$ficheiroCookies : null;
        $ch = curl_init(self::$base . $caminho);

        $opcoes = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 20,
        ];

        if ($comSessao && $ck !== null) {
            $opcoes[CURLOPT_COOKIEJAR]  = $ck;
            $opcoes[CURLOPT_COOKIEFILE] = $ck;
        }

        if ($metodo === 'POST') {
            $opcoes[CURLOPT_POST] = true;
            $corpo = $dados ?? [];

            if (!isset($corpo['_token'])) {
                $pagina = self::get($caminho, $comSessao);
                if (preg_match('/name="_token" value="([^"]+)"/', $pagina['corpo'], $m)) {
                    $corpo['_token'] = $m[1];
                }
            }

            $opcoes[CURLOPT_POSTFIELDS] = http_build_query($corpo);
        }

        curl_setopt_array($ch, $opcoes);
        $bruto = (string) curl_exec($ch);
        curl_close($ch);

        $divisao = strpos($bruto, "\r\n\r\n");
        $cabecalhos = $divisao === false ? $bruto : substr($bruto, 0, $divisao);
        $corpo = $divisao === false ? '' : substr($bruto, $divisao + 4);

        preg_match('#HTTP/[\d.]+ (\d{3})#', $cabecalhos, $mm);

        return [
            'status'  => (int) ($mm[1] ?? 0),
            'corpo'   => $corpo,
            'headers' => $cabecalhos,
        ];
    }

    public static function localizacao(array $resposta): string
    {
        return preg_match('/Location: (.+)/i', $resposta['headers'], $m)
            ? trim($m[1]) : '';
    }

    // ------------------------------------------------------------------
    // Sessão / BD
    // ------------------------------------------------------------------

    /** Autentica como admin (lê ADMIN_PASSWORD de backend/.env). */
    public static function loginAdmin(): bool
    {
        $env = @file_get_contents(dirname(__DIR__) . '/backend/.env') ?: '';
        if (!preg_match('/ADMIN_PASSWORD=(\S+)/', $env, $m)) {
            return false;
        }

        $r = self::post('/entrar', [
            'email'    => 'admin@havredesign.com',
            'password' => $m[1],
            'remember' => 1,
        ]);

        return $r['status'] === 302 && self::localizacao($r) === '/admin';
    }

    /** Caminho do ficheiro de cookies partilhado pela suite (jar + file). */
    public static function cookies(): string
    {
        if (self::$ficheiroCookies === '' || !is_file(self::$ficheiroCookies)) {
            self::$ficheiroCookies = tempnam(sys_get_temp_dir(), 'cktest') ?: '';
        }

        return self::$ficheiroCookies;
    }

    public static function bd(): PDO
    {
        static $pdo = null;

        return $pdo ??= new PDO('mysql:host=127.0.0.1;dbname=havre_design;charset=utf8mb4', 'root', '');
    }

    /** Confirma que o servidor de desenvolvimento está a correr. */
    public static function servidorDisponivel(): bool
    {
        $r = self::get('/', false);

        return $r['status'] > 0;
    }
}
