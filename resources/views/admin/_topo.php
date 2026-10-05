<?php
/**
 * Partial — topo do painel de administração.
 * Variáveis: $titulo (obrigatório), $subtitulo (opcional), $aba (opcional)
 */
$prefixo = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');
$aba = $aba ?? '';
$subtitulo = $subtitulo ?? '';

$abas = [
    ['',            'Painel'],
    ['servicos',    'Serviços'],
    ['portfolio',   'Portefólio'],
    ['solucoes',    'Soluções'],
    ['pedidos',     'Pedidos'],
    ['agendamentos', 'Agendamentos'],
    ['mensagens',   'Mensagens'],
    ['testemunhos', 'Testemunhos'],
    ['definicoes',  'Definições'],
    ['utilizadores', 'Utilizadores'],
];

$erros = \App\Core\Session::obter('_erros_validacao', null, false);
?>
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <p class="text-sm font-medium text-secondary tracking-wide uppercase">Administração</p>
      <h1 class="text-3xl font-bold text-foreground mt-1"><?= e($titulo ?? 'Painel') ?></h1>
      <?php if ($subtitulo !== ''): ?>
        <p class="text-foreground/70 mt-1"><?= e($subtitulo) ?></p>
      <?php endif; ?>
    </div>
    <a href="<?= e(rota('home')) ?>" class="px-4 py-2 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
      Ver site ↗
    </a>
  </div>

  <?php $flashOk = mostrar_flash('ok'); if ($flashOk !== null && $flashOk !== ''): ?>
    <div class="mt-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary">
      <?= e($flashOk) ?>
    </div>
  <?php endif; ?>

  <?php $flashErro = mostrar_flash('erro'); if ($flashErro !== null && $flashErro !== ''): ?>
    <div class="mt-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
      <?= e($flashErro) ?>
    </div>
  <?php endif; ?>

  <?php if (is_array($erros) && $erros !== []): ?>
    <div class="mt-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive">
      <p class="font-medium">Corrija os seguintes campos:</p>
      <ul class="list-disc list-inside mt-1">
        <?php foreach ($erros as $erro): ?>
          <li><?= e($erro) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <nav class="mt-6 border-b border-border flex gap-1 overflow-x-auto" aria-label="Secções do painel">
    <?php foreach ($abas as [$caminho, $rotulo]): ?>
      <a href="<?= e($caminho === '' ? $prefixo : $prefixo . '/' . $caminho) ?>"
         class="shrink-0 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors <?= $aba === $caminho ? 'border-secondary text-foreground' : 'border-transparent text-foreground/60 hover:text-foreground' ?>">
        <?= e($rotulo) ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <div class="mt-8">
