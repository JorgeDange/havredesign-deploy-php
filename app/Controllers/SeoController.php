<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/**
 * SeoController — robots.txt e sitemap.xml gerados da configuração e da BD.
 *
 * Ambos são rotas (não ficheiros estáticos): o domínio canónico vem de
 * APP_URL e o sitemap segue o portefólio publicado sem cópias manuais.
 */
final class SeoController
{
    /**
     * Páginas públicas estáticas e o seu peso de indexação.
     *
     * @var array<string, array{priority: string, changefreq: string}>
     */
    private const PAGINAS = [
        '/'                          => ['priority' => '1.0', 'changefreq' => 'weekly'],
        '/sobre'                     => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/servicos'                  => ['priority' => '0.9', 'changefreq' => 'monthly'],
        '/havre-solucoes'            => ['priority' => '0.9', 'changefreq' => 'monthly'],
        '/portfolio'                 => ['priority' => '0.9', 'changefreq' => 'weekly'],
        '/processo'                  => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/faq'                       => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/contacto'                  => ['priority' => '0.8', 'changefreq' => 'yearly'],
        '/solicitar-projeto'         => ['priority' => '0.9', 'changefreq' => 'yearly'],
        '/agendar'                   => ['priority' => '0.8', 'changefreq' => 'yearly'],
        '/politica-de-privacidade'   => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/termos-de-uso'             => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/networking'                => ['priority' => '0.5', 'changefreq' => 'yearly'],
    ];

    /** Áreas privadas — nunca devem ser indexadas. */
    private const PROTEGIDAS = [
        '/conta', '/entrar', '/registar',
        '/recuperar-palavra-passe', '/redefinir-palavra-passe',
    ];

    public function robots(Request $request): Response
    {
        $base = self::base();

        $linhas = ['User-agent: *', 'Allow: /'];

        foreach (self::protegidas() as $path) {
            $linhas[] = 'Disallow: ' . $path;
        }

        $linhas[] = '';
        $linhas[] = 'Sitemap: ' . $base . '/sitemap.xml';

        return Response::txt(implode("\n", $linhas) . "\n");
    }

    /**
     * Caminhos a não indexar, com o painel no topo (caminho configurável).
     *
     * @return array<int, string>
     */
    private function protegidas(): array
    {
        return array_merge(['/' . Config::obter('ADMIN_PATH', 'admin')], self::PROTEGIDAS);
    }

    public function sitemap(Request $request): Response
    {
        $base = self::base();
        $hoje = date('Y-m-d');
        $urls = [];

        foreach (self::PAGINAS as $path => $meta) {
            $urls[] = [
                'loc'       => $base . $path,
                'lastmod'   => $hoje,
                'changefreq' => $meta['changefreq'],
                'priority'  => $meta['priority'],
            ];
        }

        // Só portefólio publicado: rascunhos ficam fora do sitemap (não do site).
        $projectos = Database::todos(
            'SELECT slug, updated_at FROM portfolio_items WHERE status = \'published\' AND slug IS NOT NULL ORDER BY slug'
        );

        foreach ($projectos as $projeto) {
            $urls[] = [
                'loc'       => $base . '/portfolio/' . $projeto['slug'],
                'lastmod'   => date('Y-m-d', strtotime((string) $projeto['updated_at'])) ?: $hoje,
                'changefreq' => 'monthly',
                'priority'  => '0.6',
            ];
        }

        $linhas = ['<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $linhas[] = '  <url>';
            foreach ($url as $tag => $valor) {
                $linhas[] = '    <' . $tag . '>' . self::esc($valor) . '</' . $tag . '>';
            }
            $linhas[] = '  </url>';
        }

        $linhas[] = '</urlset>';

        return Response::xml(implode("\n", $linhas) . "\n");
    }

    private static function base(): string
    {
        return rtrim((string) Config::obter('APP_URL', ''), '/');
    }

    private static function esc(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
