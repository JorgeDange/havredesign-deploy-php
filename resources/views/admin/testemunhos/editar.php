<?php
/**
 * Painel — edição de um testemunho.
 * Espera do controller: $testemunho (array com id, name, role, content,
 * status, sort_order).
 */
$titulo         = 'Editar Testemunho - Administração HAVREDESIGN';
$tituloAdmin    = 'Editar testemunho';
$subtituloAdmin = (string) $testemunho['name'];
$idTest         = (string) $testemunho['id'];
$acao           = rota('admin.testemunhos') . '/' . rawurlencode($idTest);
$estadoActual   = (string) (velho('status', (string) $testemunho['status']));

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?php $flashOk = mostrar_flash('ok'); $flashErro = mostrar_flash('erro'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'testemunhos']) ?>

<?php if (is_string($flashOk) && $flashOk !== ''): ?>
  <div class="mb-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary"><?= e($flashOk) ?></div>
<?php endif; ?>
<?php if (is_string($flashErro) && $flashErro !== ''): ?>
  <div class="mb-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?= e($flashErro) ?></div>
<?php endif; ?>

<div class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-3xl">
  <form method="POST" action="<?= e($acao) ?>" class="space-y-5">
    <?= csrf_campo() ?>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-foreground">Nome *</label>
        <input id="name" name="name" type="text" required maxlength="150"
               value="<?= e(velho('name', (string) $testemunho['name'])) ?>"
               placeholder="Nome de quem fez o testemunho"
               class="w-full px-4 py-2.5 rounded-md border <?= erro_de('name') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('name') !== null): ?>
          <p class="text-xs text-destructive mt-1"><?= e(erro_de('name')) ?></p>
        <?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="role" class="block text-sm font-medium text-foreground">Cargo / função</label>
        <input id="role" name="role" type="text" maxlength="150"
               value="<?= e(velho('role', (string) ($testemunho['role'] ?? ''))) ?>"
               placeholder="Ex.: Cliente, Empreiteiro"
               class="w-full px-4 py-2.5 rounded-md border <?= erro_de('role') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('role') !== null): ?>
          <p class="text-xs text-destructive mt-1"><?= e(erro_de('role')) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="space-y-2">
      <label for="content" class="block text-sm font-medium text-foreground">Testemunho *</label>
      <textarea id="content" name="content" rows="6" required
                placeholder="Texto apresentado no site."
                class="w-full px-4 py-2.5 rounded-md border <?= erro_de('content') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('content', (string) $testemunho['content'])) ?></textarea>
      <?php if (erro_de('content') !== null): ?>
        <p class="text-xs text-destructive mt-1"><?= e(erro_de('content')) ?></p>
      <?php endif; ?>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
        <select id="status" name="status"
                class="w-full px-4 py-2.5 rounded-md border <?= erro_de('status') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="published" <?= $estadoActual === 'published' ? 'selected' : '' ?>>Publicado</option>
          <option value="hidden" <?= $estadoActual === 'hidden' ? 'selected' : '' ?>>Oculto</option>
        </select>
        <p class="text-xs text-muted-foreground">Também pode alternar o estado directamente na lista.</p>
        <?php if (erro_de('status') !== null): ?>
          <p class="text-xs text-destructive mt-1"><?= e(erro_de('status')) ?></p>
        <?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
               value="<?= e(velho('sort_order', (string) (int) $testemunho['sort_order'])) ?>"
               class="w-full px-4 py-2.5 rounded-md border <?= erro_de('sort_order') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Ordem crescente no site (0 aparece primeiro).</p>
        <?php if (erro_de('sort_order') !== null): ?>
          <p class="text-xs text-destructive mt-1"><?= e(erro_de('sort_order')) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar alterações
      </button>
      <a href="<?= e(rota('admin.testemunhos')) ?>" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Voltar à lista
      </a>
    </div>
  </form>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
