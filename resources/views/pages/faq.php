<?php
/**
 * Página "Perguntas Frequentes" (FAQ).
 * Convertida de backend/resources/views/pages/faq.blade.php
 */
$titulo = 'FAQ - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Perguntas Frequentes
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Encontre respostas para as dúvidas mais comuns sobre os nossos serviços, processos e etapas de desenvolvimento dos projetos.
        </p>
      </div>
    </div>
  </section>

  <!-- FAQ Accordion -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div id="faq-accordion" class="max-w-3xl mx-auto space-y-4">
        <!-- Rendered via JS -->
      </div>
    </div>
  </section>

  <!-- Still Have Questions -->
  <section class="py-16 bg-muted">
    <div class="container mx-auto px-4">
      <div class="max-w-2xl mx-auto text-center">
        <h2 class="font-serif text-2xl md:text-3xl text-foreground mb-4">Vamos conversar sobre o seu projecto?</h2>
        <p class="text-muted-foreground mb-8">
          Entre em contacto connosco e descubra soluções arquitetónicas pensadas para as suas necessidades, estilo e objetivos.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="https://wa.me/244926184104?text=Olá!%20Tenho%20uma%20dúvida%20sobre%20os%20serviços%20da%20HAVREDESIGN."
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center px-6 py-3 bg-[#25D366] hover:bg-[#128C7E] text-white font-medium rounded-md transition-colors"
          >
            <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.301-.15-1.785-.881-2.062-.982-.276-.101-.477-.15-.678.15-.2.301-.777.982-.953 1.183-.175.201-.351.226-.652.076-.301-.15-1.274-.47-2.427-1.498-.897-.8-1.502-1.788-1.678-2.09-.175-.301-.019-.464.131-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.101-.201.05-.377-.025-.527-.075-.15-.678-1.631-.93-2.235-.244-.587-.493-.507-.678-.517-.175-.01-.376-.01-.577-.01s-.527.075-.803.377c-.276.301-1.054 1.03-1.054 2.513 0 1.482 1.079 2.912 1.229 3.113.15.201 2.124 3.243 5.145 4.547.719.31 1.28.495 1.718.634.723.23 1.381.197 1.901.12.579-.086 1.785-.728 2.036-1.431.251-.703.251-1.305.175-1.431-.075-.126-.276-.201-.577-.351z"/></svg>
            Conversar no WhatsApp
          </a>
          <a href="<?= e(rota('contacto')) ?>" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground font-medium rounded-md hover:bg-card transition-colors">
            Solicitar Contacto
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Pronto para transformar o seu espaço?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Descubra soluções arquitetónicas pensadas para unir funcionalidade, identidade e sofisticação em cada detalhe.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
          Solicitar proposta personalizada
        </a>
        <a href="<?= e(rota('agendar')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 border border-primary text-primary font-medium rounded-md hover:bg-primary/10 transition-colors">
          Agendar conversa
        </a>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const faqsList = [
        {
          question: 'Quais serviços a HAVREDESIGN oferece?',
          answer: 'Três serviços principais: Arquitetura, Design de Interiores e Fiscalização e Acompanhamento Técnico. Complementares: Consultoria Técnica, Topografia, Levantamento Técnico, Modelação 3D e Renderização, Medições e Orçamento, Licenciamento e Croqui de Localização.'
        },
        {
          question: 'O que são serviços principais?',
          answer: 'São os serviços que representam as principais áreas de atuação da HAVREDESIGN: Arquitetura, Design de Interiores e Fiscalização e Acompanhamento Técnico. Podem ser contratados separadamente ou integrados numa HAVRE Solução.'
        },
        {
          question: 'O que são serviços complementares?',
          answer: 'São serviços destinados a responder a necessidades específicas do projeto, como Consultoria Técnica, Topografia, Levantamento Técnico, Modelação 3D e Renderização, Medições e Orçamento, Licenciamento e Croqui de Localização. Também podem ser contratados separadamente ou associados a outros serviços.'
        },
        {
          question: 'O que são as HAVRE Soluções?',
          answer: 'São soluções integradas que combinam serviços principais e complementares conforme as necessidades, a complexidade e a fase de desenvolvimento de cada projeto: HAVRE Genesis, HAVRE Evolution, HAVRE Ready, HAVRE Guardian e HAVRE Prime.'
        },
        {
          question: 'Qual é o processo de trabalho da HAVREDESIGN?',
          answer: 'O processo divide-se em seis etapas: 1) Briefing — analisamos as suas necessidades, objetivos e expectativas; 2) Conceção — damos identidade ao projeto, definindo o conceito arquitetónico e a abordagem de design; 3) Desenvolvimento — elaboramos peças desenhadas, modelos tridimensionais e definimos os materiais; 4) Apresentação e Ajustes — apresentamos o projeto para sua validação e fazemos os ajustes finais acordados; 5) Acompanhamento — acompanhamos a execução da obra com fiscalização e apoio técnico; 6) Entrega — disponibilizamos todos os elementos técnicos necessários para a execução do projeto. As etapas são adaptadas à natureza do serviço e ao âmbito definido em cada proposta.'
        },
        {
          question: 'Quanto tempo leva para desenvolver um projeto?',
          answer: 'O prazo de desenvolvimento varia de acordo com a dimensão, a complexidade e o âmbito de cada contratação. Projetos residenciais podem levar entre 2 a 4 meses para desenvolvimento completo, enquanto projetos comerciais ou de maior escala poderão exigir um prazo mais alargado. O prazo é definido na proposta.'
        },
        {
          question: 'O acompanhamento da obra está incluído no projeto?',
          answer: 'A fiscalização é um serviço autónomo ou complementar e não está incluída automaticamente em todos os projetos. Pode ser contratada em conjunto com a arquitetura ou isoladamente, através da solução HAVRE Guardian.'
        },
        {
          question: 'A HAVREDESIGN executa a construção?',
          answer: 'A construção não integra a oferta ativa da empresa nesta fase. A HAVREDESIGN presta fiscalização e acompanhamento técnico quando contratados.'
        },
        {
          question: 'O estudo preliminar serve para iniciar a obra?',
          answer: 'Não. O estudo preliminar define a orientação, a dimensão e o programa do projeto, mas não substitui o projeto de execução nem as especialidades necessárias à obra. A execução exige os documentos técnicos e a documentação previstos na proposta.'
        },
        {
          question: 'Quantas revisões estão incluídas no projeto?',
          answer: 'O número de revisões consta da proposta apresentada. Alterações adicionais ou alterações fora do âmbito definido podem implicar ajustes de prazo e de honorários, sempre comunicados previamente.'
        },
        {
          question: 'Como são calculados os valores?',
          answer: 'Os valores são referenciais e dependem do âmbito, da área, da complexidade, da localização, do prazo e dos entregáveis de cada contratação. Não apresentamos preços fixos em cartões: cada proposta é definida segundo os serviços, as etapas e os recursos efetivamente contratados.'
        },
        {
          question: 'Que tipos de projetos a HAVREDESIGN realiza?',
          answer: 'Projetos residenciais, comerciais, corporativos e institucionais, em qualquer fase: desenvolvimento de raiz, transformação de espaços existentes, regularização e licenciamento e acompanhamento técnico de obra.'
        }
      ];

      const accordionContainer = document.getElementById('faq-accordion');
      if (accordionContainer) {
        accordionContainer.innerHTML = faqsList.map((faq, idx) => `
          <div class="bg-card border border-border rounded-lg px-6 overflow-hidden">
            <button
              onclick="toggleFaq(${idx})"
              class="w-full text-left font-medium py-6 flex items-center justify-between focus:outline-none"
            >
              <span class="text-foreground text-lg pr-4 font-serif">${faq.question}</span>
              <span id="faq-icon-${idx}" class="text-secondary text-xl font-bold transition-transform">+</span>
            </button>
            <div id="faq-answer-${idx}" class="hidden pb-6 text-muted-foreground leading-relaxed text-sm">
              ${faq.answer}
            </div>
          </div>
        `).join('');
      }

      window.toggleFaq = (idx) => {
        const ans = document.getElementById(`faq-answer-${idx}`);
        const icon = document.getElementById(`faq-icon-${idx}`);
        if (ans && icon) {
          const isHidden = ans.classList.contains('hidden');
          ans.classList.toggle('hidden');
          icon.textContent = isHidden ? '−' : '+';
        }
      };
    });
  </script>
<?php \App\Core\View::secaoFim(); ?>
