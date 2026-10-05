<?php

declare(strict_types=1);

// Segurança: CSRF, headers, uploads, path traversal, 301 legacy, 405.

// ------------------------------------------------------------------
// CSRF
// ------------------------------------------------------------------
$r = Apoio::post('/contacto', ['name' => 'x', 'email' => 'x@x.ao', 'subject' => 'x', 'message' => 'x'], false);
// sem sessão/cookies → sem token → 419
Apoio::verificar('POST sem token → 419', $r['status'] === 419);

// POST com token falso (sessão iniciada)
Apoio::loginAdmin();
$r = Apoio::pedir('POST', '/contacto', [
    '_token'  => 'token-invalido',
    'name'    => 'x',
    'email'   => 'x@x.ao',
    'subject' => 'x',
    'message' => 'x',
], true);
Apoio::verificar('POST token inválido → 419', $r['status'] === 419);

// ------------------------------------------------------------------
// Métodos e rotas inexistentes
// ------------------------------------------------------------------
$ch = curl_init(Apoio::$base . '/contacto');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_CUSTOMREQUEST => 'DELETE']);
$bruto = (string) curl_exec($ch);
curl_close($ch);
preg_match('#HTTP/[\d.]+ (\d{3})#', $bruto, $m);
Apoio::verificar('DELETE /contacto → 405', (int) ($m[1] ?? 0) === 405);

$r = Apoio::get('/rota-impossivel');
Apoio::verificar('/rota-impossivel → 404', $r['status'] === 404);

// ------------------------------------------------------------------
// Headers de segurança
// ------------------------------------------------------------------
$r = Apoio::get('/', false);
Apoio::verificar('X-Content-Type-Options: nosniff', str_contains($r['headers'], 'X-Content-Type-Options: nosniff'));
Apoio::verificar('X-Frame-Options: SAMEORIGIN', stripos($r['headers'], 'X-Frame-Options') !== false);
Apoio::verificar('Referrer-Policy presente', stripos($r['headers'], 'Referrer-Policy') !== false);
Apoio::verificar('sem stack trace no HTML 404', !str_contains($r['corpo'], 'Stack trace'));

// Página de erro 500 simulada não expõe caminhos internos
$r = Apoio::get('/?teste=1', false);
Apoio::verificar('HTML livre de "C:\\"', !str_contains($r['corpo'], 'C:\\'));

// ------------------------------------------------------------------
// Path traversal e uploads
// ------------------------------------------------------------------
$r = Apoio::get('/../../.env');
Apoio::verificar('path traversal → 404/403', in_array($r['status'], [403, 404], true));

$r = Apoio::get('/.env');
Apoio::verificar('/.env público → 404', $r['status'] === 404);

// .htaccess a bloquear ficheiros sensíveis em public/
foreach (['/index.php', '/storage/logs/php-error.log'] as $protegido) {
    $c = curl_init(Apoio::$base . $protegido);
    curl_setopt_array($c, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false]);
    $b = (string) curl_exec($c);
    curl_close($c);
    preg_match('#HTTP/[\d.]+ (\d{3})#', $b, $mm);
    $estado = (int) ($mm[1] ?? 0);
    Apoio::verificar("{$protegido} não servido directamente ({$estado})", in_array($estado, [403, 404, 405], true));
}

// Upload de .php rejeitado no pedido de projeto
$pagina = Apoio::get('/solicitar-projeto');
preg_match('/name="_token" value="([^"]+)"/', $pagina['corpo'], $tm);
$phpFake = tempnam(sys_get_temp_dir(), 'p') . '.php';
file_put_contents($phpFake, '<?php echo "malicioso";');

$ck = Apoio::cookies();
$ch = curl_init(Apoio::$base . '/solicitar-projeto');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_COOKIEJAR      => $ck,
    CURLOPT_COOKIEFILE     => $ck,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => [
        '_token'            => $tm[1] ?? '',
        'user_name'         => 'Atacante',
        'user_email'        => 'ataque@exemplo.ao',
        'user_phone'        => '999000111',
        'project_type'      => 'Casa',
        'location'          => 'Luanda',
        'preferred_channel' => 'email',
        'description'       => 'teste',
        'privacy'           => 'on',
        'projectFiles[]'    => new CURLFile($phpFake, 'application/x-php', 'shell.php'),
    ],
]);
$bruto = (string) curl_exec($ch);
curl_close($ch);
unlink($phpFake);

$existe = false;
foreach (glob(dirname(__DIR__, 2) . '/storage/app/private/requests/*/shell.php') as $f) {
    $existe = $existe || is_file($f);
}
Apoio::verificar('upload .php rejeitado (não guardado)', !$existe);

// ------------------------------------------------------------------
// 301 legacy (tabela redirects)
// ------------------------------------------------------------------
$legacy = Apoio::bd()->query('SELECT source_path, target_path FROM redirects WHERE active = 1 LIMIT 3');
$testados = 0;
foreach ($legacy as $linha) {
    $r = Apoio::get($linha['source_path']);
    Apoio::verificar("301 {$linha['source_path']} → {$linha['target_path']}",
        $r['status'] === 301 && Apoio::localizacao($r) === $linha['target_path']);
    $testados++;
}
Apoio::verificar('há redirects legacy para testar', $testados > 0);

// ------------------------------------------------------------------
// HEAD (health checks)
// ------------------------------------------------------------------
$r = Apoio::pedir('HEAD', '/', null, false);
Apoio::verificar('HEAD / → 200', $r['status'] === 200);
