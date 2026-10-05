<?php
/**
 * Painel — resumo da operação.
 * Espera do controller: $contagens, $pedidosRecentes, $agendamentosProximos,
 * $mensagensRecentes.
 */
$titulo = 'Painel - Administração HAVREDESIGN';
$tituloAdmin = 'Painel';
$subtituloAdmin = 'Resumo da operação — ' . date('d/m/Y');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => '']); ?>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <?php
  $destaques = [
      ['Pedidos novos', $contagens['pedidosNovos'], rota('admin.pedidos') . '?status=NEW'],
      ['Agendamentos por confirmar', $contagens['agendamentosPendentes'], rota('admin.agendamentos') . '?status=PENDING'],
      ['Mensagens por ler', $contagens['mensagensNovas'], rota('admin.mensagens') . '?status=new'],
      ['Testemunhos ocultos', $contagens['testemunhosOcultos'], rota('admin.testemunhos')],
  ];
  ?>
  <?php foreach ($destaques as [$rotulo, $numero, $link]): ?>
    <a href="<?= e($link) ?>" class="bg-card border border-border rounded-xl shadow-sm p-5 hover:border-secondary transition-colors">
      <p class="text-sm text-muted-foreground"><?= e($rotulo) ?></p>
      <p class="text-3xl font-bold text-foreground mt-1"><?= (int) $numero ?></p>
    </a>
  <?php endforeach; ?>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-4">
  <?php
  $totais = [
      ['Serviços', $contagens['servicos'], rota('admin.servicos')],
      ['Projetos', $contagens['portfolio'], rota('admin.portfolio')],
      ['Soluções', $contagens['solucoes'], rota('admin.solucoes')],
      ['Utilizadores', $contagens['utilizadores'], rota('admin.utilizadores')],
  ];
  ?>
  <?php foreach ($totais as [$rotulo, $numero, $link]): ?>
    <a href="<?= e($link) ?>" class="bg-card border border-border rounded-xl shadow-sm p-5 hover:border-secondary transition-colors flex items-baseline justify-between">
      <span class="text-sm text-muted-foreground"><?= e($rotulo) ?></span>
      <span class="text-xl font-semibold text-foreground"><?= (int) $numero ?></span>
    </a>
  <?php endforeach; ?>
</div>

<div class="grid gap-6 lg:grid-cols-2 mt-8">
  <div class="bg-card border border-border rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-foreground">Agendamentos próximos</h2>
      <a href="<?= e(rota('admin.agendamentos')) ?>" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    <?php if ($agendamentosProximos === []): ?>
      <p class="text-sm text-muted-foreground">Sem agendamentos para já.</p>
    <?php else: ?>
      <ul class="divide-y divide-border">
        <?php foreach ($agendamentosProximos as $ag): ?>
          <li class="py-3 first:pt-0 last:pb-0">
            <a href="<?= e(rota('admin.agendamentos') . '/' . $ag['id']) ?>" class="flex items-center justify-between gap-3 hover:opacity-80">
              <span class="text-sm font-medium text-foreground">
                <?= e(formatar_data($ag['appt_date'])) ?> · <?= e(substr((string) $ag['appt_time'], 0, 5)) ?> · <?= e($ag['user_name']) ?>
              </span>
              <?php $rotuloAg = ['PENDING' => 'Por confirmar', 'CONFIRMED' => 'Confirmado', 'CANCELLED' => 'Cancelado'][$ag['status']] ?? $ag['status']; ?>
              <span class="text-xs font-medium px-2 py-1 rounded-full <?= $ag['status'] === 'CONFIRMED' ? 'bg-primary/10 text-primary' : ($ag['status'] === 'CANCELLED' ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground') ?>">
                <?= e($rotuloAg) ?>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="bg-card border border-border rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-foreground">Últimos pedidos de orçamento</h2>
      <a href="<?= e(rota('admin.pedidos')) ?>" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    <?php if ($pedidosRecentes === []): ?>
      <p class="text-sm text-muted-foreground">Ainda sem pedidos.</p>
    <?php else: ?>
      <ul class="divide-y divide-border">
        <?php foreach ($pedidosRecentes as $pedido): ?>
          <li class="py-3 first:pt-0 last:pb-0">
            <a href="<?= e(rota('admin.pedidos') . '/' . $pedido['id']) ?>" class="flex items-center justify-between gap-3 hover:opacity-80">
              <span class="text-sm font-medium text-foreground">
                <?= e($pedido['project_type']) ?> · <?= e($pedido['user_name']) ?>
              </span>
              <?php $rotuloPe = ['NEW' => 'Novo', 'IN_REVIEW' => 'Em análise', 'APPROVED' => 'Aprovado', 'IN_PROGRESS' => 'Em curso', 'COMPLETED' => 'Concluído', 'REJECTED' => 'Recusado'][$pedido['status']] ?? $pedido['status']; ?>
              <span class="text-xs font-medium px-2 py-1 rounded-full bg-muted text-muted-foreground">
                <?= e($rotuloPe) ?>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="bg-card border border-border rounded-xl shadow-sm p-6 mt-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold text-foreground">Últimas mensagens de contacto</h2>
    <a href="<?= e(rota('admin.mensagens')) ?>" class="text-sm text-secondary hover:underline">Ver todas</a>
  </div>
  <?php if ($mensagensRecentes === []): ?>
    <p class="text-sm text-muted-foreground">Ainda sem mensagens.</p>
  <?php else: ?>
    <ul class="divide-y divide-border">
      <?php foreach ($mensagensRecentes as $mensagem): ?>
        <li class="py-3 first:pt-0 last:pb-0">
          <a href="<?= e(rota('admin.mensagens') . '/' . $mensagem['id']) ?>" class="flex items-center justify-between gap-3 hover:opacity-80">
            <span class="text-sm font-medium text-foreground">
              <?= e($mensagem['name']) ?> — <?= e(mb_strimwidth((string) $mensagem['message'], 0, 60, '…')) ?>
            </span>
            <span class="text-xs font-medium px-2 py-1 rounded-full <?= $mensagem['status'] === 'new' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
              <?= e(['new' => 'Nova', 'replied' => 'Respondida', 'closed' => 'Fechada'][$mensagem['status']] ?? $mensagem['status']) ?>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<?= \App\Core\View::parcial('admin/_rodape'); ?>
<?php \App\Core\View::secaoFim(); ?>
