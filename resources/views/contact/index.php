<?php
/**
 * Contacto — dados de contacto, formulário e mapa.
 * Convertida de backend/resources/views/contact/index.blade.php
 */
$site = \App\Support\Site::site();

$cEmail   = $site['contact.email'] ?? '';
$cPhone1  = $site['contact.phone_1'] ?? '';
$cPhone2  = $site['contact.phone_2'] ?? '';
$cPhone3  = $site['contact.phone_3'] ?? '';
$cWaText  = $site['contact.whatsapp'] ?? '';
$cAddress = $site['contact.address_full'] ?? '';
$cHours   = $site['contact.hours_lines'] ?? '';
$waNumber = $site['whatsapp.number'] ?? '';

$titulo = 'Contacto - HAVREDESIGN';
$meta   = 'Entre em contacto com a HAVREDESIGN em Luanda: telefone, e-mail, morada em Benfica, Via Expressa, Talatona, horário e formulário de contacto.';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Contacto
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Entre em contacto connosco e vamos transformar a sua ideia em um espaço funcional, acolhedor e com identidade.
        </p>
      </div>
    </div>
  </section>

  <!-- Contact Info Cards -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-card border border-border rounded-lg p-6 flex flex-col items-center text-center space-y-4 shadow-sm">
          <div class="w-14 h-14 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          </div>
          <div>
            <h3 class="font-medium text-foreground mb-1">Telefone</h3>
            <div class="flex flex-col space-y-1 text-sm">
              <a href="tel:+<?= e(preg_replace('/[^0-9]/', '', $cPhone1)) ?>" class="text-muted-foreground hover:text-secondary transition-colors"><?= e($cPhone1) ?></a>
              <a href="tel:+<?= e(preg_replace('/[^0-9]/', '', $cPhone2)) ?>" class="text-muted-foreground hover:text-secondary transition-colors"><?= e($cPhone2) ?></a>
              <a href="tel:+<?= e(preg_replace('/[^0-9]/', '', $cPhone3)) ?>" class="text-muted-foreground hover:text-secondary transition-colors"><?= e($cPhone3) ?></a>
              <a href="https://wa.me/<?= e($waNumber) ?>" target="_blank" rel="noopener noreferrer" class="text-secondary font-semibold hover:underline">WhatsApp: <?= e($cWaText) ?></a>
            </div>
          </div>
        </div>

        <div class="bg-card border border-border rounded-lg p-6 flex flex-col items-center text-center space-y-4 shadow-sm">
          <div class="w-14 h-14 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <h3 class="font-medium text-foreground mb-1">E-mail</h3>
            <a href="mailto:<?= e($cEmail) ?>" class="text-muted-foreground hover:text-secondary transition-colors text-sm font-semibold">
              <?= e($cEmail) ?>
            </a>
            <p class="text-xs text-muted-foreground mt-1">Responderemos brevemente</p>
          </div>
        </div>

        <div class="bg-card border border-border rounded-lg p-6 flex flex-col items-center text-center space-y-4 shadow-sm">
          <div class="w-14 h-14 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div>
            <h3 class="font-medium text-foreground mb-1">Endereço</h3>
            <p class="text-muted-foreground text-sm">
              <?= e($cAddress) ?>
            </p>
          </div>
        </div>

        <div class="bg-card border border-border rounded-lg p-6 flex flex-col items-center text-center space-y-4 shadow-sm">
          <div class="w-14 h-14 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <h3 class="font-medium text-foreground mb-1">Horário</h3>
            <p class="text-muted-foreground text-sm">
              <?= nl2br(e($cHours)) ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form & Map -->
  <section class="py-16 bg-muted">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-12">
        <!-- Form -->
        <div class="space-y-6">
          <div>
            <h2 class="font-serif text-2xl md:text-3xl text-foreground mb-2">Vamos conversar sobre o seu projeto</h2>
            <p class="text-muted-foreground">
              Partilhe connosco a sua ideia, necessidade ou dúvida. A nossa equipa analisará o seu pedido e responderá com a maior brevidade possível.
            </p>
          </div>

