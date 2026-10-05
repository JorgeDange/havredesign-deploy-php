<?php
/**
 * Header — navegação principal (equivalente a partials/header.blade.php).
 */
$site       = \App\Support\Site::site();
$utilizador = utilizador();
$caminhoActual = (new \App\Core\Request())->caminho();

$navLinks = [
    ['label' => 'Início',      'href' => rota('home'),        'ativo' => $caminhoActual === '/'],
    ['label' => 'Sobre',       'href' => rota('sobre'),       'ativo' => $caminhoActual === '/sobre'],
    ['label' => 'Serviços',    'href' => rota('servicos'),    'ativo' => str_starts_with($caminhoActual, '/servicos')],
    ['label' => 'HAVRE Soluções', 'href' => rota('havre-solucoes'), 'ativo' => str_starts_with($caminhoActual, '/havre-solucoes')],
    ['label' => 'Portefólio',  'href' => rota('portfolio'),   'ativo' => str_starts_with($caminhoActual, '/portfolio')],
    ['label' => 'Processo',    'href' => rota('processo'),    'ativo' => $caminhoActual === '/processo'],
    ['label' => 'Contacto',    'href' => rota('contacto'),    'ativo' => $caminhoActual === '/contacto'],
    ['label' => 'Networking',  'href' => rota('networking'),  'ativo' => $caminhoActual === '/networking'],
];
?>
<header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 transition-all duration-300">
  <div class="container mx-auto flex h-20 items-center justify-between px-4">
    <a href="<?= rota('home') ?>" class="flex items-center gap-2 hover:opacity-90 transition-opacity">
      <img src="<?= e(\App\Support\Site::imagem('logo.png', 'LOGO-HAVREDESIGN.jpeg')) ?>"
           alt="Logo HavreDesign" width="120" height="60" class="h-10 w-auto">
    </a>

    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center gap-8">
      <?php foreach ($navLinks as $link): ?>
        <a href="<?= e($link['href']) ?>"
           class="text-sm font-medium <?= $link['ativo'] ? 'text-secondary font-semibold border-b-2 border-secondary pb-1' : 'text-foreground/80 hover:text-primary' ?> transition-colors">
          <?= e($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="hidden lg:flex items-center gap-4">
      <?php if ($utilizador !== null): ?>
        <div class="relative group">
          <button class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-muted text-sm font-medium text-foreground transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span><?= e($utilizador['name']) ?></span>
          </button>
          <div class="absolute right-0 top-full hidden group-hover:block bg-card border border-border shadow-lg rounded-md w-48 py-1 z-50 animate-fade-in-up">
            <a href="<?= rota('conta') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted">Minha Área</a>
            <?php if (eAdmin()): ?>
              <a href="<?= rota('admin') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted font-semibold text-secondary">Painel Admin</a>
            <?php endif; ?>
            <hr class="border-border my-1">
            <form method="POST" action="<?= rota('sair') ?>">
              <?= csrf_campo() ?>
              <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-muted">Sair</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= rota('entrar') ?>" class="px-4 py-2 text-sm font-medium text-foreground hover:text-primary transition-colors">
          Entrar
        </a>
      <?php endif; ?>
      <a href="<?= rota('agendar') ?>" class="px-5 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors shadow-sm hover:shadow">
        Iniciar Projeto
      </a>
    </div>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden p-3 -mr-2 text-foreground" aria-label="Toggle Menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>

  <!-- Mobile Navigation Drawer -->
  <div id="mobile-menu" class="hidden lg:hidden border-t border-border bg-background px-4 py-4 space-y-3 animate-fade-in">
    <?php foreach ($navLinks as $link): ?>
      <a href="<?= e($link['href']) ?>" class="block text-sm font-medium py-2 <?= $link['ativo'] ? 'text-secondary font-bold' : 'text-foreground/80' ?>">
        <?= e($link['label']) ?>
      </a>
    <?php endforeach; ?>
    <div class="pt-4 border-t border-border space-y-2">
      <?php if ($utilizador !== null): ?>
        <a href="<?= rota('conta') ?>" class="block w-full text-center py-2 px-4 border border-border rounded-md text-sm font-medium">Minha Área</a>
        <?php if (eAdmin()): ?>
          <a href="<?= rota('admin') ?>" class="block w-full text-center py-2 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium">Painel Admin</a>
        <?php endif; ?>
        <form method="POST" action="<?= rota('sair') ?>">
          <?= csrf_campo() ?>
          <button type="submit" class="block w-full text-center py-2 text-sm text-red-600">Sair</button>
        </form>
      <?php else: ?>
        <a href="<?= rota('entrar') ?>" class="block w-full text-center py-2 px-4 border border-border rounded-md text-sm font-medium">Entrar</a>
      <?php endif; ?>
      <a href="<?= rota('agendar') ?>" class="block w-full text-center py-2 px-4 bg-secondary text-secondary-foreground rounded-md text-sm font-medium">
        Iniciar Projeto
      </a>
    </div>
  </div>
</header>

<script>
  (function () {
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    if (btn && menu) {
      btn.addEventListener('click', function () { menu.classList.toggle('hidden'); });
    }
  })();
</script>
