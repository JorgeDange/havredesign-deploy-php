<?php
/**
 * Página "Sobre".
 * Convertida de backend/resources/views/pages/about.blade.php
 */
$titulo = 'Sobre - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Sobre a HAVREDESIGN
        </h1>
        <p class="text-xl text-primary-foreground/80 font-serif italic">
          ARQUITETURA COMO REFÚGIO
        </p>
      </div>
    </div>
  </section>

  <!-- Propósito -->
  <section class="py-16 bg-muted">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto text-center space-y-6">
        <h2 class="font-serif text-2xl md:text-3xl text-foreground">Propósito</h2>
        <p class="font-serif italic text-xl md:text-2xl text-secondary leading-relaxed">
          Utilizar a arquitetura como um instrumento para melhorar a vida das pessoas, criando espaços que
          acolhem, inspiram e permanecem relevantes ao longo do tempo.
        </p>
      </div>
    </div>
  </section>

  <!-- History Part 1 -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-16 items-center">
        <div class="space-y-6">
          <h2 class="font-serif text-2xl md:text-3xl text-foreground">Nossa História</h2>
          <p class="text-muted-foreground leading-relaxed">
            A HAVREDESIGN é um atelier de arquitetura dedicado à criação de soluções inovadoras, funcionais e sustentáveis, desenvolvendo projetos residenciais, comerciais, corporativos e institucionais com uma abordagem contemporânea e sensível ao contexto de cada espaço.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Acreditamos que a arquitetura vai além do projeto — ela influencia a forma como as pessoas vivem, trabalham e se relacionam com o ambiente ao seu redor. Por isso, cada projeto é pensado de forma personalizada, equilibrando estética, funcionalidade, conforto e identidade.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Com um olhar atento aos detalhes e à qualidade dos espaços construídos, a HAVREDESIGN cria ambientes que acolhem, inspiram e proporcionam experiências únicas, sempre com foco na inovação, sofisticação e sustentabilidade.
          </p>
          <p class="font-serif italic text-lg text-secondary font-semibold">
            Mais do que projetar espaços, criamos refúgios com propósito.
          </p>
        </div>
        <div class="relative">
          <div class="aspect-[3/4] rounded-lg overflow-hidden shadow-lg">
            <img src="<?= e(asset('assets/janette3.jpeg')) ?>"
              alt="Atelier HAVREDESIGN"
              width="960" height="1280"
              loading="lazy" decoding="async"
              class="object-cover w-full h-full">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Filosofia -->
  <section class="py-14 md:py-24 bg-secondary text-secondary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl mx-auto text-center space-y-6">
        <h2 class="font-serif text-2xl md:text-3xl">Filosofia</h2>
        <p class="leading-relaxed text-lg">
          HAVRE é um termo de origem francesa associado à ideia de porto, abrigo ou refúgio.
          Para a HAVREDESIGN, um refúgio é um ambiente capaz de proporcionar segurança, bem-estar e sentido de pertença.
        </p>
        <p class="font-serif italic text-xl">Arquitetura como refúgio — excelência em cada detalhe.</p>
      </div>
    </div>
  </section>

  <!-- Founder Section -->
  <section class="py-14 md:py-24 bg-muted">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-16 items-center">
        <div class="space-y-6">
          <h2 class="font-serif text-2xl md:text-3xl text-foreground">A Nossa Fundadora</h2>
          <p class="text-muted-foreground leading-relaxed">
            Arq.ª Janette Rodrigues da Conceição é arquiteta e fundadora da HAVREDESIGN, atelier dedicado ao desenvolvimento de soluções arquitetônicas contemporâneas, funcionais e sustentáveis.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Com experiência na área de ensino e formação em arquitetura, atua na concepção de projetos que equilibram criatividade, rigor técnico e sensibilidade ao contexto. O seu trabalho abrange diferentes escalas, desde projetos residenciais e comerciais até propostas urbanas e institucionais.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Acredita que a arquitetura deve ir além da estética, assumindo um papel fundamental na melhoria da qualidade de vida, através da criação de espaços que acolhem, inspiram e respondem às necessidades reais das pessoas.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Mais do que projetar espaços, a HAVREDESIGN cria arquitetura como refúgio.
          </p>
        </div>
        <div class="relative">
          <div class="aspect-[3/4] rounded-lg overflow-hidden shadow-lg">
            <img src="<?= e(asset('assets/janette1.jpeg')) ?>"
              alt="Arq.ª Janette Rodrigues da Conceição - Fundadora"
              width="960" height="1280"
              loading="lazy" decoding="async"
              class="object-cover w-full h-full">
          </div>
          <div class="absolute -bottom-6 -left-6 bg-secondary p-6 rounded-lg shadow-xl max-w-xs">
            <p class="font-serif text-lg text-secondary-foreground font-bold">Arq.ª Janette Rodrigues da Conceição</p>
            <p class="text-sm text-secondary-foreground/90">Fundadora da HAVREDESIGN</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mission, Vision & Values -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 gap-12">
        <div class="space-y-4 bg-card p-8 rounded-lg border border-border shadow-sm">
          <h3 class="font-serif text-2xl text-foreground">Missão</h3>
          <p class="text-muted-foreground leading-relaxed text-sm">
            Desenvolver soluções de arquitetura e design a partir da compreensão das necessidades de cada projeto,
            orientando decisões e criando espaços funcionais, acolhedores e adequados à forma como serão vividos,
            com qualidade, confiança e responsabilidade em cada etapa.
          </p>
        </div>
        <div class="space-y-4 bg-card p-8 rounded-lg border border-border shadow-sm">
          <h3 class="font-serif text-2xl text-foreground">Visão</h3>
          <p class="text-muted-foreground leading-relaxed text-sm">
            Ser uma referência em arquitetura e design em Angola, reconhecida pela excelência, pela integridade e
            pela criação de espaços que acolhem, inspiram e transformam a vida das pessoas.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Valores -->
  <section class="py-14 md:py-24 bg-muted">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16 space-y-4">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground">Valores</h2>
        <p class="text-muted-foreground max-w-2xl mx-auto">
          Os seis valores institucionais que orientam as nossas decisões e a forma como trabalhamos.
        </p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Excelência</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Rigor, dedicação, atenção ao detalhe e melhoria contínua.</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Integridade</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Honestidade, ética, transparência e responsabilidade nos compromissos assumidos.</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Humanização</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Colocar as pessoas no centro das decisões e dos projetos.</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Competência</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Valorização do conhecimento, da aprendizagem contínua e da capacidade técnica.</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Colaboração</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Cooperação, escuta ativa e construção conjunta de soluções.</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 space-y-2 shadow-sm">
          <h3 class="font-serif text-xl text-secondary">Responsabilidade</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">Consideração pelo impacto das decisões, pela viabilidade, sustentabilidade e valor gerado pelos projetos.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <h2 class="font-serif text-3xl md:text-4xl text-center mb-16">Por Que Escolher a HAVREDESIGN?</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Atendimento Personalizado</h3>
          <p class="text-primary-foreground/70 text-sm">Cada projeto é único. Dedicamos tempo para compreender as necessidades, objetivos e identidade de cada cliente.</p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Criatividade e Funcionalidade</h3>
          <p class="text-primary-foreground/70 text-sm">Desenvolvemos projetos que equilibram estética, conforto e funcionalidade, criando espaços modernos e com propósito.</p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Soluções Exclusivas</h3>
          <p class="text-primary-foreground/70 text-sm">Cada ambiente é concebido de forma estratégica e personalizada, refletindo a identidade de quem o utiliza.</p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Acompanhamento Técnico</h3>
          <p class="text-primary-foreground/70 text-sm">Estruturamos cada serviço por etapas e, quando contratado, asseguramos fiscalização e apoio técnico durante a obra.</p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Qualidade e Rigor Técnico</h3>
          <p class="text-primary-foreground/70 text-sm">Trabalhamos com profissionalismo e planeamento técnico para garantir soluções eficientes e bem executadas.</p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Compromisso com Prazos</h3>
          <p class="text-primary-foreground/70 text-sm">Valorizamos a organização e o cumprimento dos cronogramas, assegurando maior confiança e tranquilidade.</p>
        </div>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>
