<?php
/**
 * Política de Privacidade.
 * Convertida de backend/resources/views/legal/privacy.blade.php
 */
$titulo = 'Política de Privacidade - HAVREDESIGN';
$meta   = 'Política de Privacidade da HAVREDESIGN — como recolhemos, usamos e protegemos os seus dados pessoais.';
$meta_description = $meta; // o layout base lê $meta_description
$site   = \App\Support\Site::site();

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero -->
  <section class="py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4 max-w-3xl">
      <span class="text-secondary font-semibold text-sm uppercase tracking-wider">Legal</span>
      <h1 class="font-serif text-4xl md:text-5xl leading-tight mt-2 mb-4">Política de Privacidade</h1>
      <p class="text-primary-foreground/80">
        A sua confiança importa. Explicamos aqui, de forma simples, que dados recolhemos no site HAVREDESIGN, para que os usamos e como pode exercer os seus direitos.
      </p>
      <p class="text-sm text-primary-foreground/60 mt-4">Última atualização: 25 de setembro de 2026</p>
    </div>
  </section>

  <!-- Conteúdo -->
  <div class="py-14 md:py-20">
    <div class="container mx-auto px-4 max-w-3xl space-y-10">

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">1. Responsável pelo tratamento</h2>
        <p class="text-muted-foreground leading-relaxed">
          A HAVREDESIGN é responsável pelo tratamento dos dados pessoais recolhidos através deste site.
          Sede: Benfica, Via Expressa, Bairro Tchinguari, Rua 1, próximo à Administração do Talatona, Município de Talatona, Luanda, Angola.
          E-mail: <a href="mailto:<?= e($site['contact.email'] ?? '') ?>" class="text-secondary font-semibold hover:underline"><?= e($site['contact.email'] ?? '') ?></a>.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">2. Dados que recolhemos</h2>
        <p class="text-muted-foreground leading-relaxed">Recolhemos apenas os dados necessários para responder ao seu pedido:</p>
        <ul class="list-disc pl-6 space-y-1 text-muted-foreground">
          <li><strong class="text-foreground">Formulário de contacto:</strong> nome, e-mail, contacto telefónico, assunto e mensagem.</li>
          <li><strong class="text-foreground">Solicitação de projeto:</strong> nome, contactos, localização e características do projeto (área, estado, serviço e segmento), preferências de contacto e ficheiros que decida anexar.</li>
          <li><strong class="text-foreground">Agendamento de reunião:</strong> nome, e-mail, telefone, tipo de reunião, data, horário, endereço (em visita ao local) e observações.</li>
          <li><strong class="text-foreground">Conta de cliente:</strong> nome, e-mail e palavra-passe (guardada de forma cifrada).</li>
        </ul>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">3. Para que usamos os dados</h2>
        <ul class="list-disc pl-6 space-y-1 text-muted-foreground">
          <li>Responder a pedidos de contacto e enviar propostas ou orçamentos;</li>
          <li>Gerir marcações de reuniões e prestar os serviços contratados;</li>
          <li>Gerir a sua conta e a área de cliente;</li>
          <li>Cumprir obrigações legais e contratuais;</li>
          <li>Melhorar o site e a qualidade do atendimento.</li>
        </ul>
        <p class="text-muted-foreground leading-relaxed">
          A base legal do tratamento é o seu <strong class="text-foreground">consentimento</strong> (ao preencher e submeter um formulário), o <strong class="text-foreground">cumprimento de medidas pré-contratuais</strong> (pedido de proposta) e o <strong class="text-foreground">interesse legítimo</strong> em prestar o serviço e cumprir a lei.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">4. Partilha de dados</h2>
        <p class="text-muted-foreground leading-relaxed">
          Não vendemos os seus dados. Partilhamo-los apenas com prestadores de serviços que apoiam a nossa atividade (alojamento do site, correio eletrónico e ferramentas de gestão), sempre mediante obrigações de confidencialidade, e com autoridades quando a lei o exija.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">5. Conservação</h2>
        <p class="text-muted-foreground leading-relaxed">
          Os dados são conservados apenas pelo tempo necessário à finalidade para que foram recolhidos e, depois, pelos prazos exigidos por lei. Pode pedir a eliminação dos seus dados a qualquer momento, salvo obrigações legais de conservação.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">6. Cookies e armazenamento local</h2>
        <p class="text-muted-foreground leading-relaxed">
          Este site utiliza apenas armazenamento local do seu navegador para guardar preferências de navegação e sessão iniciada no dispositivo. Não usamos cookies de publicidade nem de terceiros para fins comerciais.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">7. Os seus direitos</h2>
        <p class="text-muted-foreground leading-relaxed">Pode, a qualquer momento, solicitar:</p>
        <ul class="list-disc pl-6 space-y-1 text-muted-foreground">
          <li>Acesso aos dados que temos sobre si;</li>
          <li>Retificação de dados inexatos ou incompletos;</li>
          <li>Apagamento dos seus dados;</li>
          <li>Oposição ou limitação do tratamento;</li>
          <li>Receber os dados que forneceu, em formato estruturado.</li>
        </ul>
        <p class="text-muted-foreground leading-relaxed">
          Para exercer estes direitos, envie pedido para <a href="mailto:<?= e($site['contact.email'] ?? '') ?>" class="text-secondary font-semibold hover:underline"><?= e($site['contact.email'] ?? '') ?></a>, indicando o seu nome e o direito que pretende exercer. Respondemos no prazo máximo de 30 dias.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">8. Segurança</h2>
        <p class="text-muted-foreground leading-relaxed">
          Aplicamos medidas técnicas e organizativas adequadas para proteger os dados contra acesso não autorizado, alteração, perda ou destruição. Nenhum sistema é totalmente imune, mas tratamos a segurança como prioridade.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">9. Menores</h2>
        <p class="text-muted-foreground leading-relaxed">
          Os serviços do site destinam-se a maiores de 18 anos. Se detetarmos que recolhemos dados de um menor sem consentimento de quem tem a responsabilidade, eliminamos essa informação.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="font-serif text-2xl text-foreground">10. Alterações e contacto</h2>
        <p class="text-muted-foreground leading-relaxed">
          Esta política pode ser atualizada; a data de última atualização é sempre indicada no topo da página. Em caso de dúvidas, contacte-nos por e-mail para <a href="mailto:<?= e($site['contact.email'] ?? '') ?>" class="text-secondary font-semibold hover:underline"><?= e($site['contact.email'] ?? '') ?></a> ou através do WhatsApp <a href="https://wa.me/244926184104" target="_blank" rel="noopener noreferrer" class="text-secondary font-semibold hover:underline"><?= e($site['contact.phone_1'] ?? '') ?></a>.
        </p>
        <p class="text-muted-foreground leading-relaxed">
          Consulte também os nossos <a href="<?= e(rota('termos')) ?>" class="text-secondary font-semibold hover:underline">Termos de Uso</a>.
        </p>
      </section>

      <div class="pt-6 border-t border-border flex flex-wrap gap-4">
        <a href="<?= e(rota('home')) ?>" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground font-medium rounded-md hover:bg-muted transition-colors">← Voltar ao site</a>
        <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors">Solicitar proposta personalizada</a>
      </div>
    </div>
  </div>
<?php \App\Core\View::secaoFim(); ?>
