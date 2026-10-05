<?php
/**
 * Minha conta — pedidos de orçamento e agendamentos do utilizador.
 * Convertida de backend/resources/views/conta/index.blade.php
 *
 * Espera do controller: $user (array|null), $pedidos (array),
 * $agendamentos (array), $tipos (array code => ['label' => ...]).
 */
$titulo = 'Minha conta - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');

$u            = $user ?? utilizador();
$pedidos      = $pedidos ?? [];
$agendamentos = $agendamentos ?? [];
$tipos        = $tipos ?? [];

$estadosPedido      = [
    'NEW'        => 'Recebido',
    'IN_REVIEW'  => 'Em análise',
    'APPROVED'   => 'Aprovado',
    'IN_PROGRESS'=> 'Em curso',
    'COMPLETED'  => 'Concluído',
    'REJECTED'   => 'Recusado',
];
$estadosAgendamento = [
    'PENDING'   => 'Por confirmar',
    'CONFIRMED' => 'Confirmado',
    'CANCELLED' => 'Cancelado',
];
?>
<section class="max-w-5xl mx-auto px-4 py-12">
  <?= \App\Core\View::parcial('partials/flash') ?>
  <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl sm:text-4xl font-bold text-foreground">Minha conta</h1>
      <p class="text-foreground/70 mt-2">Olá, <?= e($u['name'] ?? '') ?> — acompanhe aqui os seus pedidos e agendamentos.</p>
    </div>
    <form method="POST" action="<?= e(rota('sair')) ?>">
      <?= csrf_campo() ?>
      <button type="submit" class="px-5 py-2.5 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
        Terminar sessão
      </button>
    </form>
  </div>

  <div class="bg-card border border-border rounded-xl shadow-sm p-6 mb-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-sm text-muted-foreground">Email</p>
        <p class="font-medium text-foreground"><?= e($u['email'] ?? '') ?></p>
        <p class="text-sm text-muted-foreground mt-2">Membro desde <?= e(!empty($u['created_at']) ? formatar_data($u['created_at']) : '-') ?></p>
      </div>
      <?php if (($u['role'] ?? '') === 'ADMIN' || eAdmin()): ?>
        <a href="<?= e(rota('admin')) ?>" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors">
          Painel administrativo
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid gap-6 md:grid-cols-2">
    <!-- Pedidos de orçamento -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Pedidos de orçamento</h2>

      <?php if (empty($pedidos)): ?>
        <p class="text-sm text-muted-foreground">Ainda não fez pedidos de orçamento.</p>
        <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-block mt-3 text-sm font-medium text-secondary hover:underline">
          Solicitar proposta personalizada →
        </a>
      <?php else: ?>
        <ul class="divide-y divide-border">
          <?php foreach ($pedidos as $pedido): ?>
            <li class="py-3 first:pt-0 last:pb-0">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-foreground"><?= e($pedido['project_type'] ?? '') ?></p>
                  <p class="text-sm text-muted-foreground">
                    <?= e(formatar_data($pedido['created_at'] ?? null)) ?>
                    <?php if (!empty($pedido['location'])): ?> · <?= e($pedido['location']) ?><?php endif; ?>
                    <?php if (!empty($pedido['budget'])): ?> · <?= e($pedido['budget']) ?><?php endif; ?>
                  </p>
                </div>
                <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full <?= (($pedido['status'] ?? '') === 'APPROVED' || ($pedido['status'] ?? '') === 'COMPLETED') ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                  <?= e($estadosPedido[$pedido['status'] ?? ''] ?? ($pedido['status'] ?? '')) ?>
                </span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <!-- Agendamentos -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Agendamentos</h2>

      <?php if (empty($agendamentos)): ?>
        <p class="text-sm text-muted-foreground">Ainda não tem agendamentos.</p>
        <a href="<?= e(rota('agendar')) ?>" class="inline-block mt-3 text-sm font-medium text-secondary hover:underline">
          Marcar reunião →
        </a>
      <?php else: ?>
        <ul class="divide-y divide-border">
          <?php foreach ($agendamentos as $agendamento): ?>
            <li class="py-3 first:pt-0 last:pb-0">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-foreground">
                    <?= e(formatar_data($agendamento['appt_date'] ?? null)) ?> · <?= e(substr((string) ($agendamento['appt_time'] ?? ''), 0, 5)) ?>
                  </p>
                  <p class="text-sm text-muted-foreground">
                    <?= e($tipos[$agendamento['type'] ?? '']['label'] ?? ($agendamento['type'] ?? '')) ?>
                    <?php if (!empty($agendamento['address'])): ?> · <?= e($agendamento['address']) ?><?php endif; ?>
                  </p>
                </div>
                <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full <?= ($agendamento['status'] ?? '') === 'CONFIRMED' ? 'bg-primary/10 text-primary' : (($agendamento['status'] ?? '') === 'CANCELLED' ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground') ?>">
                  <?= e($estadosAgendamento[$agendamento['status'] ?? ''] ?? ($agendamento['status'] ?? '')) ?>
                </span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
