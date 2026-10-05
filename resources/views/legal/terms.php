<?php
/**
 * Termos de Uso.
 * Convertida de backend/resources/views/legal/terms.blade.php
 */
$titulo = 'Termos de Uso - HAVREDESIGN';
$meta   = 'Termos de Uso do site HAVREDESIGN — condições de utilização, propriedade intelectual e responsabilidades.';
$meta_description = $meta; // o layout base lê $meta_description
$site   = \App\Support\Site::site();

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero -->
  <section class="py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4 max-w-3xl">
      <span class="text-secondary font-semibold text-sm uppercase tracking-wider">Legal</span>
      <h1 class="font-serif text-4xl md:text-5xl leading-tight mt-2 mb-4">Termos de Uso</h1>
      <p class="text-primary-foreground/80">
        Condições de utilização do site HAVREDESIGN: o que pode e o que não pode fazer com o conteúdo aqui publicado, e o que pode esperar de nós.
      </p>
      <p class="text-sm text-primary-foreground/60 mt-4">Última atualização: 25 de setembro de 2026</p>
    </div>
  </section>

  <!-- Conteúdo -->
  <div class="py-14 md:py-20">
    <div class="container mx-auto px-4 max-w-3xl space-y-10">

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">1. Aceitação dos termos</h2>
        <p class="text-muted-foreground leading-relaxed">
          Ao aceder e utilizar este site, o utilizador declara ter lido e aceite os presentes Termos de Uso. Se não concordar, deve abster-se de utilizar o site. A utilização continuada constitui aceitação das condições em vigor.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">2. Objeto do site</h2>
        <p class="text-muted-foreground leading-relaxed">
          O site tem finalidade exclusivamente informativa e institucional: apresentar os serviços da HAVREDESIGN, o nosso processo de trabalho, o portefólio e os meios de contacto. Os conteúdos aqui disponibilizados <strong class="text-foreground">não constituem proposta comercial</strong> nem substituem documento contratual.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">3. Propostas, orçamentos e contratos</h2>
        <ul class="list-disc pl-6 space-y-1 text-muted-foreground">
          <li>Os valores, âmbitos e prazos de qualquer serviço são definidos exclusivamente em <strong class="text-foreground">proposta escrita</strong> personalizada ao projeto.</li>
          <li>Os pedidos submetidos através dos formulários do site têm carácter de <strong class="text-foreground">pedido de informação</strong> e não obrigam nenhuma das partes.</li>
          <li>O contrato considera-se celebrado apenas após aceitação expressa da proposta e formalização com a HAVREDESIGN.</li>
          <li>As HAVRE Soluções (Genesis, Evolution, Ready, Guardian e Prime) são enquadramentos de referência; o conteúdo detalhado de cada solução consta da proposta.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">4. Propriedade intelectual</h2>
        <p class="text-muted-foreground leading-relaxed">
          Todo o conteúdo do site — logótipo, textos, imagens, ilustrações, desenhos, código e identidade gráfica — é propriedade da HAVREDESIGN ou usado com autorização dos titulares. É proibida a reprodução, distribuição, transformação ou exploração comercial, total ou parcial, sem autorização prévia e escrita.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">5. Utilização aceitável</h2>
        <p class="text-muted-foreground leading-relaxed">O utilizador compromete-se a:</p>
        <ul class="list-disc pl-6 space-y-1 text-muted-foreground">
          <li>Utilizar o site para fins lícitos e legítimos;</li>
          <li>Não tentar comprometer a segurança, disponibilidade ou integridade do site;</li>
          <li>Não introduzir vírus, código malicioso ou qualquer elemento que possa danificar os sistemas;</li>
          <li>Não utilizar conteúdos do site de forma a induzir a erro terceiros quanto à autoria ou à origem dos trabalhos apresentados;</li>
          <li>Fornecer informações verdadeiras e atualizadas nos formulários.</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">6. Links externos</h2>
        <p class="text-muted-foreground leading-relaxed">
          O site pode conter ligações para páginas de terceiros (por exemplo, redes sociais ou serviços de mapas). A HAVREDESIGN não controla nem é responsável pelo conteúdo, disponibilidade ou políticas dessas páginas.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">7. Responsabilidade</h2>
        <p class="text-muted-foreground leading-relaxed">
          Procuramos manter o conteúdo correto e atualizado, mas não garantimos ausência total de erros ou interrupções. Na máxima extensão permitida pela lei, a HAVREDESIGN não responde por danos indiretos decorrentes da utilização ou da impossibilidade de utilização do site. As imagens de portefólio e conteúdos de demonstração são ilustrativos.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">8. Privacidade</h2>
        <p class="text-muted-foreground leading-relaxed">
          O tratamento dos dados pessoais é descrito na nossa <a href="<?= e(rota('privacidade')) ?>" class="text-secondary font-semibold hover:underline">Política de Privacidade</a>, que integra os presentes termos.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">9. Alterações</h2>
        <p class="text-muted-foreground leading-relaxed">
          A HAVREDESIGN pode alterar estes termos a qualquer momento. As alterações entram em vigor na data de publicação nesta página, que indica sempre a última atualização.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">10. Lei aplicável e contacto</h2>
        <p class="text-muted-foreground leading-relaxed">
          Estes termos regem-se pela lei angolana. Para qualquer questão, contacte-nos através do e-mail <a href="mailto:<?= e($site['contact.email'] ?? '') ?>" class="text-secondary font-semibold hover:underline"><?= e($site['contact.email'] ?? '') ?></a> ou pelo WhatsApp <a href="https://wa.me/244926184104" target="_blank" rel="noopener noreferrer" class="text-secondary font-semibold hover:underline"><?= e($site['contact.phone_1'] ?? '') ?></a>.
        </p>
        <p class="text-muted-foreground leading-relaxed">
          Morada: Benfica, Via Expressa, Bairro Tchinguari, Rua 1, próximo à Administração do Talatona, Município de Talatona, Luanda, Angola.
        </p>
      </section>

      <div class="pt-6 border-t border-border flex flex-wrap gap-4">
        <a href="<?= e(rota('home')) ?>" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground font-medium rounded-md hover:bg-muted transition-colors">← Voltar ao site</a>
        <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors">Solicitar proposta personalizada</a>
      </div>
    </div>
  </div>
<?php \App\Core\View::secaoFim(); ?>
