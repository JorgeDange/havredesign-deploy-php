<?php
/**
 * Portefólio (admin) — formulário de criação de projeto.
 * Sem dados do controller (apenas old input / erros de validação).
 */
$titulo = 'Novo projeto - Administração HAVREDESIGN';

$prefixo       = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');
$rotaPortfolio = $prefixo . '/portfolio';

$categorias = ['Residencial', 'Comercial', 'Corporativo', 'Outro'];
$segmentos  = [
    'Investimento imobiliário residencial',
    'Habitação própria',
    'Comércio e serviços',
    'Outro',
];

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', [
    'titulo'    => 'Novo projeto',
    'subtitulo' => 'Adicionar um projeto ao portefólio',
    'aba'       => 'portfolio',
]) ?>

<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="<?= e($rotaPortfolio) ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_campo() ?>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2 sm:col-span-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200" value="<?= e(velho('title')) ?>"
               placeholder="Ex.: Moradia com vista para o mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('title')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('title')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug</label>
        <p class="text-xs text-muted-foreground -mt-1">Em branco, gera automaticamente a partir do título.</p>
        <input id="slug" name="slug" type="text" maxlength="200" value="<?= e(velho('slug')) ?>"
               placeholder="moradia-vista-mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('slug')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('slug')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="category" class="block text-sm font-medium text-foreground">Categoria *</label>
        <select id="category" name="category" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">Selecione a categoria</option>
          <?php foreach ($categorias as $categoria): ?>
            <option value="<?= e($categoria) ?>" <?= velho('category') === $categoria ? 'selected' : '' ?>><?= e($categoria) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (erro_de('category')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('category')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="status" class="block text-sm font-medium text-foreground">Estado *</label>
        <select id="status" name="status" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="published" <?= velho('status', 'published') === 'published' ? 'selected' : '' ?>>Publicado</option>
          <option value="draft" <?= velho('status', 'published') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
        </select>
        <?php if (erro_de('status')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('status')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="segment" class="block text-sm font-medium text-foreground">Segmento</label>
        <select id="segment" name="segment"
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">—</option>
          <?php foreach ($segmentos as $segmento): ?>
            <option value="<?= e($segmento) ?>" <?= velho('segment') === $segmento ? 'selected' : '' ?>><?= e($segmento) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (erro_de('segment')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('segment')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="area" class="block text-sm font-medium text-foreground">Área</label>
        <input id="area" name="area" type="text" maxlength="50" value="<?= e(velho('area')) ?>"
               placeholder="Ex.: 450m2"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('area')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('area')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="year" class="block text-sm font-medium text-foreground">Ano</label>
        <input id="year" name="year" type="number" min="2000" max="2030" step="1" value="<?= e(velho('year')) ?>"
               placeholder="2000 — 2030"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('year')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('year')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="location" class="block text-sm font-medium text-foreground">Localização</label>
        <input id="location" name="location" type="text" maxlength="255" value="<?= e(velho('location')) ?>"
               placeholder="Ex.: Talatona, Luanda"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('location')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('location')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <p class="text-xs text-muted-foreground -mt-1">Ordenação na listagem pública (0 = primeiro).</p>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="<?= e(velho('sort_order', '0')) ?>"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <?php if (erro_de('sort_order')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('sort_order')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2 sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
        <textarea id="description" name="description" required rows="6"
                  placeholder="Descreva o projeto: tipologia, área, materiais e diferenciais."
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('description')) ?></textarea>
        <?php if (erro_de('description')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('description')) ?></p><?php endif; ?>
      </div>

      <div class="space-y-2">
        <label class="flex items-center gap-3 text-sm text-foreground">
          <input type="hidden" name="featured" value="0" />
          <input id="featured" name="featured" type="checkbox" value="1" <?= velho('featured') === '1' ? 'checked' : '' ?>
                 class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
          <span>Destacar no Início</span>
        </label>
      </div>

      <div class="space-y-2">
        <label for="image" class="block text-sm font-medium text-foreground">Imagem de capa</label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
        <img id="image-previa" alt="Pré-visualização da imagem de capa" hidden
             class="hidden h-24 w-36 rounded-md object-cover bg-muted border border-border" />
        <p class="text-xs text-muted-foreground">JPG, PNG ou WEBP até 5 MB.</p>
        <?php if (erro_de('image')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('image')) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar projeto
      </button>
      <a href="<?= e($rotaPortfolio) ?>" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Cancelar
      </a>
    </div>
  </form>
</div>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
<script>
  (function () {
    var entrada = document.getElementById('image');
    var previa = document.getElementById('image-previa');
    if (!entrada || !previa) return;

    entrada.addEventListener('change', function () {
      var ficheiro = entrada.files && entrada.files[0];
      previa.classList.add('hidden');
      previa.removeAttribute('src');
      if (!ficheiro) return;
      previa.src = URL.createObjectURL(ficheiro);
      previa.classList.remove('hidden');
    });
  })();
</script>
<?php \App\Core\View::secaoFim(); ?>
