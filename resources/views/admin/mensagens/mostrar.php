<?php
/**
 * Admin — detalhe de uma mensagem de contacto.
 * Espera do controller: $mensagem, $estados.
 */
$titulo         = 'Mensagem de contacto - Administração HAVREDESIGN';
$tituloAdmin    = 'Mensagem de contacto';
$subtituloAdmin = 'Recebida em ' . formatar_data((string) $mensagem['created_at'], 'd/m/Y H:i') . '.';

$badges = [
    'new'     => 'bg-primary/10 text-primary',
    'replied' => 'bg-muted text-muted-foreground',
    'closed'  => 'bg-muted text-muted-foreground',
];

$existe = static fn (mixed $v): bool => $v !== null && trim((string) $v) !== '';
$outra  = static fn (mixed $v, string $padrao = '—'): string => $existe($v) ? (string) $v : $padrao;
$quando = static fn (mixed $v): string => $existe($v) ? formatar_data((string) $v, 'd/m/Y H:i') : '—';

$base   = rota('admin.mensagens');
$rotaUm = $base . '/' . rawurlencode((string) $mensagem['id']);

$statusActual = (string) (velho('status', (string) $mensagem['status']) ?: $mensagem['status']);

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'mensagens']); ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="<?= e($base) ?>" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($badges[$mensagem['status']] ?? 'bg-muted text-muted-foreground') ?>">
    <?= e($estados[$mensagem['status']] ?? $mensagem['status']) ?>
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <div class="flex flex-wrap items-start justify-between gap-3 border-b border-border pb-4">
        <div>
          <h2 class="text-lg font-semibold text-foreground"><?= $existe($mensagem['subject']) ? e($mensagem['subject']) : 'Sem assunto' ?></h2>
          <p class="text-sm text-muted-foreground mt-1">
            <?= e($mensagem['name']) ?> · <?= e(formatar_data((string) $mensagem['created_at'], 'd/m/Y H:i')) ?>
          </p>
        </div>
        <a href="mailto:<?= e($mensagem['email']) ?>" class="text-sm font-medium text-secondary hover:underline">
          Responder por e-mail
        </a>
      </div>

      <p class="text-sm text-foreground/80 mt-4 whitespace-pre-wrap"><?= e($mensagem['message']) ?></p>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Remetente</h2>
      <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Nome</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5"><?= e($mensagem['name']) ?></dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5 break-all"><?= e($mensagem['email']) ?></dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($mensagem['phone'])) ?></dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">IP</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($mensagem['ip_address'])) ?></dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Consentimento RGPD</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5"><?= e($quando($mensagem['privacy_consented_at'])) ?></dd>
        </div>
      </dl>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Estado da mensagem</h2>
      <p class="text-xs text-muted-foreground mb-4">Marque como respondida depois de responder ao cliente.</p>

      <form method="POST" action="<?= e($rotaUm) ?>" class="space-y-4">
        <?= csrf_campo() ?>

        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
          <select id="status" name="status" required
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
            <?php foreach ($estados as $codigo => $rotulo): ?>
              <option value="<?= e($codigo) ?>" <?= $statusActual === $codigo ? 'selected' : '' ?>><?= e($rotulo) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (erro_de('status')): ?>
            <p class="text-xs text-destructive mt-1"><?= e(erro_de('status')) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="w-full px-6 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
          Guardar estado
        </button>
      </form>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Apagar mensagem</h2>
      <p class="text-xs text-muted-foreground mb-4">Esta ação não pode ser desfeita.</p>

      <form method="POST" action="<?= e($rotaUm . '/apagar') ?>"
            onsubmit="return confirm('Apagar esta mensagem? Esta ação não pode ser desfeita.')">
        <?= csrf_campo() ?>
        <button type="submit" class="w-full px-6 py-2.5 border border-destructive/40 text-destructive text-sm font-medium rounded-md hover:bg-destructive/10 transition-colors">
          Apagar mensagem
        </button>
      </form>
    </div>
  </div>
</div>

<?= \App\Core\View::parcial('admin/_rodape'); ?>
<?php \App\Core\View::secaoFim(); ?>
