<?php
/**
 * Footer — rodapé partilhado (equivalente a partials/footer.blade.php).
 */
$site = \App\Support\Site::site();
?>
<footer class="bg-primary text-primary-foreground">
  <div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
      <!-- Marca -->
      <div class="space-y-4">
        <div class="flex flex-col">
          <a href="<?= rota('home') ?>" class="flex items-center gap-2">
            <img src="<?= e(\App\Support\Site::imagem('logo.png', 'LOGO-HAVREDESIGN.jpeg')) ?>"
                 alt="Logo HavreDesign" width="120" height="60"
                 class="h-10 w-auto filter brightness-0 invert">
          </a>
        </div>
        <p class="text-sm text-primary-foreground/80 leading-relaxed">
          <?= e($site['brand.legal_name'] ?? 'HAVREDESIGN') ?> — Arquitetura como refúgio. Criamos ambientes que acolhem, inspiram e transformam a forma como as pessoas vivem os espaços.
        </p>
        <div class="flex gap-4 pt-2">
          <a href="<?= e($site['social.instagram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="hover:text-secondary transition-colors hover:scale-110 transform">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </a>
          <a href="<?= e($site['social.facebook'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="hover:text-secondary transition-colors hover:scale-110 transform">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.592 0 9 1.583 9 4.615V8z"/></svg>
          </a>
          <a href="<?= e($site['social.linkedin'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="hover:text-secondary transition-colors hover:scale-110 transform">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.156z"/></svg>
          </a>
          <a href="https://wa.me/<?= e($site['whatsapp.number'] ?? '244926184104') ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="hover:text-secondary transition-colors hover:scale-110 transform">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.301-.15-1.785-.881-2.062-.982-.276-.101-.477-.15-.678.15-.2.301-.777.982-.953 1.183-.175.201-.351.226-.652.076-.301-.15-1.274-.47-2.427-1.498-.897-.8-1.502-1.788-1.678-2.09-.175-.301-.019-.464.131-.614.136-.135.301-.351.451-.527.15-.175.201-.301.301-.502.101-.201.05-.377-.025-.527-.075-.15-.678-1.631-.93-2.235-.244-.587-.493-.507-.678-.517-.175-.01-.376-.01-.577-.01s-.527.075-.803.377c-.276.301-1.054 1.03-1.054 2.513 0 1.482 1.079 2.912 1.229 3.113.15.201 2.124 3.243 5.145 4.547.719.31 1.28.495 1.718.634.723.23 1.381.197 1.901.12.579-.086 1.785-.728 2.036-1.431.251-.703.251-1.305.175-1.431-.075-.126-.276-.201-.577-.351z"/></svg>
          </a>
        </div>
      </div>

      <!-- Links rápidos -->
      <div class="space-y-4">
        <h3 class="font-serif text-lg font-semibold">Links Rápidos</h3>
        <nav class="flex flex-col gap-2">
          <a href="<?= rota('sobre') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Sobre Nós</a>
          <a href="<?= rota('servicos') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Serviços</a>
          <a href="<?= rota('portfolio') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Portefólio</a>
          <a href="<?= rota('processo') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Processo de Trabalho</a>
          <a href="<?= rota('faq') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Perguntas Frequentes</a>
          <a href="<?= rota('havre-solucoes') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">HAVRE Soluções</a>
          <a href="<?= rota('networking') ?>" class="text-sm text-primary-foreground/60 hover:text-secondary transition-colors">Networking</a>
        </nav>
      </div>

      <!-- Serviços -->
      <div class="space-y-4">
        <h3 class="font-serif text-lg font-semibold">Serviços</h3>
        <nav class="flex flex-col gap-2">
          <a href="<?= rota('servicos') ?>#arquitetura" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Projeto Arquitetónico</a>
          <a href="<?= rota('servicos') ?>#interiores" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Design de Interiores</a>
          <a href="<?= rota('servicos') ?>#fiscalizacao" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Fiscalização e Acompanhamento Técnico</a>
          <a href="<?= rota('solicitar-projeto') ?>" class="text-sm text-primary-foreground/80 hover:text-secondary transition-colors">Solicitar proposta personalizada</a>
        </nav>
      </div>

      <!-- Contacto -->
      <div class="space-y-4">
        <h3 class="font-serif text-lg font-semibold">Contacto</h3>
        <div class="flex flex-col gap-3">
          <a href="tel:<?= e(preg_replace('/[^0-9]/', '', $site['contact.phone_1'] ?? '')) ?>" class="flex items-center gap-3 text-sm text-primary-foreground/80 hover:text-secondary transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <?= e($site['contact.phone_1'] ?? '') ?>
          </a>
          <a href="mailto:<?= e($site['contact.email'] ?? '') ?>" class="flex items-center gap-3 text-sm text-primary-foreground/80 hover:text-secondary transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <?= e($site['contact.email'] ?? '') ?>
          </a>
          <div class="flex items-start gap-3 text-sm text-primary-foreground/80">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span><?= e($site['contact.address_full'] ?? '') ?></span>
          </div>
          <div class="flex items-center gap-3 text-sm text-primary-foreground/80">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= e($site['contact.hours'] ?? '') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-12 pt-8 border-t border-primary-foreground/20 flex flex-col md:flex-row justify-between items-center gap-4">
      <p class="text-sm text-primary-foreground/60">
        <?= date('Y') ?> <?= e($site['brand.name'] ?? 'HAVREDESIGN') ?>. Todos os direitos reservados.
      </p>
      <div class="flex gap-6">
        <a href="<?= rota('privacidade') ?>" class="text-sm text-primary-foreground/60 hover:text-secondary transition-colors">
          Política de Privacidade
        </a>
        <a href="<?= rota('termos') ?>" class="text-sm text-primary-foreground/60 hover:text-secondary transition-colors">
          Termos de Uso
        </a>
      </div>
    </div>
  </div>
</footer>
