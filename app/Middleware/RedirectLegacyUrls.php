<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/**
 * RedirectLegacyUrls — 301 de URLs antigas (tabela `redirects`).
 * Corre ANTES do router (apanha caminhos que dariam 404).
 */
final class RedirectLegacyUrls
{
    public static function processar(): void
    {
        $metodo = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Só para navegação (GET/HEAD) — nunca redireccionar submissões
        if (!in_array($metodo, ['GET', 'HEAD'], true)) {
            return;
        }

        $caminho = Request::normalizarCaminho($_SERVER['REQUEST_URI'] ?? '/');
        if ($caminho === '/') {
            return;
        }

        try {
            $redirect = Database::um(
                'SELECT target_path FROM redirects WHERE source_path = ? AND active = 1 LIMIT 1',
                [$caminho]
            );
        } catch (\Throwable) {
            return; // BD indisponível → deixa o router tratar (404)
        }

        if ($redirect !== null && isset($redirect['target_path'])) {
            $destino = (string) $redirect['target_path'];

            // Nunca redireccionar para fora do domínio (proteção anti-open-redirect)
            if (str_starts_with($destino, '/') && !str_starts_with($destino, '//')) {
                Response::redirecionarPermanente($destino)->enviar();
            }
        }
    }
}
