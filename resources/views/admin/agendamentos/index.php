<?php
/**
 * Admin — lista de agendamentos.
 * Espera do controller: $agendamentos, $status, $data, $estados, $contagens,
 * $tipos (código => rótulo PT).
 */
$titulo         = 'Agendamentos - Administração HAVREDESIGN';
$tituloAdmin    = 'Agendamentos';
$subtituloAdmin = 'Reuniões e visitas pedidas pelo site.';

$badges = [
    'PENDING'   => 'bg-primary/10 text-primary',
    'CONFIRMED' => 'bg-primary/10 text-primary',
    'CANCELLED' => 'bg-destructive/10 text-destructive',
];

$base = rota('admin.agendamentos');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'agendamentos']); ?>

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-2">
      <a href="<?= e($base . ($data !== '' ? '?data=' . rawurlencode($data) : '')) ?>"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors <?= $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' ?>">
        Todos
        <span class="px-1.5 py-0.5 rounded-full <?= $status === '' ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' ?>"><?= (int) $contagens['total'] ?></span>
      </a>

      <?php foreach ($estados as $codigo => $rotulo): ?>
        <?php
        $ativo = $status === $codigo;
        $qs    = array_filter(['status' => $codigo, 'data' => $data], static fn (string $v): bool => $v !== '');
        ?>
        <a href="<?= e($base . '?' . http_build_query($qs)) ?>"
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors <?= $ativo ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' ?>">
          <?= e($rotulo) ?>
          <span class="px-1.5 py-0.5 rounded-full <?= $ativo ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' ?>"><?= (int) ($contagens[$codigo] ?? 0) ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <form method="GET" action="<?= e($base) ?>" class="flex flex-wrap items-end gap-2">
      <?php if ($status !== ''): ?>
        <input type="hidden" name="status" value="<?= e($status) ?>" />
      <?php endif; ?>

      <div class="space-y-1">
        <label for="data" class="block text-xs text-muted-foreground">Data</label>
        <input id="data" name="data" type="date" value="<?= e($data) ?>"
               class="px-3 py-1.5 rounded-md border border-border bg-card text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
      </div>

      <button type="submit" class="px-3 py-1.5 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
        Filtrar
      </button>

      <?php if ($data !== ''): ?>
        <a href="<?= e($base . ($status !== '' ? '?status=' . rawurlencode($status) : '')) ?>" class="px-3 py-1.5 text-xs font-medium text-secondary hover:underline">
          Limpar data
        </a>
      <?php endif; ?>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-muted text-left">
          <th class="px-4 py-3 font-medium text-muted-foreground">Data</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Hora</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Cliente</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Tipo</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        <?php if ($agendamentos === []): ?>
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem agendamentos para os filtros selecionados.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($agendamentos as $agendamento): ?>
            <?php $cor = $badges[$agendamento['status']] ?? 'bg-muted text-muted-foreground'; ?>
            <tr class="hover:bg-muted/50 transition-colors">
              <td class="px-4 py-3 text-foreground whitespace-nowrap"><?= e(formatar_data((string) $agendamento['appt_date'])) ?></td>
              <td class="px-4 py-3 text-muted-foreground whitespace-nowrap"><?= e(substr((string) $agendamento['appt_time'], 0, 5)) ?></td>
              <td class="px-4 py-3">
                <p class="font-medium text-foreground"><?= e($agendamento['user_name']) ?></p>
                <p class="text-xs text-muted-foreground"><?= e($agendamento['user_email']) ?></p>
              </td>
              <td class="px-4 py-3 text-foreground/80"><?= e($tipos[$agendamento['type']] ?? $agendamento['type']) ?></td>
              <td class="px-4 py-3">
                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($cor) ?>">
                  <?= e($estados[$agendamento['status']] ?? $agendamento['status']) ?>
                </span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="<?= e($base . '/' . rawurlencode($agendamento['id'])) ?>" class="text-sm font-medium text-secondary hover:underline">Ver detalhe</a>
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
