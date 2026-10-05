<?php
/**
 * Soluções (admin) — edição de solução.
 * Espera do controller: $solucao.
 */
$titulo = 'Editar Solução - Administração HAVREDESIGN';

$prefixo      = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');
$rotaSolucoes = $prefixo . '/solucoes';
$urlGuardar   = $rotaSolucoes . '/' . rawurlencode((string) $solucao['id']);
$ativa        = (string) (int) ($solucao['active'] ?? 0);

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', [
    'titulo'    => 'Editar solução',
    'subtitulo' => (string) $solucao['name'],
    'aba'       => 'solucoes',
]) ?>

<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="<?= e($urlGuardar) ?>" class="space-y-5">
    <?= csrf_campo() ?>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="code" class="block text-sm font-medium text-foreground">Código *</label>
        <input id="code" name="code" type="text" required maxlength="20"
               value="<?= e(velho('code', (string) $solucao['code'])) ?>"
               placeholder="Ex.: HAV-01"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Máx. 20 caracteres, tem de ser único.</p>
        <?php if (erro_de('code')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('code')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-foreground">Nome *</label>
        <input id="name" name="name" type="text" required maxlength="120"
               value="<?= e(velho('name', (string) $solucao['name'])) ?>"
               placeholder="Ex.: HAVRE Essencial"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('name')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('name')) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug *</label>
        <input id="slug" name="slug" type="text" required maxlength="120"
               value="<?= e(velho('slug', (string) $solucao['slug'])) ?>"
               placeholder="Ex.: havre-essencial"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Identificador único no endereço do site.</p>
        <?php if (erro_de('slug')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('slug')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="cta_label" class="block text-sm font-medium text-foreground">Texto do botão</label>
        <input id="cta_label" name="cta_label" type="text" maxlength="60"
               value="<?= e(velho('cta_label', (string) ($solucao['cta_label'] ?? ''))) ?>"
               placeholder="Ex.: Solicitar orçamento"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('cta_label')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('cta_label')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
               value="<?= e(velho('sort_order', (string) (int) ($solucao['sort_order'] ?? 0))) ?>"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('sort_order')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('sort_order')) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="space-y-2">
      <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
      <textarea id="description" name="description" rows="5" required
                placeholder="Descreva a solução apresentada no site."
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('description', (string) $solucao['description'])) ?></textarea>
      <?php if (erro_de('description')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('description')) ?></p><?php endif; ?>
    </div>

    <label class="flex items-center gap-3 text-sm text-foreground">
      <input type="hidden" name="active" value="0" />
      <input id="active" name="active" type="checkbox" value="1" <?= velho('active', $ativa) === '1' ? 'checked' : '' ?>
             class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
      <span>Solução ativa (visível no site)</span>
    </label>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar alterações
      </button>
      <a href="<?= e($rotaSolucoes) ?>" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Voltar à lista
      </a>
    </div>
  </form>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
