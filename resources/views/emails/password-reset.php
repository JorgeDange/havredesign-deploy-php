<?php
/**
 * E-mail — redefinir palavra-passe.
 * Template usado por Mailer::enviar($para, $assunto, 'password-reset', ['link' => $link, ...])
 * Texto de referência: backend/resources/views/auth/forgot-password.blade.php
 */
$link = $link ?? '';
$nome = $nome ?? '';
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Redefinir a palavra-passe</h1>

<p style="margin:0 0 12px;">Olá<?= $nome !== '' ? ', ' . e($nome) : '' ?>,</p>

<p style="margin:0 0 12px;">
  Recebemos um pedido para redefinir a palavra-passe da sua conta HAVREDESIGN.
  Informámos o seu email e enviamos este link para escolher uma nova.
</p>

<p style="margin:24px 0;">
  <a href="<?= e($link) ?>"
     style="display:inline-block;background:#0F253F;color:#FFFFFF;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:600;">
    Escolher nova palavra-passe
  </a>
</p>

<p style="margin:0 0 12px;font-size:14px;color:#555555;">
  Se o botão não funcionar, copie e cole este endereço no navegador:
  <br>
  <a href="<?= e($link) ?>" style="color:#0F253F;"><?= e($link) ?></a>
</p>

<p style="margin:0 0 12px;">
  Se não pediste esta alteração, ignora este e-mail — a tua palavra-passe actual continua válida.
</p>

<p style="margin:0;">
  Obrigado,<br>
  HAVREDESIGN
</p>
