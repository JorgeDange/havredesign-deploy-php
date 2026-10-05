<?php
/**
 * E-mail ao admin — novo pedido de agendamento.
 * Variável: $a = ['cliente','data','hora','tipo','local','estado'], $notas (opcional)
 */
$a = $a ?? [];
$notas = $notas ?? '';
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Novo pedido de agendamento</h1>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;">
  <strong>Cliente:</strong> <?= e($a['cliente'] ?? '') ?><br>
  <strong>Data:</strong> <?= e($a['data'] ?? '') ?> às <?= e($a['hora'] ?? '') ?><br>
  <strong>Tipo:</strong> <?= e($a['tipo'] ?? '') ?><br>
  <strong>Local:</strong> <?= e($a['local'] ?? '—') ?><br>
  <strong>Estado:</strong> <?= e($a['estado'] ?? 'Por confirmar') ?>
</div>

<?php if ($notas !== ''): ?>
<p style="margin:0 0 12px;"><strong>Observações:</strong><br>
<span style="white-space:pre-wrap;"><?= e($notas) ?></span></p>
<?php endif; ?>

<p style="margin:0;">HAVREDESIGN</p>
