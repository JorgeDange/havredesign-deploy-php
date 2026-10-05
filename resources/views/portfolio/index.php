<?php
/**
 * Portfólio — grelha de projectos publicados com filtros de categoria.
 * Convertida de backend/resources/views/portfolio/index.blade.php
 */
$titulo = 'Portfólio - HAVREDESIGN';
$meta   = 'Explore alguns dos projetos desenvolvidos pela HAVREDESIGN e descubra soluções arquitetónicas pensadas para transformar espaços com funcionalidade, identidade e sofisticação.';

\App\Core\View::layout('layouts/app');

$projetos = \App\Core\Database::todos(
    "SELECT * FROM portfolio_items WHERE status = 'published' ORDER BY sort_order"
);

// Filtros gerados a partir das categorias reais dos projectos publicados.
$categorias = ['Todos'];
foreach ($projetos as $projeto) {
    $cat = trim((string) ($projeto['category'] ?? ''));
    if ($cat !== '' && !in_array($cat, $categorias, true)) {
        $categorias[] = $cat;
    }
}

\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Portfólio
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Explore alguns dos projetos desenvolvidos pela HAVREDESIGN e descubra soluções arquitetónicas pensadas para transformar espaços com funcionalidade, identidade e sofisticação.
        </p>
      </div>
    </div>
  </section>

  <!-- Filter Bar -->
  <section class="py-6 border-b border-border sticky top-20 bg-background/95 backdrop-blur z-40">
    <div class="container mx-auto px-4">
      <div id="category-filters" class="flex flex-wrap gap-2">
        <?php foreach ($categorias as $indice => $cat): ?>
          <button
            data-cat="<?= e($cat) ?>"
            onclick="setCategory('<?= e($cat) ?>')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?= e($indice === 0 ? 'bg-primary text-primary-foreground font-bold' : 'border border-border text-foreground hover:bg-secondary hover:text-secondary-foreground') ?>"
          >
            <?= e($cat) ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Projects Grid -->
  <section class="py-16 reveal-on-scroll">
    <div class="container mx-auto px-4">
      <div id="portfolio-grid-container" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($projetos as $p): ?>
          <a href="<?= e(rota('portfolio') . '/' . rawurlencode((string) $p['slug'])) ?>" data-category="<?= e($p['category']) ?>" class="group block cursor-pointer card-hover-effect">
            <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-muted shadow-sm">
              <img src="<?= e(asset($p['image_url'])) ?>"
                alt="<?= e($p['title']) ?>"
                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500"
                loading="lazy">
              <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                <div>
                  <span class="text-secondary text-sm font-semibold"><?= e($p['category']) ?></span>
                  <h3 class="font-serif text-xl text-primary-foreground"><?= e($p['title']) ?></h3>
                </div>
              </div>
            </div>
            <div class="space-y-1">
              <span class="text-xs text-secondary font-semibold uppercase tracking-wider"><?= e($p['category']) ?></span>
              <h3 class="font-serif text-lg text-foreground group-hover:text-primary transition-colors line-clamp-1">
                <?= e($p['title']) ?>
              </h3>
              <p class="text-sm text-muted-foreground line-clamp-2">
                <?= e($p['description']) ?>
              </p>
              <div class="flex flex-wrap gap-3 text-xs text-muted-foreground mt-2">
                <?php if ($p['area']): ?>
                  <span class="flex items-center gap-1"><svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg> <?= e($p['area']) ?></span>
                <?php endif; ?>
                <?php if ($p['year']): ?>
                  <span class="flex items-center gap-1"><svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> <?= e($p['year']) ?></span>
                <?php endif; ?>
                <?php if ($p['location']): ?>
                  <span class="flex items-center gap-1"><svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> <?= e($p['location']) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; ?>

        <div id="portfolio-empty" class="<?= e(empty($projetos) ? '' : 'hidden') ?> col-span-full text-center py-12 text-muted-foreground">
          Nenhum projeto encontrado nesta categoria.
        </div>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var base = 'px-4 py-2 text-sm font-medium rounded-md transition-colors';
    var activeClass = 'bg-primary text-primary-foreground font-bold';
    var idleClass = 'border border-border text-foreground hover:bg-secondary hover:text-secondary-foreground';

    var filterButtons = Array.prototype.slice.call(document.querySelectorAll('#category-filters button'));
    var cards = Array.prototype.slice.call(document.querySelectorAll('#portfolio-grid-container [data-category]'));
    var empty = document.getElementById('portfolio-empty');

    window.setCategory = function (cat) {
      filterButtons.forEach(function (btn) {
        var isActive = btn.getAttribute('data-cat') === cat;
        btn.className = base + ' ' + (isActive ? activeClass : idleClass);
      });

      var visible = 0;
      cards.forEach(function (card) {
        var show = cat === 'Todos' || card.getAttribute('data-category') === cat;
        card.classList.toggle('hidden', !show);
        if (show) visible += 1;
      });

      if (empty) empty.classList.toggle('hidden', visible > 0);
    };

    window.setCategory('Todos');
  });
</script>
<?php \App\Core\View::secaoFim(); ?>
