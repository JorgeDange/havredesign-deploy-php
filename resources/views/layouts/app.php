<?php
/**
 * Layout base — equivalente a layouts/app.blade.php.
 * Secções: content (obrigatória), head, scripts; variáveis $titulo e $meta.
 */
$site   = \App\Support\Site::site();
$titulo = $titulo ?? trim(($site['brand.name'] ?? 'HAVREDESIGN') . ' - Arquitetura como Refúgio');
$meta   = $meta ?? ($meta_description ?? 'HAVREDESIGN — atelier de arquitetura e design em Luanda. Projeto arquitetónico, design de interiores e fiscalização. Arquitetura como refúgio, excelência em cada detalhe.');
$utilizador = utilizador();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
  <title><?= e($titulo) ?></title>
  <meta name="description" content="<?= e($meta) ?>">
  <meta property="og:title" content="<?= e($titulo) ?>">
  <meta property="og:description" content="<?= e($meta) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e(\App\Core\Config::obter('APP_URL')) . e($_SERVER['REQUEST_URI'] ?? '/') ?>">

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            background: '#F8F4ED',
            foreground: '#0F253F',
            card: { DEFAULT: '#FFFFFF', foreground: '#0F253F' },
            popover: { DEFAULT: '#FFFFFF', foreground: '#0F253F' },
            primary: { DEFAULT: '#0F253F', foreground: '#FFFFFF' },
            secondary: { DEFAULT: '#C9B29E', foreground: '#0F253F' },
            muted: { DEFAULT: '#EFECE6', foreground: '#6B7280' },
            accent: { DEFAULT: '#C9B29E', foreground: '#0F253F' },
            destructive: { DEFAULT: '#EF4444', foreground: '#FFFFFF' },
            border: '#E5E0D8',
            input: '#E5E0D8',
            ring: '#C9B29E'
          }
        }
      }
    };
  </script>

  <link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>?v=3">
  <link rel="stylesheet" href="<?= asset('css/styles.css') ?>?v=3">
  <link rel="icon" href="<?= asset('favicon.png') ?>" sizes="any">
  <link rel="icon" href="<?= asset('icon.png') ?>" type="image/png">
  <link rel="apple-touch-icon" href="<?= asset('apple-icon.png') ?>">
  <?php \App\Core\View::sec('head'); ?>
</head>
<body class="min-h-screen bg-background text-foreground">

  <!-- Ecrã de loading -->
  <div id="page-loader" class="page-loader" role="status" aria-live="polite">
    <img src="<?= e(\App\Support\Site::imagem('logo.png', 'LOGO-HAVREDESIGN.jpeg')) ?>"
         alt="<?= e($site['brand.name'] ?? 'HAVREDESIGN') ?>"
         class="page-loader__logo" width="220" height="110">
    <div class="page-loader__track" aria-hidden="true"><span class="page-loader__bar"></span></div>
    <span class="sr-only">A carregar…</span>
  </div>

  <?= \App\Core\View::parcial('partials/header') ?>

  <main>
    <?php \App\Core\View::sec('content'); ?>
  </main>

  <?= \App\Core\View::parcial('partials/footer') ?>
  <?= \App\Core\View::parcial('partials/whatsapp') ?>

  <script src="<?= asset('js/app.js') ?>?v=3" defer></script>
  <?php \App\Core\View::sec('scripts'); ?>
</body>
</html>
