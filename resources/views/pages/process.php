<?php
/**
 * Página "Processo de Trabalho".
 * Convertida de backend/resources/views/pages/process.blade.php
 */
$titulo = 'Processo de Trabalho - HAVREDESIGN';
$site   = \App\Support\Site::site();

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Processo de Trabalho
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Conheça as etapas que seguimos para transformar ideias em espaços funcionais, acolhedores e personalizados, desenvolvidos de acordo com as necessidades e identidade de cada cliente.
        </p>
      </div>
    </div>
  </section>

  <!-- Processo de Trabalho -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <div class="mb-12 space-y-3">
          <span class="text-secondary font-semibold text-sm uppercase tracking-wider">Etapas</span>
          <h2 class="font-serif text-3xl md:text-4xl text-foreground">Processo de Trabalho</h2>
        </div>
        <div class="space-y-12">
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">1</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">Briefing</h3>
              <p class="text-muted-foreground leading-relaxed">Analisamos as suas necessidades, objetivos e expectativas para o projeto.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">2</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">Conceção</h3>
              <p class="text-muted-foreground leading-relaxed">Damos identidade ao projeto, definindo o conceito arquitetónico e a abordagem de design.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">3</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">Desenvolvimento</h3>
              <p class="text-muted-foreground leading-relaxed">Elaboramos peças desenhadas, modelos tridimensionais e definimos os materiais.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">4</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">Apresentação e Ajustes</h3>
              <p class="text-muted-foreground leading-relaxed">Apresentamos o projeto para sua validação e fazemos os ajustes finais acordados.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">5</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">Acompanhamento</h3>
              <p class="text-muted-foreground leading-relaxed">Acompanhamos a execução da obra com fiscalização e apoio técnico contratados.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">6</div>
            </div>
            <div>
              <h3 class="font-serif text-2xl text-foreground mb-3">Entrega</h3>
              <p class="text-muted-foreground leading-relaxed">Disponibilizamos todos os elementos técnicos necessários para a execução final do projeto.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Notas de adaptação e âmbito -->
  <section class="py-16 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Etapas adaptadas</h3>
          <p class="text-primary-foreground/80 text-sm leading-relaxed">
            As etapas são adaptadas à natureza do serviço e ao âmbito definido em cada proposta.
          </p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Sobre a execução da obra</h3>
          <p class="text-primary-foreground/80 text-sm leading-relaxed">
            A HAVREDESIGN presta fiscalização e acompanhamento técnico quando contratados.
            A construção não integra a oferta atual da empresa.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Pronto para Iniciar Seu Projeto?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Agende uma consulta e vamos começar a transformar seu espaço juntos.
      </p>
      <a href="<?= e(rota('agendar')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
        Agendar conversa
        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>
