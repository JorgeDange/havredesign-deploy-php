<?php

declare(strict_types=1);

// Painel admin: todas as rotas do CMS respondem 200 com sessão de admin.

if (!Apoio::loginAdmin()) {
    Apoio::verificar('login admin para suite admin', false);
    return;
}

$rotas = [
    '/admin'                              => 200,
    '/admin/servicos'                     => 200,
    '/admin/servicos/novo'                => 200,
    '/admin/portfolio'                    => 200,
    '/admin/portfolio/novo'               => 200,
    '/admin/solucoes'                     => 200,
    '/admin/solucoes/nova'                => 200,
    '/admin/pedidos'                      => 200,
    '/admin/agendamentos'                 => 200,
    '/admin/mensagens'                    => 200,
    '/admin/testemunhos'                  => 200,
    '/admin/definicoes'                   => 200,
    '/admin/utilizadores'                 => 200,
];

foreach ($rotas as $rota => $esperado) {
    $r = Apoio::get($rota);
    Apoio::verificar("GET {$rota} → {$esperado}", $r['status'] === $esperado);
    if ($esperado === 200 && $r['status'] === 200) {
        Apoio::verificar("{$rota} tem topo admin", str_contains($r['corpo'], 'admin-barra') || str_contains($r['corpo'], 'HAVREDESIGN'));
    }
}

// Edição de registo existente (testemunho real da BD)
$idT = Apoio::bd()->query('SELECT id FROM testimonials LIMIT 1')->fetchColumn();
if ($idT) {
    $r = Apoio::get('/admin/testemunhos/' . $idT . '/editar');
    Apoio::verificar('GET /admin/testemunhos/{id}/editar → 200', $r['status'] === 200);
}

// Detalhe de registo existente (pedido real da BD)
$id = Apoio::bd()->query('SELECT id FROM project_requests ORDER BY created_at DESC LIMIT 1')->fetchColumn();
if ($id) {
    $r = Apoio::get('/admin/pedidos/' . $id);
    Apoio::verificar('GET /admin/pedidos/{id} → 200', $r['status'] === 200);
}

// Alterar estado de um pedido (POST) e verificar persistência
if ($id) {
    $r = Apoio::post('/admin/pedidos/' . $id, ['status' => 'in_review']);
    Apoio::verificar('POST estado do pedido → 302', $r['status'] === 302);
    $estado = Apoio::bd()->query('SELECT status FROM project_requests WHERE id = ' . Apoio::bd()->quote((string) $id))->fetchColumn();
    Apoio::verificar('estado persistido (in_review)', $estado === 'in_review');
}