<?= \App\Core\View::parcial('partials/flash') ?>
  <form id="contact-form" method="POST" action="<?= e(rota('contacto')) ?>" class="space-y-6">
            <?= csrf_campo() ?>
            <!-- Honeypot: invisível a humanos, apanha bots (backend.md 6.4) -->
            <div class="hidden" aria-hidden="true">
              <label for="homepage">Homepage</label>
              <input type="text" id="homepage" name="homepage" tabindex="-1" autocomplete="off" />
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
              <div class="form-group">
                <label for="name" class="form-label">Nome *</label>
                <input
                  id="name"
                  name="name"
                  type="text"
                  required
                  value="<?= e(velho('name')) ?>"
                  placeholder="Seu nome"
                  class="form-input"
                />
                <?php if (erro_de('name')): ?><p class="form-error"><?= e(erro_de('name')) ?></p><?php endif; ?>
              </div>
              <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  value="<?= e(velho('email')) ?>"
                  placeholder="seu@email.com"
                  class="form-input"
                />
                <?php if (erro_de('email')): ?><p class="form-error"><?= e(erro_de('email')) ?></p><?php endif; ?>
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
              <div class="form-group">
                <label for="phone" class="form-label">Contacto Telefónico</label>
                <input
                  id="phone"
                  name="phone"
                  type="text"
                  value="<?= e(velho('phone')) ?>"
                  placeholder="+244 926 184 104"
                  class="form-input"
                />
                <?php if (erro_de('phone')): ?><p class="form-error"><?= e(erro_de('phone')) ?></p><?php endif; ?>
              </div>
              <div class="form-group">
                <label for="subject" class="form-label">Assunto *</label>
                <input
                  id="subject"
                  name="subject"
                  type="text"
                  required
                  value="<?= e(velho('subject')) ?>"
                  placeholder="Ex: Orçamento para projeto residencial"
                  class="form-input"
                />
                <?php if (erro_de('subject')): ?><p class="form-error"><?= e(erro_de('subject')) ?></p><?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label for="message" class="form-label">Mensagem *</label>
              <textarea
                id="message"
                name="message"
                required
                rows="5"
                placeholder="Fale-nos sobre o seu projeto, necessidades ou objetivos para o espaço."
                class="form-textarea"
              ><?= e(velho('message')) ?></textarea>
              <?php if (erro_de('message')): ?><p class="form-error"><?= e(erro_de('message')) ?></p><?php endif; ?>
            </div>

            <button
              type="submit"
              class="inline-flex items-center justify-center px-8 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors"
            >
              Entrar em contacto
              <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </form>
        </div>

        <!-- Map -->
        <div class="space-y-6">
          <div class="rounded-lg overflow-hidden bg-card border border-border shadow-sm">
            <iframe
              title="Mapa — HAVREDESIGN, Benfica, Via Expressa, Talatona, Luanda"
              src="https://www.google.com/maps?q=Benfica+Via+Expressa+Bairro+Tchinguari+Rua+1+proximo+a+Administracao+do+Talatona+Municipio+de+Talatona+Luanda+Angola&z=15&output=embed"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              allowfullscreen
              class="block w-full h-[400px] lg:h-[520px] border-0"
            ></iframe>
          </div>

          <div class="bg-card border border-border rounded-lg p-6 space-y-3 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 shrink-0 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div class="space-y-2">
                <h3 class="font-serif text-xl text-foreground">Onde Estamos</h3>
                <p class="text-muted-foreground text-sm">
                  <?= e($cAddress) ?>
                </p>
                <a
                  href="https://maps.google.com/?q=Benfica+Via+Expressa+Tchinguari+Talatona+Luanda+Angola"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center text-secondary font-semibold hover:underline"
                >
                  Como chegar no Google Maps →
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Actions -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="font-serif text-2xl md:text-3xl text-foreground mb-2">Outras Formas de Contacto</h2>
        <p class="text-muted-foreground">Escolha a forma mais conveniente para conversar connosco sobre o seu projeto.</p>
      </div>

      <div class="grid md:grid-cols-2 gap-6 max-w-2xl mx-auto">
        <a
          href="https://wa.me/<?= e($waNumber) ?>"
          target="_blank"
          rel="noopener noreferrer"
          class="block p-6 rounded-lg bg-[#25D366] text-white hover:bg-[#128C7E] transition-all hover:scale-105 shadow-sm"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.301-.15-1.785-.881-2.062-.982-.276-.101-.477-.15-.678.15-.2.301-.777.982-.953 1.183-.175.201-.351.226-.652.076-.301-.15-1.274-.47-2.427-1.498-.897-.8-1.502-1.788-1.678-2.09-.175-.301-.019-.464.131-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.101-.201.05-.377-.025-.527-.075-.15-.678-1.631-.93-2.235-.244-.587-.493-.507-.678-.517-.175-.01-.376-.01-.577-.01s-.527.075-.803.377c-.276.301-1.054 1.03-1.054 2.513 0 1.482 1.079 2.912 1.229 3.113.15.201 2.124 3.243 5.145 4.547.719.31 1.28.495 1.718.634.723.23 1.381.197 1.901.12.579-.086 1.785-.728 2.036-1.431.251-.703.251-1.305.175-1.431-.075-.126-.276-.201-.577-.351z"/></svg>
            </div>
            <div>
              <h3 class="font-medium text-lg">WhatsApp</h3>
              <p class="text-white/80 text-sm">
                Fale connosco de forma rápida e prática para esclarecer dúvidas.
              </p>
            </div>
          </div>
        </a>

        <a
          href="<?= e(rota('agendar')) ?>"
          class="block p-6 rounded-lg bg-secondary text-secondary-foreground hover:bg-secondary/90 transition-all hover:scale-105 shadow-sm"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                  <h3 class="font-medium text-lg">Agendar Consultoria</h3>
                </div>
          </div>
        </a>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>
