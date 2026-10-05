<?php
/**
 * Admin — lista de mensagens de contacto.
 * Espera do controller: $mensagens, $status, $estados, $contagens.
 */
$titulo         = 'Mensagens de contacto - Administração HAVREDESIGN';
$tituloAdmin    = 'Mensagens de contacto';
$subtituloAdmin = 'Mensagens enviadas pelo formulário de contacto do site.';

$badges = [
    'new'     => 'bg-primary/10 text-primary',
    'replied' => 'bg-muted text-muted-foreground',
    'closed'  => 'bg-muted text-muted-foreground',
];

$base = rota('admin.mensagens');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'mensagens']); ?>

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center gap-2">
    <a href="<?= e($base) ?>"
       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors <?= $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' ?>">
      Todas
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
          <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">E-mail</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Assunto</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Mensagem</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if ($mensagens === []): ?>
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem mensagens<?= $status !== '' ? ' com o estado selecionado' : '' ?>.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($mensagens as $mensagem): ?>
            <?php $cor = $badges[$mensagem['status']] ?? 'bg-muted text-muted-foreground'; ?>
            <tr class="hover:bg-muted/50 transition-colors">
              <td class="px-4 py-3 text-muted-foreground whitespace-nowrap"><?= e(formatar_data((string) $mensagem['created_at'], 'd/m/Y H:i')) ?></td>
              <td class="px-4 py-3 font-medium text-foreground"><?= e($mensagem['name']) ?></td>
              <td class="px-4 py-3 text-foreground/80 break-all"><?= e($mensagem['email']) ?></td>
              <td class="px-4 py-3 text-foreground/80"><?= $mensagem['subject'] !== null && $mensagem['subject'] !== '' ? e($mensagem['subject']) : '—' ?></td>
              <td class="px-4 py-3 text-muted-foreground max-w-xs">
                <span class="block truncate"><?= e(mb_strimwidth((string) $mensagem['message'], 0, 70, '…')) ?></span>
              </td>
              <td class="px-4 py-3">
                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($cor) ?>">
                  <?= e($estados[$mensagem['status']] ?? $mensagem['status']) ?>
                </span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="<?= e($base . '/' . rawurlencode($mensagem['id'])) ?>" class="text-sm font-medium text-secondary hover:underline">Ver mensagem</a>
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
