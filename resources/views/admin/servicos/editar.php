<?php
/**
 * Admin — formulário de edição de serviço.
 * Espera do controller: $servico (linha da tabela services), $inclui
 * (itens da tabela service_includes, um por linha).
 * Convertida de backend/resources/views/admin/servicos/edit.blade.php
 */
$titulo = 'Editar Serviço - Administração HAVREDESIGN';
$tituloAdmin = 'Editar serviço';
$subtituloAdmin = (string) $servico['title'];

$baseServicos = rota('admin.servicos');
$urlEditar    = $baseServicos . '/' . $servico['id'];
$imagem       = (string) ($servico['image_url'] ?? '');

$valorGrupo = velho('group', (string) $servico['group']);
$valorOrdem = velho('sort_order', (string) (int) $servico['sort_order']);
$valorAtivo = velho('active', empty($servico['active']) ? '0' : '1') === '1';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'servicos']) ?>

<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="<?= e($urlEditar) ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_campo() ?>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e(velho('title', (string) $servico['title'])) ?>"
               placeholder="Ex.: Projeto Arquitetónico"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('title')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('title')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug *</label>
        <input id="slug" name="slug" type="text" required maxlength="200"
               value="<?= e(velho('slug', (string) $servico['slug'])) ?>"
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
               value="<?= e(velho('icon', (string) $servico['icon'])) ?>"
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
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('description', (string) $servico['description'])) ?></textarea>
      <?php if (erro_de('description')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('description')) ?></p><?php endif; ?>
    </div>

    <div class="space-y-2">
      <label for="image" class="block text-sm font-medium text-foreground">Imagem</label>
      <?php if ($imagem !== ''): ?>
        <img src="<?= e(asset($imagem)) ?>"
             alt="<?= e($servico['title']) ?>"
             class="w-40 h-28 rounded-md object-cover border border-border bg-muted" />
      <?php endif; ?>
      <input id="image" name="image" type="file" accept="image/*"
             class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
      <p class="text-xs text-muted-foreground">
        Formatos de imagem, até 5 MB. <?= $imagem !== '' ? 'Deixe vazio para manter a imagem atual.' : 'Sem imagem associada.' ?>
      </p>
      <?php if (erro_de('image')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('image')) ?></p><?php endif; ?>
    </div>

    <div class="space-y-2">
      <label for="inclui" class="block text-sm font-medium text-foreground">O que inclui</label>
      <textarea id="inclui" name="inclui" rows="8"
                placeholder="Um item por linha.&#10;Estudo prévio e anteprojeto&#10;Projeto de execução"
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('inclui', $inclui)) ?></textarea>
      <p class="text-xs text-muted-foreground">Escreva um item por linha. As linhas vazias são ignoradas e a ordem é guardada tal como escrita.</p>
      <?php if (erro_de('inclui')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('inclui')) ?></p><?php endif; ?>
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
        Guardar alterações
      </button>
      <a href="<?= e($baseServicos) ?>" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Voltar à lista
      </a>
    </div>
  </form>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
