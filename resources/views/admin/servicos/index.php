<?php
/**
 * Admin — lista de serviços.
 * Espera do controller: $servicos (linhas da tabela services).
 * Convertida de backend/resources/views/admin/servicos/index.blade.php
 */
$titulo = 'Serviços - Administração HAVREDESIGN';
$tituloAdmin = 'Serviços';
$subtituloAdmin = 'Gestão dos serviços principais e complementares apresentados no site.';

$totalServicos = count($servicos);
$baseServicos  = rota('admin.servicos');

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'servicos']) ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground"><?= (int) $totalServicos ?> serviço(s) registado(s)</p>
  <a href="<?= e($baseServicos . '/novo') ?>" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Novo serviço
  </a>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Imagem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Serviço</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Grupo</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      <?php if ($servicos === []): ?>
        <tr>
          <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
            Ainda não existem serviços. Use «Novo serviço» para criar o primeiro.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($servicos as $servico): ?>
          <?php
          $imagem     = (string) ($servico['image_url'] ?? '');
          $ePrincipal = ($servico['group'] ?? '') === 'principal';
          $eAtivo     = !empty($servico['active']);
          ?>
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3">
              <?php if ($imagem !== ''): ?>
                <img src="<?= e(asset($imagem)) ?>"
                     alt="<?= e($servico['title']) ?>"
                     loading="lazy"
                     class="h-12 w-16 rounded-md object-cover border border-border bg-muted" />
              <?php else: ?>
                <span class="inline-flex h-12 w-16 items-center justify-center rounded-md border border-border bg-muted text-xs text-muted-foreground">Sem imagem</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3">
              <p class="font-medium text-foreground"><?= e($servico['title']) ?></p>
              <p class="text-xs text-muted-foreground"><?= e($servico['slug']) ?></p>
            </td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= $ePrincipal ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                <?= $ePrincipal ? 'Principal' : 'Complementar' ?>
              </span>
            </td>
            <td class="px-4 py-3 text-muted-foreground"><?= (int) $servico['sort_order'] ?></td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= $eAtivo ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                <?= $eAtivo ? 'Ativo' : 'Inativo' ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                <a href="<?= e($baseServicos . '/' . $servico['id'] . '/editar') ?>"
                   class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Editar
                </a>
                <form method="POST" action="<?= e($baseServicos . '/' . $servico['id'] . '/alternar') ?>" class="inline">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                    <?= $eAtivo ? 'Desativar' : 'Ativar' ?>
                  </button>
                </form>
                <form method="POST" action="<?= e($baseServicos . '/' . $servico['id'] . '/apagar') ?>" class="inline"
                      data-confirm="Apagar o serviço «<?= e($servico['title']) ?>»? Esta ação não pode ser desfeita." data-confirm-acao="Apagar">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                    Apagar
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
