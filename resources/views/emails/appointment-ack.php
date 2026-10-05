<?php
/**
 * E-mail de confirmação ao cliente — pedido de agendamento.
 * Variável: $a = ['cliente','data','hora','tipo','local','estado']
 */
$a = $a ?? [];
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Pedido de agendamento</h1>

<p style="margin:0 0 12px;">Olá,</p>

<p style="margin:0 0 12px;">
  Recebemos o seu pedido de agendamento. Iremos confirmá-lo por e-mail
  assim que possível.
</p>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;">
  <strong>Data:</strong> <?= e($a['data'] ?? '') ?> às <?= e($a['hora'] ?? '') ?><br>
  <strong>Tipo:</strong> <?= e($a['tipo'] ?? '') ?><br>
  <strong>Local:</strong> <?= e($a['local'] ?? '—') ?><br>
  <strong>Estado:</strong> <?= e($a['estado'] ?? 'Por confirmar') ?>
</div>

<p style="margin:0 0 12px;">
  Se precisar de alterar alguma coisa, responda a este e-mail
  ou fale connosco por WhatsApp.
</p>

<p style="margin:0;">Obrigado,<br>HAVREDESIGN</p>
