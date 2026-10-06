<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session — início seguro de sessão com cookies protegidos.
 */
final class Session
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = Config::booleano('SESSION_SECURE_COOKIE', false);
        $domain = Config::sessionDomain();

        // Pasta de sessão própria (evita /tmp partilhado em hosting)
        $savePath = dirname(__DIR__, 2) . '/storage/sessions';
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0755, true);
        }
        session_save_path($savePath);

        // Nome da sessão separado (session_set_cookie_params não aceita 'name')
        session_name(Config::obter('SESSION_NAME', 'havre_sessao'));

        $opcoes = [
            'lifetime' => Config::inteiro('SESSION_LIFETIME', 120) * 60,
            'path'     => '/',
            'secure'   => $secure,   // true em produção (HTTPS)
            'httponly' => true,      // inacessível ao JavaScript
            'samesite' => 'Lax',     // Lax: permite envio em POST de formulário mesmo-origin
        ];

        if ($domain !== null) {
            $opcoes['domain'] = $domain;
        }

        // PHP 7.3+ usa session_set_cookie_params com array
        session_set_cookie_params($opcoes);

        // Parâmetros de arranque seguros
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.sid_length', '48');
        ini_set('session.sid_bits_per_character', '6');

        session_start();

        // Expansão de sessão por actividade (rolling)
        if (isset($_SESSION['_ultimo_acesso'])) {
            $inativo = time() - (int) $_SESSION['_ultimo_acesso'];
            $maximo = Config::inteiro('SESSION_LIFETIME', 120) * 60;
            if ($inativo > $maximo) {
                self::destruir();
                session_start();
            }
        }
        $_SESSION['_ultimo_acesso'] = time();
    }

    /**
     * Obtém um valor da sessão (e apaga-o se $limpar = true — flash messages).
     */
    public static function obter(string $chave, mixed $padrao = null, bool $limpar = false): mixed
    {
        $valor = $_SESSION[$chave] ?? $padrao;
        if ($limpar) {
            unset($_SESSION[$chave]);
        }

        return $valor;
    }

    public static function colocar(string $chave, mixed $valor): void
    {
        $_SESSION[$chave] = $valor;
    }

    /**
     * Flash: valor visível só no próximo request.
     */
    public static function flash(string $chave, mixed $valor): void
    {
        $_SESSION['_flash'][$chave] = $valor;
    }

    /**
     * Lê uma flash e marca para remoção.
     */
    public static function lerFlash(string $chave, mixed $padrao = null): mixed
    {
        $valor = $_SESSION['_flash'][$chave] ?? $padrao;
        unset($_SESSION['_flash'][$chave]);

        return $valor;
    }

    /**
     * Regenera o ID da sessão (obrigatório após login — evita fixation).
     */
    public static function regenerar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Destruição completa (logout).
     */
    public static function destruir(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();

            // Apagar o cookie também
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                $domain = Config::sessionDomain();
                setcookie(session_name(), '', [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $domain ?? $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => 'Lax',
                ]);
            }
        }
    }
}
