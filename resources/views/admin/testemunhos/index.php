<?php
/**
 * Painel — lista de testemunhos (aprovação e ordenação).
 * Espera do controller: $testemunhos (lista de arrays).
 */
$titulo       = 'Testemunhos - Administração HAVREDESIGN';
$tituloAdmin  = 'Testemunhos';
$subtituloAdmin = 'Aprovação e ordenação dos testemunhos apresentados no site.';
$total        = count($testemunhos);
$rotaBase     = rota('admin.testemunhos');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?php $flashOk = mostrar_flash('ok'); $flashErro = mostrar_flash('erro'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'testemunhos']) ?>

<?php if (is_string($flashOk) && $flashOk !== ''): ?>
  <div class="mb-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary"><?= e($flashOk) ?></div>
<?php endif; ?>
<?php if (is_string($flashErro) && $flashErro !== ''): ?>
  <div class="mb-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?= e($flashErro) ?></div>
<?php endif; ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground"><?= (int) $total ?> testemunho(s) registado(s)</p>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Testemunho</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Acções</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      <?php if ($testemunhos === []): ?>
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-foreground">
            Ainda não existem testemunhos.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($testemunhos as $testemunho): ?>
          <?php
            $idTest   = (string) $testemunho['id'];
            $publicado = ((string) $testemunho['status']) === 'published';
            $editar   = $rotaBase . '/' . rawurlencode($idTest) . '/editar';
          ?>
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3">
              <p class="font-medium text-foreground"><?= e($testemunho['name']) ?></p>
              <?php if (!empty($testemunho['role'])): ?>
                <p class="text-xs text-muted-foreground"><?= e($testemunho['role']) ?></p>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-muted-foreground max-w-md">
              <?= e(mb_strimwidth((string) $testemunho['content'], 0, 120, '…')) ?>
            </td>
            <td class="px-4 py-3 text-muted-foreground"><?= (int) $testemunho['sort_order'] ?></td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= $publicado ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                <?= e($publicado ? 'Publicado' : 'Oculto') ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                <form method="POST" action="<?= e($rotaBase . '/' . rawurlencode($idTest) . '/alternar') ?>" class="inline">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                    Alternar
                  </button>
                </form>
                <a href="<?= e($editar) ?>"
                   class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Editar
                </a>
                <form method="POST" action="<?= e($rotaBase . '/' . rawurlencode($idTest) . '/apagar') ?>" class="inline"
                      data-confirm="Apagar o testemunho de «<?= e($testemunho['name']) ?>»? Esta acção não pode ser desfeita." data-confirm-acao="Apagar">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                    Apagar
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
