<?php

declare(strict_types=1);

// Suites de testes — cada ficheiro é require'do por tests/executar.php
// e usa a classe Apoio (asserções + HTTP).

$rotas = [
    '/'                            => 200,
    '/sobre'                       => 200,
    '/servicos'                    => 200,
    '/havre-solucoes'              => 200,
    '/portfolio'                   => 200,
    '/processo'                    => 200,
    '/faq'                         => 200,
    '/contacto'                    => 200,
    '/solicitar-projeto'           => 200,
    '/agendar'                     => 200,
    '/networking'                  => 200,
    '/politica-de-privacidade'     => 200,
    '/termos-de-uso'               => 200,
    '/entrar'                      => 200,
    '/registar'                    => 200,
    '/recuperar-palavra-passe'     => 200,
    '/redefinir-palavra-passe/abc' => 200,
    '/robots.txt'                  => 200,
    '/sitemap.xml'                 => 200,
    '/agendar/disponibilidade?mes=2026-12' => 200,
    '/pagina-que-nao-existe'       => 404,
    '/conta'                       => 302, // exige sessão
];

foreach ($rotas as $rota => $esperado) {
    $r = Apoio::get($rota);
    Apoio::verificar("GET {$rota} → {$esperado}", $r['status'] === $esperado);
}

// URL legada: /orcamentos redirecciona 301 para /havre-solucoes
$r = Apoio::get('/orcamentos');
Apoio::verificar(
    '301 /orcamentos → /havre-solucoes',
    $r['status'] === 301 && str_contains(Apoio::localizacao($r), '/havre-solucoes')
);

// Conteúdo: título e sem erros 500 no HTML
$r = Apoio::get('/servicos');
Apoio::verificar('/servicos tem <title>', str_contains($r['corpo'], '<title>'));
Apoio::verificar('/servicos lista serviços', str_contains($r['corpo'], 'Servi'));

// Portefólio com detalhe real (slug da BD)
$slug = Apoio::bd()->query("SELECT slug FROM portfolio_items WHERE status='published' AND slug IS NOT NULL LIMIT 1")->fetchColumn();
if ($slug) {
    $r = Apoio::get('/portfolio/' . $slug);
    Apoio::verificar("GET /portfolio/{slug} → 200", $r['status'] === 200);
}
