<?php
/**
 * E-mail ao admin — novo contacto recebido.
 * Variável: $m = ['id','name','email','phone','subject','message','ip']
 */
$m = $m ?? [];
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Novo contacto recebido</h1>

<p style="margin:0 0 12px;">
  <strong>Nome:</strong> <?= e($m['name'] ?? '') ?><br>
  <strong>E-mail:</strong> <?= e($m['email'] ?? '') ?><br>
  <strong>Telefone:</strong> <?= e(($m['phone'] ?? '') !== '' ? $m['phone'] : '—') ?><br>
  <strong>Assunto:</strong> <?= e($m['subject'] ?? '') ?>
</p>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;white-space:pre-wrap;"><?= e($m['message'] ?? '') ?></div>

<p style="margin:0 0 12px;font-size:14px;color:#555555;">
  Registado por IP <?= e($m['ip'] ?? '—') ?>.
</p>

<p style="margin:0;">Obrigado,<br>HAVREDESIGN</p>
