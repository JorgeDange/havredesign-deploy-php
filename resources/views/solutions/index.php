<?php
/**
 * HAVRE Soluções — grelha de soluções integradas.
 * Convertida de backend/resources/views/solutions/index.blade.php
 */
$titulo = 'HAVRE Soluções - HAVREDESIGN';
$meta   = 'Soluções integradas da HAVREDESIGN, adaptadas às necessidades e às diferentes etapas de cada projeto: arquitetura, transformação de espaços, licenciamento, fiscalização e solução exclusiva.';

\App\Core\View::layout('layouts/app');

$solutions = \App\Core\Database::todos(
    'SELECT * FROM solutions WHERE active = 1 ORDER BY sort_order'
);

// O badge de categoria do ui/orcamento.html não tem coluna própria —
// deriva-se do `code` da BD (com fallback para o próprio code).
$solutionBadges = [
    'GENESIS'  => 'Arquitetura',
    'EVOLUTION' => 'Transformação',
    'READY'    => 'Licenciamento',
    'GUARDIAN' => 'Acompanhamento',
    'PRIME'    => 'Exclusiva',
];

\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-12 md:py-20 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          HAVRE Soluções
        </h1>
        <p class="text-lg md:text-xl text-primary-foreground/80 max-w-2xl">
          Soluções integradas, adaptadas às necessidades e às diferentes etapas de cada projeto.
        </p>
      </div>
    </div>
  </section>

  <!-- Solutions Grid -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php foreach ($solutions as $solution): ?>
          <?php
            $parts = explode("\n\n", $solution['description'], 2);
            $solutionText = trim($parts[0]);
            $solutionLines = isset($parts[1])
                ? array_values(array_filter(array_map('trim', explode("\n", $parts[1])), fn ($line) => $line !== ''))
                : [];
            $badge = $solutionBadges[$solution['code']] ?? $solution['code'];
            $panelId = 'sol-' . $solution['slug'];
          ?>

          <!-- <?= e($solution['name']) ?> -->
          <div id="solucao-<?= e($solution['slug']) ?>" class="bg-card border border-border rounded-lg p-6 flex flex-col shadow-sm">
            <div class="flex-1">
              <span class="inline-block text-xs font-semibold uppercase tracking-wider text-secondary bg-secondary/10 px-2.5 py-1 rounded mb-4"><?= e($badge) ?></span>
              <h2 class="font-serif text-xl text-foreground mb-2"><?= e($solution['name']) ?></h2>
              <p class="text-sm text-muted-foreground leading-relaxed mb-4">
                <?= e($solutionText) ?>
              </p>
              <div class="mb-4">
                <span class="text-base font-semibold text-secondary">Proposta personalizada</span>
              </div>
            </div>
            <div class="space-y-2 mt-auto">
              <button type="button" onclick="toggleSolution('<?= e($panelId) ?>')" class="w-full py-2.5 px-4 border border-border rounded-md text-sm font-medium hover:bg-muted transition-colors flex items-center justify-center gap-2">
                <span>Conhecer solução</span>
                <span id="<?= e($panelId) ?>-arrow">▼</span>
              </button>
              <div id="<?= e($panelId) ?>" class="hidden mt-4 pt-4 border-t border-border space-y-2 text-sm text-muted-foreground">
                <?php foreach ($solutionLines as $line): ?>
                  <?php if (str_starts_with($line, '✓')): ?>
                    <p><?= e($line) ?></p>
                  <?php else: ?>
                    <p class="text-xs italic"><?= e($line) ?></p>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
              <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex w-full items-center justify-center py-2.5 px-4 bg-secondary text-secondary-foreground rounded-md text-sm font-medium hover:bg-secondary/90 transition-colors">
                <?= e($solution['cta_label']) ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>

      </div>

      <!-- Notice -->
      <div class="mt-12 bg-muted p-6 rounded-lg border border-border text-sm text-muted-foreground space-y-2">
        <h2 class="font-serif text-lg text-foreground font-semibold">Observações Importantes</h2>
        <p>• Os honorários são calculados segundo os serviços, as etapas e os recursos efetivamente contratados.</p>
        <p>• Cada proposta é definida de acordo com o âmbito, a complexidade, a localização e o prazo do projeto.</p>
        <p>• Deslocações fora da província de Luanda poderão implicar custos complementares, confirmados previamente.</p>
        <p>• O início dos trabalhos está sujeito à aceitação da proposta e à formalização da contratação.</p>
      </div>

      <!-- CTA -->
      <div class="text-center mt-12">
        <h2 class="font-serif text-2xl text-foreground mb-4">Solicite uma proposta personalizada</h2>
        <p class="text-muted-foreground mb-6 max-w-xl mx-auto">
          Entre em contacto connosco para uma análise detalhada do seu projeto e descubra a solução mais adequada.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="<?= e(rota('solicitar-projeto')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors">
            Solicitar proposta personalizada
          </a>
          <a href="<?= e(rota('agendar')) ?>" class="inline-flex items-center justify-center px-8 py-3.5 border border-border text-foreground font-medium rounded-md hover:bg-card transition-colors">
            Agendar conversa
          </a>
        </div>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
  <script>
    function toggleSolution(id) {
      const el = document.getElementById(id);
      const arrow = document.getElementById(`${id}-arrow`);
      if (el) {
        const isHidden = el.classList.contains('hidden');
        el.classList.toggle('hidden');
        if (arrow) arrow.textContent = isHidden ? '▲' : '▼';
      }
    }
  </script>
<?php \App\Core\View::secaoFim(); ?>
