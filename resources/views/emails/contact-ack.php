<?php
/**
 * E-mail de confirmação ao cliente — mensagem recebida.
 * Variável: $m = ['name','email','subject','message']
 */
$m = $m ?? [];
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Mensagem recebida</h1>

<p style="margin:0 0 12px;">Olá <?= e($m['name'] ?? '') ?>,</p>

<p style="margin:0 0 12px;">
  Recebemos a sua mensagem e a nossa equipa responderá com a maior brevidade possível.
</p>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;">
  <strong>Assunto:</strong> <?= e($m['subject'] ?? '') ?><br>
  <span style="white-space:pre-wrap;"><?= e($m['message'] ?? '') ?></span>
</div>

<p style="margin:0 0 12px;">
  Se preferir adiantar detalhes do seu projeto, pode responder a este e-mail
  ou falar connosco por WhatsApp.
</p>

<p style="margin:0;">Obrigado,<br>HAVREDESIGN</p>
