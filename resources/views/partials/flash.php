<?php
/**
 * Partial — mensagens flash (ok/erro) no topo da página.
 * Uso: <?= \App\Core\View::parcial('partials/flash') ?>
 */
$flashOk   = mostrar_flash('ok');
$flashErro = mostrar_flash('erro');
?>
<?php if ($flashOk !== null): ?>
  <div class="mb-4 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary"><?= e($flashOk) ?></div>
<?php endif; ?>
<?php if ($flashErro !== null): ?>
  <div class="mb-4 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?= e($flashErro) ?></div>
<?php endif; ?>
