<?php

declare(strict_types=1);

// Autenticação: login, permissões e logout.

// /admin sem sessão → /entrar
$r = Apoio::get('/admin', false);
Apoio::verificar('/admin sem sessão → 302', $r['status'] === 302);
Apoio::verificar('/admin redirecciona para /entrar', Apoio::localizacao($r) === '/entrar');

// Login de admin
Apoio::verificar('login admin (POST /entrar → 302 /admin)', Apoio::loginAdmin());

// /admin com sessão → 200
$r = Apoio::get('/admin');
Apoio::verificar('/admin com sessão → 200', $r['status'] === 200);

// /conta com sessão → 200
$r = Apoio::get('/conta');
Apoio::verificar('/conta com sessão → 200', $r['status'] === 200);

// Logout
$r = Apoio::post('/sair');
Apoio::verificar('POST /sair → 302', $r['status'] === 302);

$r = Apoio::get('/admin');
Apoio::verificar('/admin depois do logout → 302', $r['status'] === 302);

// Login com password errada → volta a /entrar com erro
$r = Apoio::post('/entrar', ['email' => 'admin@havredesign.com', 'password' => 'password-errada']);
Apoio::verificar('login errado → 302 /entrar', $r['status'] === 302 && Apoio::localizacao($r) === '/entrar');
$pagina = Apoio::get('/entrar');
Apoio::verificar('login errado mostra erro', str_contains($pagina['corpo'], 'palavra-passe') || str_contains($pagina['corpo'], 'inválid') || str_contains($pagina['corpo'], 'incorret'));
