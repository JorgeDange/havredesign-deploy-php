<?php
/**
 * Admin — formulário de criação de serviço.
 * Não espera variáveis do controller (após erro usa velho()/erro_de()).
 * Convertida de backend/resources/views/admin/servicos/create.blade.php
 */
$titulo = 'Novo Serviço - Administração HAVREDESIGN';
$tituloAdmin = 'Novo serviço';
$subtituloAdmin = 'Criar um serviço principal ou complementar.';

$baseServicos = rota('admin.servicos');
$valorGrupo   = velho('group', 'principal');
$valorOrdem   = velho('sort_order', '0');
$valorAtivo   = velho('active', '1') === '1';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'servicos']) ?>

<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="<?= e($baseServicos) ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_campo() ?>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e(velho('title')) ?>"
               placeholder="Ex.: Projeto Arquitetónico"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('title')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('title')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug *</label>
        <input id="slug" name="slug" type="text" required maxlength="200"
               value="<?= e(velho('slug')) ?>"
               placeholder="Ex.: projeto-arquitetonico"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Identificador único no endereço do site.</p>
        <?php if (erro_de('slug')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('slug')) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="space-y-2">
        <label for="group" class="block text-sm font-medium text-foreground">Grupo *</label>
        <select id="group" name="group" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="principal" <?= $valorGrupo === 'principal' ? 'selected' : '' ?>>Principal</option>
          <option value="complementar" <?= $valorGrupo === 'complementar' ? 'selected' : '' ?>>Complementar</option>
        </select>
        <?php if (erro_de('group')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('group')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="icon" class="block text-sm font-medium text-foreground">Ícone</label>
        <input id="icon" name="icon" type="text" maxlength="50"
               value="<?= e(velho('icon')) ?>"
               placeholder="Ex.: home"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Nome do ícone (máx. 50 caracteres).</p>
        <?php if (erro_de('icon')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('icon')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
               value="<?= e($valorOrdem) ?>"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('sort_order')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('sort_order')) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="space-y-2">
      <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
      <textarea id="description" name="description" rows="5" required
                placeholder="Descreva o serviço apresentado no site."
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('description')) ?></textarea>
      <?php if (erro_de('description')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('description')) ?></p><?php endif; ?>
    </div>

    <div class="space-y-2">
      <label for="image" class="block text-sm font-medium text-foreground">Imagem</label>
      <input id="image" name="image" type="file" accept="image/*"
             class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
      <p class="text-xs text-muted-foreground">Formatos de imagem, até 5 MB.</p>
      <?php if (erro_de('image')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('image')) ?></p><?php endif; ?>
    </div>

    <label class="flex items-center gap-3 text-sm text-foreground">
      <input type="hidden" name="active" value="0" />
      <input id="active" name="active" type="checkbox" value="1" <?= $valorAtivo ? 'checked' : '' ?>
             class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
      <span>Serviço ativo (visível no site)</span>
    </label>
    <?php if (erro_de('active')): ?><p class="text-xs text-destructive"><?= e(erro_de('active')) ?></p><?php endif; ?>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar serviço
      </button>
      <a href="<?= e($baseServicos) ?>" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Cancelar
      </a>
    </div>
  </form>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
