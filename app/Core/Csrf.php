<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Csrf — token por sessão, validação com hash_equals().
 */
final class Csrf
{
    private const CHAVE = '_csrf_token';

    /**
     * Devolve (e cria se necessário) o token da sessão actual.
     */
    public static function token(): string
    {
        $token = Session::obter(self::CHAVE);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::colocar(self::CHAVE, $token);
        }

        return $token;
    }

    /**
     * Campo oculto para os formulários: <input type="hidden" ...>
     */
    public static function campo(): string
    {
        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Valida o token enviado no POST. Comparação em tempo constante.
     */
    public static function validar(?string $token): bool
    {
        $esperado = Session::obter(self::CHAVE);
        if (!is_string($esperado) || !is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($esperado, $token);
    }

    /**
     * Rotação do token (após login, por segurança).
     */
    public static function rotacionar(): void
    {
        Session::colocar(self::CHAVE, bin2hex(random_bytes(32)));
    }
}
