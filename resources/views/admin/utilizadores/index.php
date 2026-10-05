<?php
/**
 * Painel — utilizadores registados e papel (USER/ADMIN).
 * Espera do controller: $utilizadores (lista de arrays — nunca inclui password).
 */
$titulo         = 'Utilizadores - Administração HAVREDESIGN';
$tituloAdmin    = 'Utilizadores';
$subtituloAdmin = 'Contas registadas e papel de acesso ao painel (USER ou ADMIN).';
$rotaBase       = rota('admin.utilizadores');
$euId           = utilizador() !== null ? (string) (utilizador()['id'] ?? '') : '';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?php $flashOk = mostrar_flash('ok'); $flashErro = mostrar_flash('erro'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'utilizadores']) ?>

<?php if (is_string($flashOk) && $flashOk !== ''): ?>
  <div class="mb-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary"><?= e($flashOk) ?></div>
<?php endif; ?>
<?php if (is_string($flashErro) && $flashErro !== ''): ?>
  <div class="mb-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?= e($flashErro) ?></div>
<?php endif; ?>

<div class="bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="border-b border-border bg-muted/60">
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Nome</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">E-mail</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Telefone</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Papel</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Registado</th>
        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Acção</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      <?php if ($utilizadores === []): ?>
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-muted-foreground">Sem utilizadores registados.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($utilizadores as $linha): ?>
          <?php
            $idLinha  = (string) $linha['id'];
            $papel    = (string) $linha['role'];
            $euesou   = $idLinha === $euId;
          ?>
          <tr class="hover:bg-muted/40 transition-colors">
            <td class="px-6 py-4 text-foreground font-medium"><?= e($linha['name']) ?></td>
            <td class="px-6 py-4 text-muted-foreground"><?= e($linha['email']) ?></td>
            <td class="px-6 py-4 text-muted-foreground"><?= e($linha['phone'] ?? '') !== '' ? e($linha['phone']) : '—' ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $papel === 'ADMIN' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                <?= e($papel) ?>
              </span>
            </td>
            <td class="px-6 py-4 text-muted-foreground"><?= e(formatar_data(isset($linha['created_at']) ? (string) $linha['created_at'] : null, 'd/m/Y H:i')) ?></td>
            <td class="px-6 py-4 text-right">
              <?php if ($euesou): ?>
                <span class="text-xs font-medium text-muted-foreground">Si próprio</span>
              <?php else: ?>
                <form method="POST" action="<?= e($rotaBase . '/' . rawurlencode($idLinha) . '/papel') ?>" class="inline"
                      data-confirm="Deseja alterar o papel deste utilizador?" data-confirm-tipo="primario" data-confirm-acao="Alterar">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="px-3 py-1.5 text-xs font-medium border border-border rounded-md text-foreground hover:bg-muted transition-colors">
                    <?= e($papel === 'ADMIN' ? 'Tornar USER' : 'Tornar ADMIN') ?>
                  </button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<p class="mt-4 text-xs text-muted-foreground">
  A palavra-passe nunca é mostrada nem pode ser alterada aqui — para isso use a
  recuperação de palavra-passe (<span class="font-mono">/recuperar-palavra-passe</span>).
</p>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
