<?php
/**
 * Serviços — principais e complementares.
 * Convertida de backend/resources/views/services/index.blade.php
 */
$titulo = 'Nossos Serviços - HAVREDESIGN';
$meta   = 'Serviços principais e complementares da HAVREDESIGN: projeto arquitetónico, design de interiores, fiscalização e apoio técnico, adaptados a cada fase do projeto.';

\App\Core\View::layout('layouts/app');

$mainServices = \App\Core\Database::todos(
    "SELECT * FROM services WHERE active = 1 AND `group` = 'principal' ORDER BY sort_order"
);
$complementaryServices = \App\Core\Database::todos(
    "SELECT * FROM services WHERE active = 1 AND `group` = 'complementar' ORDER BY sort_order"
);

// "includes" dos serviços (service_includes), agrupados por serviço (evita N+1).
$incluidos = [];
foreach (\App\Core\Database::todos('SELECT service_id, item FROM service_includes ORDER BY service_id, sort_order') as $linha) {
    $incluidos[$linha['service_id']][] = $linha['item'];
}

// Mesmos SVGs Lucide de ui/js/app.js (renderLucideIcon) — só os `path`s.
$lucide = [
    'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/>',
    'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
    'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'lightbulb' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
    'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'maximize' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>',
    'image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'dollar-sign' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1v22m5-18H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
    'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'trees' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7m-7-7v18"/>',
];

\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Nossos Serviços
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Serviços principais e complementares para transformar o seu espaço, adaptados a cada fase do projeto.
        </p>
      </div>
    </div>
  </section>

  <!-- Serviços Principais -->
  <section class="py-14 md:py-24 reveal-on-scroll">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl mb-16">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground mb-4">Serviços Principais</h2>
        <p class="text-muted-foreground leading-relaxed">
          As principais áreas de atuação da HAVREDESIGN. Podem ser contratados separadamente ou integrados numa HAVRE Solução.
        </p>
      </div>
      <div id="services-main-list" class="space-y-24">
        <?php foreach ($mainServices as $indice => $service): ?>
          <?php
            $isEven = $indice % 2 === 0;
            $serviceIncludes = $incluidos[$service['id']] ?? [];
          ?>
          <div id="<?= e($service['slug']) ?>" class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 <?= e(!$isEven ? 'lg:order-2' : '') ?>">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
                  <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $lucide[$service['icon']] ?? $lucide['home'] ?></svg>
                </div>
                <h3 class="font-serif text-2xl md:text-3xl text-foreground"><?= e($service['title']) ?></h3>
              </div>
              <p class="text-muted-foreground leading-relaxed text-lg">
                <?= e($service['description']) ?>
              </p>
              <?php if (count($serviceIncludes) > 0): ?>
                <div class="space-y-3">
                  <h4 class="font-medium text-foreground">O que inclui:</h4>
                  <ul class="grid sm:grid-cols-2 gap-2">
                    <?php foreach ($serviceIncludes as $include): ?>
                      <li class="flex items-center gap-2 text-sm text-muted-foreground">
                        <svg class="w-4 h-4 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?= e($include) ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>
              <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors mt-4">
                Solicitar proposta personalizada
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </a>
            </div>
            <div class="relative aspect-[4/3] rounded-lg overflow-hidden bg-muted shadow-md <?= e(!$isEven ? 'lg:order-1' : '') ?>">
              <?php if ($service['image_url']): ?>
                <img src="<?= e(asset($service['image_url'])) ?>" alt="<?= e($service['title']) ?>" class="object-cover w-full h-full" loading="lazy">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-secondary">
                  <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $lucide[$service['icon']] ?? $lucide['home'] ?></svg>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Serviços Complementares -->
  <section class="py-14 md:py-24 bg-muted reveal-on-scroll">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl mb-16">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground mb-4">Serviços Complementares</h2>
        <p class="text-muted-foreground leading-relaxed">
          Serviços destinados a responder a necessidades específicas do projeto. Podem ser contratados separadamente ou associados a outros serviços.
        </p>
      </div>
      <div id="services-complementary-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($complementaryServices as $service): ?>
          <div id="<?= e($service['slug']) ?>" class="bg-card border border-border rounded-lg p-6 flex flex-col gap-3 shadow-sm card-hover-effect">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 rounded-lg bg-secondary/20 flex items-center justify-center text-secondary shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $lucide[$service['icon']] ?? $lucide['home'] ?></svg>
              </div>
              <h3 class="font-serif text-xl text-foreground leading-snug"><?= e($service['title']) ?></h3>
            </div>
            <p class="text-sm text-muted-foreground leading-relaxed flex-1">
              <?= e($service['description']) ?>
            </p>
            <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-4 py-2 border border-border rounded-md text-sm font-medium text-foreground hover:bg-muted transition-colors">
              Solicitar proposta personalizada
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary reveal-on-scroll">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Não Encontrou o Que Procura?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Entre em contacto connosco e partilhe a sua ideia.
        Na HAVREDESIGN desenvolvemos soluções personalizadas, pensadas para responder às necessidades, identidade e objetivos de cada projeto.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="<?= e(rota('contacto')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
          Fale Conosco
        </a>
        <a href="<?= e(rota('agendar')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 border border-primary text-primary font-medium rounded-md hover:bg-primary/10 transition-colors">
          Agendar conversa
        </a>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>
