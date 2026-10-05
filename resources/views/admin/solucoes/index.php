<?php
/**
 * Soluções (admin) — lista das soluções comerciais.
 * Espera do controller: $solucoes.
 */
$titulo = 'Soluções - Administração HAVREDESIGN';

$prefixo      = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');
$rotaSolucoes = $prefixo . '/solucoes';
$total        = count($solucoes);

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', [
    'titulo'    => 'Soluções',
    'subtitulo' => 'Gestão das soluções comerciais apresentadas no site.',
    'aba'       => 'solucoes',
]) ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground"><?= $total ?> solução(ões) registada(s)</p>
  <a href="<?= e($rotaSolucoes . '/nova') ?>" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Nova solução
  </a>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm min-w-[720px]">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Código</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Descrição</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      <?php if ($total === 0): ?>
        <tr>
          <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
            Ainda não existem soluções. Use «Nova solução» para criar a primeira.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($solucoes as $solucao): ?>
          <?php
            $ativa      = (int) ($solucao['active'] ?? 0) === 1;
            $urlEditar  = $rotaSolucoes . '/' . rawurlencode((string) $solucao['id']) . '/editar';
            $urlApagar  = $rotaSolucoes . '/' . rawurlencode((string) $solucao['id']) . '/apagar';
          ?>
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                <?= e($solucao['code']) ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <p class="font-medium text-foreground"><?= e($solucao['name']) ?></p>
              <p class="text-xs text-muted-foreground"><?= e($solucao['slug']) ?></p>
            </td>
            <td class="px-4 py-3 text-muted-foreground max-w-xs">
              <?= e(mb_strimwidth((string) $solucao['description'], 0, 90, '…')) ?>
            </td>
            <td class="px-4 py-3 text-muted-foreground"><?= (int) ($solucao['sort_order'] ?? 0) ?></td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= $ativa ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                <?= $ativa ? 'Ativa' : 'Inativa' ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                <a href="<?= e($urlEditar) ?>"
                   class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Editar
                </a>
                <form method="POST" action="<?= e($urlApagar) ?>" class="inline"
                      onsubmit="return confirm('Apagar a solução «<?= e($solucao['name']) ?>»? Esta ação não pode ser desfeita.');">
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
