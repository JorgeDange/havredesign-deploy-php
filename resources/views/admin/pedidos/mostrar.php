<?php
/**
 * Admin — detalhe de um pedido de orçamento + anexos.
 * Espera do controller: $pedido, $estados, $anejos, $servico, $solucao, $conta.
 */
$titulo = 'Pedido de orçamento - Administração HAVREDESIGN';
$tituloAdmin = 'Pedido de orçamento';
$subtituloAdmin = 'Ref. ' . strtoupper(substr((string) $pedido['id'], 0, 8))
    . ' — submetido em ' . formatar_data((string) $pedido['created_at'], 'd/m/Y H:i') . '.';

$badges = [
    'NEW'         => 'bg-primary/10 text-primary',
    'IN_REVIEW'   => 'bg-muted text-muted-foreground',
    'APPROVED'    => 'bg-primary/10 text-primary',
    'IN_PROGRESS' => 'bg-muted text-muted-foreground',
    'COMPLETED'   => 'bg-primary/10 text-primary',
    'REJECTED'    => 'bg-destructive/10 text-destructive',
];
$canais = ['email' => 'E-mail', 'phone' => 'Telefone', 'whatsapp' => 'WhatsApp'];

$tamanho = static function (int $bytes): string {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 0, ',', '.') . ' KB';
    }

    return $bytes . ' B';
};

$base   = rota('admin.pedidos');
$rotaUm = $base . '/' . rawurlencode((string) $pedido['id']);

