<?php
/**
 * Portfólio — detalhe do projecto (hero, ficha técnica, imagem principal e galeria).
 * Convertida de backend/resources/views/portfolio/show.blade.php
 * Recebe $slug no scope (a rota passa-o: View::render('portfolio/show', ['slug' => ...])).
 */
$slug    = (string) ($slug ?? '');
$projeto = \App\Core\Database::um(
    "SELECT * FROM portfolio_items WHERE status = 'published' AND slug = ? LIMIT 1",
    [$slug]
);

if (! $projeto) {
    \App\Core\View::limpar();
    \App\Core\Response::html(\App\Core\View::render('errors/404'), 404)->enviar();
}

$galeria = [];
foreach (\App\Core\Database::todos(
    'SELECT image_url FROM portfolio_gallery WHERE portfolio_id = ? ORDER BY sort_order',
    [$projeto['id']]
) as $linha) {
    if (trim((string) ($linha['image_url'] ?? '')) !== '') {
        $galeria[] = $linha['image_url'];
    }
}

if ($galeria === []) {
    $galeria = [$projeto['image_url']];
}

$textoMeta = trim(strip_tags((string) ($projeto['description'] ?? '')));
$metaDescription = mb_strlen($textoMeta) > 155
    ? mb_substr($textoMeta, 0, 155) . '…'
    : $textoMeta;

$titulo = $projeto['title'] . ' - HAVREDESIGN';
$meta   = $metaDescription;

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero & Project Details -->
  <section id="project-hero" class="relative py-12 md:py-20 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <a href="<?= e(rota('portfolio')) ?>" class="inline-flex items-center gap-2 text-secondary hover:underline mb-6 text-sm">
        ← Voltar ao Portfólio
      </a>
      <div id="project-header-content">
        <div class="max-w-3xl">
          <span class="text-secondary font-medium text-sm uppercase tracking-wider"><?= e($projeto['category']) ?></span>
          <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight my-4">
            <?= e($projeto['title']) ?>
          </h1>
          <p class="text-xl text-primary-foreground/80">
            <?= e($projeto['description']) ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Image & Spec Cards -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div id="project-body-content" class="space-y-12 max-w-5xl mx-auto">
        <!-- Meta stats bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-card p-6 rounded-lg border border-border shadow-sm text-center">
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Categoria</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1"><?= e($projeto['category']) ?></p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Área</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1"><?= e($projeto['area'] ?: 'N/A') ?></p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Ano</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1"><?= e($projeto['year'] ?: 'N/A') ?></p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Localização</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1"><?= e($projeto['location'] ?: 'Angola') ?></p>
          </div>
        </div>

        <!-- Featured Main Image -->
        <div class="aspect-[16/9] rounded-lg overflow-hidden shadow-lg bg-muted">
          <img src="<?= e(asset($projeto['image_url'])) ?>" alt="<?= e($projeto['title']) ?>" class="object-cover w-full h-full" loading="lazy">
        </div>

        <!-- Gallery Grid -->
        <div class="space-y-6 pt-8">
          <h3 class="font-serif text-2xl text-foreground">Galeria do Projeto</h3>
          <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($galeria as $img): ?>
              <div class="aspect-[4/3] rounded-lg overflow-hidden shadow-sm bg-muted">
                <img src="<?= e(asset($img)) ?>" alt="<?= e($projeto['title']) ?>" class="object-cover w-full h-full hover:scale-105 transition-transform duration-500" loading="lazy">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Gostou deste Projeto?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Entre em contacto connosco e descubra como podemos conceber um espaço sob medida para as suas necessidades.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
          Solicitar Projeto Semelhante
        </a>
        <a href="<?= e(rota('agendar')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 border border-primary text-primary font-medium rounded-md hover:bg-primary/10 transition-colors">
          Agendar conversa
        </a>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>
