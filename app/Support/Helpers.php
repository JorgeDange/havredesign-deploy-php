<?php

/**
 * Helpers — funções auxiliares globais (carregadas no front controller).
 * Sem namespaces (uso livre nos templates).
 */

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\Logger;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

if (!function_exists('e')) {
    /**
     * Escape XSS — usar SEMPRE em variáveis de utilizador dentro dos templates.
     */
    function e(mixed $valor): string
    {
        if ($valor === null) {
            return '';
        }

        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    /**
     * URL de um asset estático (relativa à raiz pública).
     */
    function asset(string $caminho): string
    {
        $base = rtrim(Config::obter('APP_URL'), '/');
        // Remover o host, ficar só com o path (ex.: /havredesign/public)
        $path = parse_url($base, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');

        return $path . '/' . ltrim($caminho, '/');
    }
}

if (!function_exists('url')) {
    /**
     * URL absoluta da aplicação.
     */
    function url(string $caminho = ''): string
    {
        $base = rtrim(Config::obter('APP_URL'), '/');

        return $base . '/' . ltrim($caminho, '/');
    }
}

if (!function_exists('redireccionar')) {
    /**
     * Redirect e termina.
     */
    function redireccionar(string $destino, int $codigo = 302): never
    {
        Response::redirect($destino, $codigo)->enviar();
    }
}

if (!function_exists('voltar')) {
    /**
     * Redirect para o HTTP_REFERER (seguro: só caminhos internos) ou fallback.
     */
    function voltar(string $fallback = '/'): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $caminho = parse_url($ref, PHP_URL_PATH) ?: '';

        // Só redireccionar para caminhos internos (evita open redirect)
        if ($caminho !== '' && str_starts_with($caminho, '/')) {
            redireccionar($caminho);
        }

        redireccionar($fallback);
    }
}

if (!function_exists('autenticado')) {
    function autenticado(): bool
    {
        return Auth::autenticado();
    }
}

if (!function_exists('eAdmin')) {
    function eAdmin(): bool
    {
        return Auth::eAdmin();
    }
}

if (!function_exists('utilizador')) {
    /**
     * @return array<string, mixed>|null
     */
    function utilizador(): ?array
    {
        return Auth::utilizador();
    }
}

if (!function_exists('csrf_campo')) {
    function csrf_campo(): string
    {
        return Csrf::campo();
    }
}

if (!function_exists('erro_de')) {
    /**
     * Mensagem de erro validada de um campo (para templates).
     */
    function erro_de(string $campo): ?string
    {
        $erros = Session::obter('_erros_validacao', null, false);
        if (is_array($erros) && isset($erros[$campo])) {
            return (string) $erros[$campo];
        }

        return null;
    }
}

if (!function_exists('velho')) {
    /**
     * Valor "old" (reenvio do formulário após erro de validação).
     */
    function velho(string $campo, string $padrao = ''): string
    {
        $velhos = Session::obter('_old_input', null, false);
        if (is_array($velhos) && isset($velhos[$campo]) && is_scalar($velhos[$campo])) {
            return (string) $velhos[$campo];
        }

        return $padrao;
    }
}

if (!function_exists('mostrar_flash')) {
    /**
     * Lê e remove uma flash message ('ok' ou 'erro').
     */
    function mostrar_flash(string $chave): ?string
    {
        $valor = Session::lerFlash($chave);

        return is_string($valor) && $valor !== '' ? $valor : null;
    }
}

if (!function_exists('formatar_data')) {
    /**
     * Formata uma data da BD (AAAA-MM-DD) em português.
     */
    function formatar_data(?string $data, string $formato = 'd/m/Y'): string
    {
        if ($data === null || $data === '') {
            return '';
        }
        $ts = strtotime($data);

        return $ts === false ? '' : date($formato, $ts);
    }
}

if (!function_exists('formatar_moeda')) {
    /**
     * Formata Kwanza: 350000 → "350.000 Kz".
     */
    function formatar_moeda(float|int|string|null $valor): string
    {
        if ($valor === null || $valor === '') {
            return '';
        }

        return number_format((float) $valor, 0, ',', '.') . ' Kz';
    }
}

if (!function_exists('novo_uuid')) {
    /** UUID v4 aleatório (36 caracteres) para IDs varchar(36). */
    function novo_uuid(): string
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }
}

if (!function_exists('csrf_validar_ou_negar')) {
    /**
     * Valida o CSRF de um POST; se falhar, responde 419 e sai.
     */
    function csrf_validar_ou_negar(): void
    {
        $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        if (!Csrf::validar(is_string($token) ? $token : null)) {
            Logger::aviso('csrf.token_invalido', ['ip' => $_SERVER['REMOTE_ADDR'] ?? '?']);
            Response::html(View::render('errors/419'), 419)->enviar();
        }
    }
}
