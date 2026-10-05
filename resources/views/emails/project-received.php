<?php
/**
 * E-mail ao admin — novo pedido de projeto.
 * Variável: $p = ['ref','project_type','service','solution','location','budget',
 *                  'timeline','user_name','user_email','canal','anexos']
 */
$p = $p ?? [];
$canal = match ($p['canal'] ?? '') {
    'phone'    => 'telefone',
    'whatsapp' => 'WhatsApp',
    default    => 'e-mail',
};
?>
<h1 style="margin:0 0 16px;font-size:22px;color:#0F253F;">Pedido de projeto recebido</h1>

<div style="background:#F7F7F7;border-left:4px solid #0F253F;padding:16px;margin:16px 0;">
  <strong>Referência:</strong> <?= e($p['ref'] ?? '') ?><br>
  <strong>Cliente:</strong> <?= e($p['user_name'] ?? '') ?> (<?= e($p['user_email'] ?? '') ?>)<br>
  <strong>Tipo:</strong> <?= e($p['project_type'] ?? '') ?><br>
  <strong>Serviço:</strong> <?= e(($p['service'] ?? '') !== '' ? $p['service'] : '—') ?><br>
  <strong>Solução:</strong> <?= e(($p['solution'] ?? '') !== '' ? $p['solution'] : '—') ?><br>
  <strong>Localização:</strong> <?= e(($p['location'] ?? '') !== '' ? $p['location'] : '—') ?><br>
  <strong>Orçamento:</strong> <?= e(($p['budget'] ?? '') !== '' ? $p['budget'] : '—') ?><br>
  <strong>Prazo:</strong> <?= e(($p['timeline'] ?? '') !== '' ? $p['timeline'] : '—') ?>
</div>

<?php if (!empty($p['anexos'])): ?>
<p style="margin:0 0 12px;"><strong>Anexos recebidos:</strong> <?= e(implode(', ', $p['anexos'])) ?></p>
<?php endif; ?>

<p style="margin:0 0 12px;">A equipa deverá contactar o cliente através de <strong><?= e($canal) ?></strong>.</p>

<p style="margin:0;">Obrigado,<br>HAVREDESIGN</p>
