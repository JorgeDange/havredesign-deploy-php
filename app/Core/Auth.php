<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Auth — autenticação com password_hash()/password_verify(),
 * rate limiting de tentativas e sessão regenerada após login.
 */
final class Auth
{
    private const CHAVE_UTILIZADOR = '_auth_user_id';
    private const CHAVE_LEMBRAR    = '_remember_token';

    // ------------------------------------------------------------------
    // Login
    // ------------------------------------------------------------------

    /**
     * Tenta autenticar. Devolve null em sucesso ou a mensagem de erro.
     * Aplica throttle: N tentativas por IP+e-mail dentro da janela.
     */
    public static function tentarLogin(string $email, string $senha, bool $lembrar, string $ip): ?string
    {
        // 1. Rate limiting (5 tentativas / 15 min por omissão)
        $erro = self::verificarThrottle($email, $ip);
        if ($erro !== null) {
            return $erro;
        }

        // 2. Procurar utilizador
        $utilizador = Database::um(
            'SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1',
            [$email]
        );

        // 3. Verificar senha (se não existir utilizador, correr verify contra hash falso
        //    para não revelar por timing quem existe)
        $hash = $utilizador['password'] ?? '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalidi';
        $senhaOk = password_verify($senha, $hash);

        if ($utilizador === null || !$senhaOk) {
            self::registarTentativa($email, $ip);
            Logger::aviso('auth.login_falhou', ['email' => $email, 'ip' => $ip]);

            return 'Credenciais inválidas.';
        }

        // 4. Sucesso — limpar throttle, regenerar sessão, criar sessão
        self::limparThrottle($email, $ip);
        Session::regenerar();
        Csrf::rotacionar();

        Session::colocar(self::CHAVE_UTILIZADOR, (string) $utilizador['id']);
        Session::colocar('utilizador', [
            'id'    => (string) $utilizador['id'],
            'name'  => (string) $utilizador['name'],
            'email' => (string) $utilizador['email'],
            'role'  => (string) $utilizador['role'],
        ]);

        // 5. Cookie "lembrar-me" (30 dias, opaco e guardado na BD)
        if ($lembrar) {
            self::criarCookieLembrar((string) $utilizador['id']);
        }

        Logger::info('auth.login_ok', ['user_id' => $utilizador['id'], 'ip' => $ip]);

        return null;
    }

    // ------------------------------------------------------------------
    // Sessão
    // ------------------------------------------------------------------

    /**
     * Utilizador autenticado actual (array) ou null.
     *
     * @return array<string, mixed>|null
     */
    public static function utilizador(): ?array
    {
        $u = Session::obter('utilizador');

        return is_array($u) ? $u : null;
    }

    public static function autenticado(): bool
    {
        return self::utilizador() !== null;
    }

    public static function eAdmin(): bool
    {
        $u = self::utilizador();

        return $u !== null && ($u['role'] ?? '') === 'ADMIN';
    }

    public static function id(): ?string
    {
        $u = self::utilizador();

        return $u !== null ? (string) $u['id'] : null;
    }

