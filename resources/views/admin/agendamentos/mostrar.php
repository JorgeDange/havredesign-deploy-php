<?php
/**
 * Admin — detalhe de um agendamento + formulário de estado.
 * Espera do controller: $agendamento, $estados, $tipos, $conta.
 */
$titulo    = 'Agendamento - Administração HAVREDESIGN';
$tituloAdmin = 'Agendamento';
$subtituloAdmin = formatar_data((string) $agendamento['appt_date']) . ' às '
    . substr((string) $agendamento['appt_time'], 0, 5) . ' — '
    . ($tipos[$agendamento['type']] ?? $agendamento['type']) . '.';

$badges = [
    'PENDING'   => 'bg-primary/10 text-primary',
    'CONFIRMED' => 'bg-primary/10 text-primary',
    'CANCELLED' => 'bg-destructive/10 text-destructive',
];

$existe = static fn (mixed $v): bool => $v !== null && trim((string) $v) !== '';
$outra  = static fn (mixed $v, string $padrao = '—'): string => $existe($v) ? (string) $v : $padrao;
$quando = static fn (mixed $v): string => $existe($v) ? formatar_data((string) $v, 'd/m/Y H:i') : '—';

$base    = rota('admin.agendamentos');
$rotaUm  = $base . '/' . rawurlencode((string) $agendamento['id']);

$statusActual = (string) (velho('status', (string) $agendamento['status']) ?: $agendamento['status']);
$notaActual   = velho('fee_note', $outra($agendamento['fee_note'], ''));

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'agendamentos']); ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="<?= e($base) ?>" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($badges[$agendamento['status']] ?? 'bg-muted text-muted-foreground') ?>">
    <?= e($estados[$agendamento['status']] ?? $agendamento['status']) ?>
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Marcação</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Data</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e(formatar_data((string) $agendamento['appt_date'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Hora</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e(substr((string) $agendamento['appt_time'], 0, 5)) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Tipo</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($tipos[$agendamento['type']] ?? $agendamento['type']) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Fuso horário</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($agendamento['timezone'])) ?></p>
        </div>

        <?php if ($agendamento['type'] === 'SITE'): ?>
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Morada</p>
            <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($agendamento['address'])) ?></p>
          </div>
        <?php endif; ?>

        <?php if ($existe($agendamento['meeting_link'])): ?>
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Ligação (reunião online)</p>
            <a href="<?= e($agendamento['meeting_link']) ?>" target="_blank" rel="noopener noreferrer"
               class="text-sm font-medium text-secondary hover:underline break-all"><?= e($agendamento['meeting_link']) ?></a>
          </div>
        <?php endif; ?>

        <div class="sm:col-span-2">
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Notas do pedido</p>
          <p class="text-sm text-foreground/80 mt-1 whitespace-pre-wrap"><?= e($outra($agendamento['notes'])) ?></p>
        </div>

        <?php if ($existe($agendamento['fee_note'])): ?>
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Nota/taxa registada</p>
            <p class="text-sm text-foreground/80 mt-1 whitespace-pre-wrap"><?= e($agendamento['fee_note']) ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Cliente</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Nome</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($agendamento['user_name'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</p>
          <p class="text-sm font-medium text-foreground mt-0.5 break-all"><?= e($outra($agendamento['user_email'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($agendamento['user_phone'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Conta no site</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($conta !== null && trim((string) $conta) !== '' ? (string) $conta : 'Sem conta associada') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Atualizar agendamento</h2>
      <p class="text-xs text-muted-foreground mb-4">
        Ao mudar o estado, o cliente recebe automaticamente um e-mail de aviso.
      </p>

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

        <div class="space-y-2">
          <label for="fee_note" class="block text-sm font-medium text-foreground">Nota/taxa</label>
          <textarea id="fee_note" name="fee_note" rows="4" maxlength="500"
                    placeholder="Ex.: taxa de deslocação, condições acordadas..."
                    class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e($notaActual) ?></textarea>
          <p class="text-xs text-muted-foreground">Enviada ao cliente no e-mail de estado, quando existir.</p>
          <?php if (erro_de('fee_note')): ?>
            <p class="text-xs text-destructive mt-1"><?= e(erro_de('fee_note')) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="w-full px-6 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
          Guardar alterações
        </button>
      </form>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Registo</h2>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Pedido em</dt>
          <dd class="text-foreground font-medium text-right"><?= e($quando($agendamento['created_at'])) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Confirmado em</dt>
          <dd class="text-foreground font-medium text-right"><?= e($quando($agendamento['confirmed_at'])) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Cancelado em</dt>
          <dd class="text-foreground font-medium text-right"><?= e($quando($agendamento['cancelled_at'])) ?></dd>
        </div>
      </dl>
    </div>
  </div>
</div>

<?= \App\Core\View::parcial('admin/_rodape'); ?>
<?php \App\Core\View::secaoFim(); ?>