$existe  = static fn (mixed $v): bool => $v !== null && trim((string) $v) !== '';
$outra   = static fn (mixed $v, string $padrao = '—'): string => $existe($v) ? (string) $v : $padrao;
$quando   = static fn (mixed $v, string $formato = 'd/m/Y H:i'): string => $existe($v) ? formatar_data((string) $v, $formato) : '—';
$statusActual = (string) (velho('status', (string) $pedido['status']) ?: $pedido['status']);

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'pedidos']); ?>

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="<?= e($base) ?>" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= e($badges[$pedido['status']] ?? 'bg-muted text-muted-foreground') ?>">
    <?= e($estados[$pedido['status']] ?? $pedido['status']) ?>
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Cliente e contactos</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Nome</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['user_name'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</p>
          <p class="text-sm font-medium text-foreground mt-0.5 break-all"><?= e($outra($pedido['user_email'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['user_phone'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Conta no site</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($conta !== null && trim((string) $conta) !== '' ? (string) $conta : 'Sem conta associada') ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Canal preferido</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($canais[$pedido['preferred_channel']] ?? ($pedido['preferred_channel'] !== null && $pedido['preferred_channel'] !== '' ? (string) $pedido['preferred_channel'] : '—')) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Horário preferido</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['preferred_time'])) ?></p>
        </div>
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Projeto</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Tipo de projeto</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['project_type'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Localização</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['location'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Área aproximada</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($existe($pedido['area_approx']) ? $pedido['area_approx'] . ' m²' : '—') ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Estado do projeto</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['project_stage'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Serviço pretendido</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($servico !== null && trim((string) $servico) !== '' ? (string) $servico : '—') ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">HAVRE Solução pretendida</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($solucao !== null && trim((string) $solucao) !== '' ? (string) $solucao : '—') ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Segmento</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['segment'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Orçamento previsto</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['budget'])) ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Prazo desejado</p>
          <p class="text-sm font-medium text-foreground mt-0.5"><?= e($outra($pedido['timeline'])) ?></p>
        </div>
      </div>

      <div class="mt-5 pt-5 border-t border-border">
        <p class="text-xs uppercase tracking-wide text-muted-foreground">Descrição do projeto</p>
        <p class="text-sm text-foreground/80 mt-2 whitespace-pre-wrap"><?= e($outra($pedido['description'])) ?></p>
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-foreground">Anexos</h2>
        <span class="text-xs text-muted-foreground"><?= count($anejos) ?> ficheiro(s)</span>
      </div>
      <?php if ($anejos === []): ?>
        <p class="text-sm text-muted-foreground">Este pedido não tem anexos.</p>
      <?php else: ?>
        <ul class="divide-y divide-border">
          <?php foreach ($anejos as $anejo): ?>
            <?php
            $mime      = strtolower(trim((string) $anejo['mime_type']));
            $visivel   = str_starts_with($mime, 'image/') || $mime === 'application/pdf';
            $urlBaixa  = $rotaUm . '/anexos/' . rawurlencode((string) $anejo['id']);
            $urlVer    = $urlBaixa . '/ver';
            ?>
            <li class="py-3 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-3">
              <div class="min-w-0 flex items-center gap-3">
                <span class="shrink-0 w-9 h-9 rounded-md bg-muted border border-border flex items-center justify-center text-muted-foreground" aria-hidden="true">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 1113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </span>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-foreground truncate"><?= e($anejo['original_name']) ?></p>
                  <p class="text-xs text-muted-foreground"><?= e($anejo['mime_type']) ?> · <?= e($tamanho((int) $anejo['size_bytes'])) ?></p>
                </div>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <?php if ($visivel): ?>
                  <button type="button"
                          data-anexo-ver="<?= e($urlVer) ?>"
                          data-anexo-baixar="<?= e($urlBaixa) ?>"
                          data-anexo-nome="<?= e($anejo['original_name']) ?>"
                          data-anexo-tipo="<?= $mime !== '' && str_starts_with($mime, 'image/') ? 'imagem' : 'documento' ?>"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-border bg-card text-xs font-medium text-foreground hover:bg-muted transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Ver
                  </button>
                <?php endif; ?>
                <a href="<?= e($urlBaixa) ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary text-secondary-foreground text-xs font-medium hover:bg-secondary/90 transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                  Baixar
                </a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <p class="text-xs text-muted-foreground mt-4">Ficheiros privados: só são servidos através do painel, com verificação de sessão de administrador.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Estado do pedido</h2>
      <p class="text-xs text-muted-foreground mb-4">Ao alterar o estado, o pedido passa a aparecer filtrado nessa aba.</p>

      <form method="POST" action="<?= e($rotaUm) ?>" class="space-y-4">
        <?= csrf_campo() ?>

        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
          <select id="status" name="status" required
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
            <?php foreach ($estados as $codigo => $rotulo): ?>
              <option value="<?= e($codigo) ?>" <?= $statusActual === $codigo ? 'selected' : '' ?>><?= e($rotulo) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (erro_de('status')): ?>
            <p class="text-xs text-destructive mt-1"><?= e(erro_de('status')) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="w-full px-6 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
          Guardar estado
        </button>
      </form>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Registo</h2>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Recebido</dt>
          <dd class="text-foreground font-medium text-right"><?= e($quando($pedido['created_at'])) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Consentimento RGPD</dt>
          <dd class="text-foreground font-medium text-right"><?= e($quando($pedido['privacy_consented_at'])) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Versão da política</dt>
          <dd class="text-foreground font-medium text-right"><?= e($outra($pedido['privacy_version'])) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">IP</dt>
          <dd class="text-foreground font-medium text-right"><?= e($outra($pedido['ip_address'])) ?></dd>
        </div>
      </dl>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Apagar pedido</h2>
      <p class="text-xs text-muted-foreground mb-4">Remove o pedido e os seus anexos. Esta ação não pode ser desfeita.</p>

      <form method="POST" action="<?= e($rotaUm . '/apagar') ?>"
            onsubmit="return confirm('Apagar este pedido e todos os anexos? Esta ação não pode ser desfeita.')">
        <?= csrf_campo() ?>
        <button type="submit" class="w-full px-6 py-2.5 border border-destructive/40 text-destructive text-sm font-medium rounded-md hover:bg-destructive/10 transition-colors">
          Apagar pedido
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Modal de visualização de anexos -->
<div id="anexoModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="anexoModalTitulo">
  <div class="absolute inset-0 bg-black/70" data-anexo-fechar></div>
  <div class="relative h-full flex items-center justify-center p-3 md:p-6">
    <div class="flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-xl border border-border bg-card shadow-2xl">
      <div class="flex items-start justify-between gap-4 border-b border-border px-4 py-3">
        <div class="min-w-0">
          <p id="anexoModalTitulo" class="text-sm font-medium text-foreground truncate">Anexo</p>
          <p class="text-xs text-muted-foreground mt-0.5">Pré-visualização — o ficheiro continua privado.</p>
        </div>
        <button type="button" data-anexo-fechar aria-label="Fechar pré-visualização"
                class="shrink-0 rounded-md p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="flex-1 min-h-0 bg-muted p-3 flex items-center justify-center">
        <img id="anexoModalImg" src="" alt="" class="hidden max-h-full max-w-full rounded-md bg-white object-contain" />
        <iframe id="anexoModalFrame" src="" title="Pré-visualização do anexo" class="hidden h-full w-full rounded-md border-0 bg-white"></iframe>
        <p id="anexoModalVazio" class="hidden text-sm text-muted-foreground">Não é possível pré-visualizar este formato — use «Baixar».</p>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-4 py-3">
        <p id="anexoModalMeta" class="text-xs text-muted-foreground truncate"></p>
        <a id="anexoModalBaixar" href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary text-secondary-foreground text-xs font-medium hover:bg-secondary/90 transition-colors">
          Baixar
        </a>
      </div>
    </div>
  </div>
</div>

<?= \App\Core\View::parcial('admin/_rodape'); ?>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('anexoModal');
    if (!modal) return;

    const img = document.getElementById('anexoModalImg');
    const frame = document.getElementById('anexoModalFrame');
    const vazio = document.getElementById('anexoModalVazio');
    const titulo = document.getElementById('anexoModalTitulo');
    const meta = document.getElementById('anexoModalMeta');
    const baixar = document.getElementById('anexoModalBaixar');
    let gatilho = null;

    function abrir(botao) {
      gatilho = botao;
      titulo.textContent = botao.dataset.anexoNome || 'Anexo';
      meta.textContent = botao.dataset.anexoNome || '';
      baixar.href = botao.dataset.anexoBaixar || '#';

      const url = botao.dataset.anexoVer;
      const imagem = botao.dataset.anexoTipo === 'imagem';

      img.classList.add('hidden');
      frame.classList.add('hidden');
      vazio.classList.add('hidden');

      if (imagem) {
        img.src = url;
        img.classList.remove('hidden');
      } else {
        frame.src = url;
        frame.classList.remove('hidden');
      }

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      const fechar = modal.querySelector('[data-anexo-fechar]');
      if (fechar) fechar.focus();
    }

    function fechar() {
      modal.classList.add('hidden');
      img.src = '';
      frame.src = '';
      document.body.style.overflow = '';
      if (gatilho) gatilho.focus();
    }

    document.querySelectorAll('[data-anexo-ver]').forEach((botao) => {
      botao.addEventListener('click', () => abrir(botao));
    });

    modal.querySelectorAll('[data-anexo-fechar]').forEach((alvo) => {
      alvo.addEventListener('click', fechar);
    });

    document.addEventListener('keydown', (evento) => {
      if (evento.key === 'Escape' && !modal.classList.contains('hidden')) fechar();
    });
  });
</script>
<?php \App\Core\View::secaoFim(); ?>