    /**
     * Logout — destrói completamente a sessão e o cookie de lembrar.
     */
    public static function sair(): void
    {
        // Apagar token remember (se existir)
        $token = $_COOKIE[self::CHAVE_LEMBRAR] ?? null;
        if (is_string($token) && $token !== '') {
            Database::executar(
                'UPDATE users SET remember_token = NULL WHERE remember_token = ?',
                [hash('sha256', $token)]
            );
        }

        Session::destruir();

        if (isset($_COOKIE[self::CHAVE_LEMBRAR])) {
            setcookie(self::CHAVE_LEMBRAR, '', [
                'expires'  => time() - 42000,
                'path'     => '/',
                'httponly' => true,
                'secure'   => Config::booleano('SESSION_SECURE_COOKIE', false),
                'samesite' => 'Lax',
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Registo e palavras-passe
    // ------------------------------------------------------------------

    /**
     * Cria conta. Devolve null em sucesso ou mensagem de erro.
     */
    public static function registar(string $nome, string $email, string $senha): ?string
    {
        $existe = Database::um('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($existe !== null) {
            return 'Já existe uma conta com este e-mail.';
        }

        Database::executar(
            'INSERT INTO users (id, name, email, password, role, created_at, updated_at)
             VALUES (UUID(), ?, ?, ?, ?, NOW(), NOW())',
            [
                $nome,
                $email,
                password_hash($senha, PASSWORD_DEFAULT),
                'USER',
            ]
        );

        Logger::info('auth.registo', ['email' => $email]);

        return null;
    }

    /**
     * Altera a palavra-passe do utilizador actual.
     */
    public static function alterarSenha(string $userId, string $senha): void
    {
        Database::executar(
            'UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?',
            [password_hash($senha, PASSWORD_DEFAULT), $userId]
        );
    }

    // ------------------------------------------------------------------
    // Throttle de login
    // ------------------------------------------------------------------

    private static function chaveThrottle(string $email, string $ip): string
    {
        return 'login:' . strtolower($email) . '|' . $ip;
    }

    private static function verificarThrottle(string $email, string $ip): ?string
    {
        $dados = self::lerThrottle($email, $ip);
        $max   = Config::inteiro('LOGIN_MAX_TENTATIVAS', 5);
        $janela = Config::inteiro('LOGIN_BLOQUEIO_SEGUNDOS', 900);

        if ($dados['tentativas'] >= $max && (time() - $dados['ultima']) < $janela) {
            $minutos = (int) ceil(($janela - (time() - $dados['ultima'])) / 60);

            return "Demasiadas tentativas. Tente novamente em {$minutos} minuto(s).";
        }

        // Janela expirada → reset
        if ((time() - $dados['ultima']) >= $janela) {
            self::limparThrottle($email, $ip);
        }

        return null;
    }

    private static function registarTentativa(string $email, string $ip): void
    {
        $dados = self::lerThrottle($email, $ip);
        $dados['tentativas']++;
        $dados['ultima'] = time();
        self::gravarThrottle($email, $ip, $dados);
    }

    private static function limparThrottle(string $email, string $ip): void
    {
        self::gravarThrottle($email, $ip, ['tentativas' => 0, 'ultima' => 0]);
    }

    /**
     * Persistência do throttle: sessão + ficheiro em storage/cache (funciona
     * mesmo com múltiplas sessões — o IP partilhado do atacante é o alvo).
     *
     * @return array{tentativas: int, ultima: int}
     */
    private static function lerThrottle(string $email, string $ip): array
    {
        $ficheiro = dirname(__DIR__, 2) . '/storage/cache/login-throttle.json';
        $todos    = self::lerFicheiro($ficheiro);
        $chave    = self::chaveThrottle($email, $ip);

        return $todos[$chave] ?? ['tentativas' => 0, 'ultima' => 0];
    }

    private static function gravarThrottle(string $email, string $ip, array $dados): void
    {
        $ficheiro = dirname(__DIR__, 2) . '/storage/cache/login-throttle.json';
        $todos    = self::lerFicheiro($ficheiro);
        $chave    = self::chaveThrottle($email, $ip);

        $todos[$chave] = $dados;

        // Limpar entradas velhas (> 24h)
        foreach ($todos as $k => $v) {
            if ($v['ultima'] > 0 && (time() - $v['ultima']) > 86400) {
                unset($todos[$k]);
            }
        }

        @file_put_contents(
            $ficheiro,
            json_encode($todos, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    /**
     * @return array<string, array{tentativas: int, ultima: int}>
     */
    private static function lerFicheiro(string $ficheiro): array
    {
        if (!is_file($ficheiro)) {
            return [];
        }

        $dados = json_decode((string) file_get_contents($ficheiro), true);

        return is_array($dados) ? $dados : [];
    }

    // ------------------------------------------------------------------
    // Cookie "lembrar-me"
    // ------------------------------------------------------------------

    private static function criarCookieLembrar(string $userId): void
    {
        $token = bin2hex(random_bytes(32));

        // Guardar só o hash — se a BD vazar, os tokens não servem
        Database::executar(
            'UPDATE users SET remember_token = ? WHERE id = ?',
            [hash('sha256', $token), $userId]
        );

        setcookie(self::CHAVE_LEMBRAR, $token, [
            'expires'  => time() + 30 * 86400,
            'path'     => '/',
            'httponly' => true,
            'secure'   => Config::booleano('SESSION_SECURE_COOKIE', false),
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Tenta restaurar sessão a partir do cookie de lembrar.
     * Chamar no arranque, antes de verificar autenticação.
     */
    public static function tentarLembrar(): void
    {
        if (self::autenticado()) {
            return;
        }

        $token = $_COOKIE[self::CHAVE_LEMBRAR] ?? null;
        if (!is_string($token) || $token === '') {
            return;
        }

        $utilizador = Database::um(
            'SELECT id, name, email, role FROM users WHERE remember_token = ? LIMIT 1',
            [hash('sha256', $token)]
        );

        if ($utilizador === null) {
            return;
        }

        // Sucesso — sessão nova + novo token (rotação)
        Session::regenerar();
        Session::colocar('utilizador', [
            'id'    => (string) $utilizador['id'],
            'name'  => (string) $utilizador['name'],
            'email' => (string) $utilizador['email'],
            'role'  => (string) $utilizador['role'],
        ]);
        self::criarCookieLembrar((string) $utilizador['id']);
    }
}
