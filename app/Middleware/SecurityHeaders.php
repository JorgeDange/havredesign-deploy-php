<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Config;

/**
 * SecurityHeaders — headers de segurança em TODAS as respostas.
 */
final class SecurityHeaders
{
    public static function enviar(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header_remove('X-Powered-By');

        // CSP — restringe origens de scripts/estilos/imagens
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "frame-src https://www.google.com https://www.youtube.com",
            "form-action 'self'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
        ]);
        header("Content-Security-Policy: {$csp}");

        // HSTS só em produção (HTTPS)
        if (Config::eProducao()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
