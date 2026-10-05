<?php
/**
 * E-mail de estado ao cliente — agendamento alterado no painel admin.
 *
 * Variável: $a = [codigo, cliente, telefone, data, hora, tipo, morada,
 *                 ligacao, nota, estado]
 * Convertido de backend/resources/views/emails/appointment-status.blade.php
 */
$a      = $a ?? [];
$codigo = (string) ($a['codigo'] ?? 'PENDING');

$titulo = match ($codigo) {
    'CONFIRMED' => 'Agendamento confirmado',
    'CANCELLED' => 'Agendamento cancelado',
    default     => 'Agendamento atualizado',
};

$intro = match ($codigo) {
    'CONFIRMED' => 'O seu agendamento foi confirmado.',
    'CANCELLED' => 'Lamentamos comunicar que o seu agendamento foi cancelado.',
    default     => 'O estado do seu agendamento foi atualizado.',
};

$fecho = $codigo === 'CANCELLED'
    ? 'Se ainda pretender conversar connosco, pode pedir um novo horário na nossa página de agendamento e reagendamos consigo.'
    : 'Se precisar de alterar alguma coisa, basta responda a este e-mail e tratamos do resto.';
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;"><?= e($titulo) ?></h1>

<p style="margin:0 0 12px;">Olá <?= e($a['cliente'] ?? '') ?>,</p>

<p style="margin:0 0 12px;"><?= e($intro) ?></p>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;">
  <strong>Data:</strong> <?= e($a['data'] ?? '') ?> às <?= e($a['hora'] ?? '') ?><br>
  <strong>Tipo:</strong> <?= e($a['tipo'] ?? '') ?>
  <?php if (!empty($a['morada'])): ?>
    <br><strong>Morada:</strong> <?= e($a['morada']) ?>
  <?php endif; ?>
  <?php if (!empty($a['ligacao'])): ?>
    <br><strong>Ligação (reunião online):</strong> <?= e($a['ligacao']) ?>
  <?php endif; ?>
  <?php if (!empty($a['nota'])): ?>
    <br><strong>Nota/taxa:</strong> <?= e($a['nota']) ?>
  <?php endif; ?>
  <br><strong>Cliente:</strong> <?= e($a['cliente'] ?? '') ?> (<?= e($a['telefone'] ?? '') ?>)
  <br><strong>Estado:</strong> <?= e($a['estado'] ?? '') ?>
</div>

<p style="margin:0 0 12px;"><?= e($fecho) ?></p>

<?php if ($codigo === 'CANCELLED'): ?>
  <p style="margin:0 0 16px;">
    <a href="<?= e(url('agendar')) ?>"
       style="display:inline-block;background:#0F253F;color:#FFFFFF;text-decoration:none;padding:12px 20px;border-radius:6px;">
      Pedir novo horário
    </a>
  </p>
<?php endif; ?>

<p style="margin:0;">Obrigado,<br>HAVREDESIGN</p>
