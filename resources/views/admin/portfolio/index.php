<?php
/**
 * Portefólio (admin) — lista de projetos.
 * Espera do controller: $projetos.
 */
$titulo = 'Portefólio - Administração HAVREDESIGN';

$prefixo       = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');
$rotaPortfolio = $prefixo . '/portfolio';
$totalProjetos = count($projetos);

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', [
    'titulo'    => 'Portefólio',
    'subtitulo' => $totalProjetos . ' projeto(s) no portefólio',
    'aba'       => 'portfolio',
]) ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground">Organize os projetos por ordem, estado de publicação e destaque no Início.</p>
  <a href="<?= e($rotaPortfolio . '/novo') ?>" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Novo projeto
  </a>
</div>

<div class="bg-card border border-border rounded-xl shadow-sm mt-4 overflow-hidden">
  <?php if ($totalProjetos === 0): ?>
    <div class="p-8 text-center">
      <p class="text-sm text-muted-foreground">Ainda não existem projetos no portefólio.</p>
      <a href="<?= e($rotaPortfolio . '/novo') ?>" class="inline-block mt-4 px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Criar o primeiro projeto
      </a>
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm min-w-[760px]">
        <thead>
          <tr class="bg-muted text-muted-foreground">
            <th scope="col" class="text-left font-medium px-4 py-3">Imagem</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Título</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Categoria</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Estado</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Destaque</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Ordem</th>
            <th scope="col" class="text-right font-medium px-4 py-3">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php foreach ($projetos as $projeto): ?>
            <?php
              $imagemCapa = (string) ($projeto['image_url'] ?? '');
              $eDestacado = (int) ($projeto['featured'] ?? 0) === 1;
              $ePublicado = (string) ($projeto['status'] ?? '') === 'published';
              $urlEditar  = $rotaPortfolio . '/' . rawurlencode((string) $projeto['id']) . '/editar';
              $urlApagar  = $rotaPortfolio . '/' . rawurlencode((string) $projeto['id']) . '/apagar';
            ?>
            <tr>
              <td class="px-4 py-3">
                <?php if ($imagemCapa !== ''): ?>
                  <img src="<?= e(asset($imagemCapa)) ?>"
                       alt="<?= e($projeto['title']) ?>"
                       class="h-12 w-16 rounded-md object-cover bg-muted border border-border" />
                <?php else: ?>
                  <span class="flex h-12 w-16 items-center justify-center rounded-md bg-muted border border-border text-xs text-muted-foreground">sem imagem</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3">
                <span class="font-medium text-foreground"><?= e($projeto['title']) ?></span>
                <?php if ((string) ($projeto['slug'] ?? '') !== ''): ?>
                  <span class="block text-xs text-muted-foreground">/portfolio/<?= e($projeto['slug']) ?></span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-muted-foreground"><?= e($projeto['category']) ?></td>
              <td class="px-4 py-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full <?= $ePublicado ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                  <?= $ePublicado ? 'Publicado' : 'Rascunho' ?>
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full <?= $eDestacado ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' ?>">
                  <?= $eDestacado ? 'Sim' : 'Não' ?>
                </span>
              </td>
              <td class="px-4 py-3 text-muted-foreground"><?= (int) ($projeto['sort_order'] ?? 0) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="<?= e($urlEditar) ?>"
                   class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Editar
                </a>
                <form method="POST" action="<?= e($urlApagar) ?>" class="inline"
                      data-confirm="Apagar o projeto «<?= e($projeto['title']) ?>»? Será removido do portefólio e não pode ser anulado." data-confirm-acao="Apagar">
                  <?= csrf_campo() ?>
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                    Apagar
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
