<?php

/**
 * Router de DESENVOLVIMENTO — para `php -S 127.0.0.1:8090 router.php`.
 * Em produção usa-se Apache + .htaccess (public/.htaccess).
 *
 * Uso: php -S 127.0.0.1:8090 router.php
 */

declare(strict_types=1);

// Ler APP_URL do .env para determinar o path base (ex.: /havredesign/public)
$appUrl = 'http://localhost/havredesign/public';
$envPath = __DIR__ . '/.env';
if (is_file($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        if (str_starts_with(trim($linha), 'APP_URL=')) {
            $appUrl = substr($linha, 8);
            break;
        }
    }
}
$basePath = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '/', '/'); // ex.: /havredesign/public

// Servir ficheiros estáticos directamente (css, js, imagens, etc.)
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Remover o path base do pedido se presente (para assets gerados com asset())
if ($basePath !== '' && $basePath !== '/' && str_starts_with($caminho, $basePath)) {
    $caminho = substr($caminho, strlen($basePath));
    if ($caminho === '') $caminho = '/';
}

// Bloquear ficheiros sensíveis mesmo em desenvolvimento
$bloqueados = ['/index.php', '/.htaccess', '/.env', '/.env.example', '/.gitignore', '/composer.json', '/composer.lock', '/phpunit.xml', '/artisan'];
if (in_array($caminho, $bloqueados, true)) {
    http_response_code(404);
    exit('Not Found');
}

$ficheiro = __DIR__ . '/public' . $caminho;

if ($caminho !== '/' && is_file($ficheiro)) {
    // Servir o ficheiro com o MIME type correcto
    $ext = strtolower(pathinfo($ficheiro, PATHINFO_EXTENSION));
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'txt'  => 'text/plain',
        'pdf'  => 'application/pdf',
    ];
    $tipo = $mimes[$ext] ?? 'application/octet-stream';
    header("Content-Type: $tipo");
    header('Content-Length: ' . filesize($ficheiro));
    readfile($ficheiro);
    exit;
}

// Todo o resto → front controller
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/public';

require __DIR__ . '/public/index.php';
