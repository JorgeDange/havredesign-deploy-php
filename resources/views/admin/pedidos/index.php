<?php
/**
 * Admin — lista de pedidos de orçamento.
 * Espera do controller: $pedidos, $status, $estados, $contagens,
 * $anexosPorPedido.
 */
$titulo       = 'Pedidos de orçamento - Administração HAVREDESIGN';
$tituloAdmin  = 'Pedidos de orçamento';
$subtituloAdmin = 'Pedidos de orçamento recebidos pelo formulário do site.';

$badges = [
    'NEW'         => 'bg-primary/10 text-primary',
    'IN_REVIEW'   => 'bg-muted text-muted-foreground',
    'APPROVED'    => 'bg-primary/10 text-primary',
    'IN_PROGRESS' => 'bg-muted text-muted-foreground',
    'COMPLETED'   => 'bg-primary/10 text-primary',
    'REJECTED'    => 'bg-destructive/10 text-destructive',
];

$base = rota('admin.pedidos');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'pedidos']); ?>

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center gap-2">
    <a href="<?= e($base) ?>"
       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors <?= $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' ?>">
      Todos
      <span class="px-1.5 py-0.5 rounded-full <?= $status === '' ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' ?>"><?= (int) $contagens['total'] ?></span>
    </a>

    <?php foreach ($estados as $codigo => $rotulo): ?>
      <?php $ativo = $status === $codigo; ?>
      <a href="<?= e($base . '?status=' . rawurlencode($codigo)) ?>"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors <?= $ativo ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' ?>">
        <?= e($rotulo) ?>
        <span class="px-1.5 py-0.5 rounded-full <?= $ativo ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' ?>"><?= (int) ($contagens[$codigo] ?? 0) ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-muted text-left">
          <th class="px-4 py-3 font-medium text-muted-foreground">Data</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Cliente</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Tipo de projeto</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Localização</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if ($pedidos === []): ?>
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem pedidos<?= $status !== '' ? ' com o estado selecionado' : '' ?>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($pedidos as $pedido): ?>
            <?php
            $cor  = $badges[$pedido['status']] ?? 'bg-muted text-muted-foreground';
            $lote = (int) ($anexosPorPedido[$pedido['id']] ?? 0);
            ?>
            <tr class="hover:bg-muted/50 transition-colors">
              <td class="px-4 py-3 text-muted-foreground whitespace-nowrap"><?= e(formatar_data((string) $pedido['created_at'], 'd/m/Y H:i')) ?></td>
              <td class="px-4 py-3">
                <p class="font-medium text-foreground"><?= e($pedido['user_name']) ?></p>
                <p class="text-xs text-muted-foreground"><?= e($pedido['user_email']) ?></p>
              </td>
              <td class="px-4 py-3 text-foreground/80"><?= e($pedido['project_type']) ?></td>
              <td class="px-4 py-3 text-muted-foreground"><?= $pedido['location'] !== null && $pedido['location'] !== '' ? e($pedido['location']) : '—' ?></td>
              <td class="px-4 py-3">
                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($cor) ?>">
                  <?= e($estados[$pedido['status']] ?? $pedido['status']) ?>
                </span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="<?= e($base . '/' . rawurlencode($pedido['id'])) ?>" class="text-sm font-medium text-secondary hover:underline">Ver detalhe</a>
                <?php if ($lote > 0): ?>
                  <span class="ml-2 text-xs text-muted-foreground"><?= $lote ?> anexo(s)</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= \App\Core\View::parcial('admin/_rodape'); ?>
<?php \App\Core\View::secaoFim(); ?>
