<?php
/**
 * Agendar Conversa — assistente de 3 passos (tipo, data/hora, dados).
 * Convertida de backend/resources/views/booking/create.blade.php
 */
$titulo = 'Agendar Conversa - HAVREDESIGN';
$meta   = 'Agende uma conversa com a HAVREDESIGN: visita ao local ou reunião online. Escolha data e horário à sua conveniência.';

// Sucesso do POST (flash do controller; fallback para chave simples da sessão)
$resumoAg = \App\Core\Session::lerFlash('appointment_submitted')
    ?? \App\Core\Session::obter('appointment_submitted');

// Utilizador autenticado → pré-preenchimento do formulário (@json no script)
$u            = utilizador();
$utilizadorJs = $u !== null ? ['name' => $u['name'] ?? '', 'email' => $u['email'] ?? ''] : null;

// Erros de validação do servidor (@if ($errors->any()) + @json($errors->all()))
$errosValidacao = \App\Core\Session::obter('_erros_validacao', null, false);
$listaErros     = is_array($errosValidacao) ? array_values($errosValidacao) : [];
$primeiroErro   = is_array($errosValidacao) && $errosValidacao !== [] ? (string) array_key_first($errosValidacao) : null;

// Agenda de `settings.agenda` — MESMA estrutura que o layout Blade servia
// (AgendaService::config(): chaves camelCase lidas pelo AGENDA_CONFIG do app.js).
$agendaBruta = \App\Support\Site::json('agenda', []);
$tiposJs     = [];
foreach (($agendaBruta['tipos'] ?? []) as $codigoTipo => $t) {
    $tiposJs[(string) $codigoTipo] = [
        'label'           => $t['label'] ?? (string) $codigoTipo,
        'precisaEndereco' => (bool) ($t['precisa_endereco'] ?? ($t['precisaEndereco'] ?? false)),
        'nota'            => $t['nota'] ?? null,
    ];
}
$agendaJs = [
    'horarios'          => array_values($agendaBruta['horarios'] ?? ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00']),
    'diasIndisponiveis' => array_map('intval', $agendaBruta['dias_indisponiveis'] ?? [0, 6]),
    'horariosSabado'    => array_values($agendaBruta['horarios_sabado'] ?? []),
    'tipoPredefinido'   => $agendaBruta['tipo_predefinido'] ?? 'ONLINE',
    'tipos'             => $tiposJs,
];

$flagsJson = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
  <!-- Hero Section -->
  <section class="relative py-16 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl leading-tight mb-4">
          Agendar Conversa
        </h1>
        <p class="text-lg text-primary-foreground/80">
          Escolha a melhor forma de nos encontrarmos para discutir seu projeto.
        </p>
      </div>
    </div>
  </section>

  <!-- Step Wizard -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <!-- Step Indicators -->
        <div class="flex items-center justify-center mb-12">
          <div id="step-dot-1" class="w-10 h-10 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center font-bold">1</div>
          <div id="step-line-1" class="w-16 h-0.5 bg-muted"></div>
          <div id="step-dot-2" class="w-10 h-10 rounded-full bg-muted text-muted-foreground flex items-center justify-center font-bold">2</div>
          <div id="step-line-2" class="w-16 h-0.5 bg-muted"></div>
          <div id="step-dot-3" class="w-10 h-10 rounded-full bg-muted text-muted-foreground flex items-center justify-center font-bold">3</div>
        </div>

        <!-- Success Message (Hidden by default) -->
        <div id="success-view" class="<?= $resumoAg ? '' : 'hidden' ?> text-center space-y-6 max-w-md mx-auto py-12">
          <div class="w-20 h-20 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
          <h2 class="font-serif text-2xl md:text-3xl text-foreground">Agendamento Confirmado!</h2>
          <p class="text-muted-foreground">Seu agendamento foi realizado com sucesso e registrado no sistema.</p>
          <div id="success-summary" class="bg-card p-6 rounded-lg border border-border text-left space-y-3 shadow-sm">
            <?php if ($resumoAg): ?>
              <p class="text-sm"><strong>Cliente:</strong> <?= e($resumoAg['cliente'] ?? '') ?></p>
              <p class="text-sm"><strong>Data:</strong> <?= e($resumoAg['data'] ?? '') ?> às <?= e($resumoAg['hora'] ?? '') ?></p>
              <p class="text-sm"><strong>Tipo:</strong> <?= e($resumoAg['tipo'] ?? '') ?></p>
              <p class="text-sm"><strong>Local:</strong> <?= e($resumoAg['local'] ?? '') ?></p>
              <p class="text-sm"><strong>Estado:</strong> <?= e($resumoAg['estado'] ?? '') ?></p>
            <?php endif; ?>
          </div>
          <div class="flex flex-col gap-3 pt-4">
            <a href="<?= e(url('/')) ?>" class="w-full py-3 bg-secondary text-secondary-foreground font-medium rounded-md text-center hover:bg-secondary/90">
              Voltar ao Início
            </a>
            <a href="/conta" class="w-full py-3 border border-border text-foreground font-medium rounded-md text-center hover:bg-muted">
              Ver Meus Agendamentos
            </a>
          </div>
        </div>

        <!-- Wizard Form -->
        <?= \App\Core\View::parcial('partials/flash') ?>
  <form id="wizard-form" method="POST" action="<?= e(rota('agendar')) ?>" class="<?= $resumoAg ? 'hidden ' : '' ?>space-y-8">
          <?= csrf_campo() ?>
          <!-- Honeypot: invisível a humanos, apanha bots (backend.md 6.4) -->
          <div class="hidden" aria-hidden="true">
            <label for="homepage">Homepage</label>
            <input type="text" id="homepage" name="homepage" tabindex="-1" autocomplete="off" />
          </div>
          <!-- Estado do assistente, enviado com o formulário -->
          <input type="hidden" name="type" id="field-type" value="<?= e(velho('type')) ?>" />
          <input type="hidden" name="appt_date" id="field-date" value="<?= e(velho('appt_date')) ?>" />
          <input type="hidden" name="appt_time" id="field-time" value="<?= e(velho('appt_time')) ?>" />
          <!-- Step 1: Type -->
          <div id="wizard-step-1" class="space-y-8">
            <div class="text-center mb-8">
              <h2 class="font-serif text-2xl text-foreground mb-2">Tipo de Consulta</h2>
              <p class="text-muted-foreground">Escolha o formato da consulta.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <label onclick="selectType('SITE')" class="cursor-pointer">
                <div id="type-card-site" class="p-6 rounded-lg border-2 border-border bg-card text-center space-y-4 hover:border-secondary transition-colors">
                  <div class="w-16 h-16 rounded-full bg-secondary/20 flex items-center justify-center mx-auto text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  </div>
                  <h3 class="font-serif text-xl font-semibold">Visita ao Local</h3>
                  <p class="text-sm text-muted-foreground">A Arq.ª Janette vai até você para conhecer o espaço e entender suas necessidades.</p>
                  <p class="text-xs text-muted-foreground">A visita ao local pode implicar uma taxa de deslocação, calculada conforme a localização e confirmada previamente.</p>
                </div>
              </label>

              <label onclick="selectType('ONLINE')" class="cursor-pointer">
                <div id="type-card-online" class="p-6 rounded-lg border-2 border-secondary bg-card text-center space-y-4 hover:border-secondary transition-colors">
                  <div class="w-16 h-16 rounded-full bg-secondary/20 flex items-center justify-center mx-auto text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                  </div>
                  <h3 class="font-serif text-xl font-semibold">Reunião Online</h3>
                  <p class="text-sm text-muted-foreground">Conversa por videochamada ou telefone, no horário combinado.</p>
                </div>
              </label>
            </div>

            <div id="address-container" class="<?= velho('type') === 'SITE' ? '' : 'hidden' ?> space-y-2">
              <label for="address" class="block text-sm font-medium text-foreground">Endereço do Local *</label>
              <textarea id="address" name="address" rows="3" placeholder="Digite o endereço completo da visita" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('address')) ?></textarea>
              <?php if (erro_de('address')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('address')) ?></p><?php endif; ?>
            </div>

            <div class="flex justify-end">
              <button type="button" onclick="goToStep(2)" class="px-8 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors">
                Continuar
              </button>
            </div>
          </div>

          <!-- Step 2: Date & Time -->
          <div id="wizard-step-2" class="hidden space-y-8">
            <div class="text-center mb-8">
              <h2 class="font-serif text-2xl text-foreground mb-2">Data e Horário</h2>
              <p class="text-muted-foreground">Selecione a data e horário de sua preferência.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
              <div>
                <label class="block text-sm font-medium text-foreground mb-4">Selecione a Data *</label>
                <div class="bg-card border border-border p-4 rounded-lg shadow-sm">
                  <div class="flex justify-between items-center mb-4">
                    <button type="button" onclick="changeMonth(-1)" class="p-1 hover:bg-muted rounded text-foreground font-bold">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span id="calendar-month-year" class="font-bold text-foreground"></span>
                    <button type="button" onclick="changeMonth(1)" class="p-1 hover:bg-muted rounded text-foreground font-bold">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                  </div>
                  <div class="calendar-grid">
                    <div class="calendar-day-header">Dom</div>
                    <div class="calendar-day-header">Seg</div>
                    <div class="calendar-day-header">Ter</div>
                    <div class="calendar-day-header">Qua</div>
                    <div class="calendar-day-header">Qui</div>
                    <div class="calendar-day-header">Sex</div>
                    <div class="calendar-day-header">Sáb</div>
                  </div>
                  <div id="calendar-days" class="calendar-grid mt-2"></div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-foreground mb-4">Selecione o Horário *</label>
                <div id="time-slots-grid" class="grid grid-cols-2 gap-3">
                  <!-- Times rendered via JS -->
                </div>
                <div id="selected-summary" class="mt-6 p-4 bg-muted rounded-lg hidden">
                  <p class="text-xs text-muted-foreground">Você selecionou:</p>
                  <p id="selected-summary-text" class="font-medium text-foreground text-sm mt-1"></p>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-3 justify-between">
              <button type="button" onclick="goToStep(1)" class="px-6 py-3 border border-border text-foreground font-medium rounded-md hover:bg-muted">
                Voltar
              </button>
              <button type="button" onclick="goToStep(3)" class="px-8 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90">
                Continuar
              </button>
            </div>
          </div>

          <!-- Step 3: Personal Info -->
          <div id="wizard-step-3" class="hidden space-y-8">
            <div class="text-center mb-8">
              <h2 class="font-serif text-2xl text-foreground mb-2">Seus Dados</h2>
              <p class="text-muted-foreground">Preencha suas informações para confirmar o agendamento.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="userName" class="block text-sm font-medium text-foreground">Nome Completo *</label>
                <input id="userName" name="user_name" type="text" required value="<?= e(velho('user_name')) ?>" placeholder="Seu nome" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
                <?php if (erro_de('user_name')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('user_name')) ?></p><?php endif; ?>
              </div>
              <div class="space-y-2">
                <label for="userEmail" class="block text-sm font-medium text-foreground">E-mail *</label>
                <input id="userEmail" name="user_email" type="email" required value="<?= e(velho('user_email')) ?>" placeholder="seu@email.com" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
                <?php if (erro_de('user_email')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('user_email')) ?></p><?php endif; ?>
              </div>
              <div class="space-y-2">
                <label for="userPhone" class="block text-sm font-medium text-foreground">Telefone *</label>
                <input id="userPhone" name="user_phone" type="text" required value="<?= e(velho('user_phone')) ?>" placeholder="+244 926 184 104" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
                <?php if (erro_de('user_phone')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('user_phone')) ?></p><?php endif; ?>
              </div>
              <div class="space-y-2 md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-foreground">Observações (opcional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Conte-nos sobre o seu projeto ou dúvidas" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"><?= e(velho('notes')) ?></textarea>
                <?php if (erro_de('notes')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('notes')) ?></p><?php endif; ?>
              </div>
            </div>

            <!-- Confirmation Summary Card -->
            <div class="bg-muted p-6 rounded-lg space-y-3">
              <h4 class="font-medium text-foreground">Resumo do Agendamento</h4>
              <div class="grid sm:grid-cols-2 gap-4 text-sm text-muted-foreground">
                <div><span>Data:</span> <strong id="sum-date" class="text-foreground">--</strong></div>
                <div><span>Horário:</span> <strong id="sum-time" class="text-foreground">--</strong></div>
                <div><span>Tipo:</span> <strong id="sum-type" class="text-foreground">--</strong></div>
                <div id="sum-addr-wrapper" class="hidden"><span>Endereço:</span> <strong id="sum-addr" class="text-foreground">--</strong></div>
              </div>
            </div>

            <div class="flex flex-wrap gap-3 justify-between">
              <button type="button" onclick="goToStep(2)" class="px-6 py-3 border border-border text-foreground font-medium rounded-md hover:bg-muted">
                Voltar
              </button>
              <button type="submit" class="px-8 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90">
                Confirmar Agendamento
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
<script>
  // Agenda vinda de `settings.agenda` — lida pelo app.js antes de correr (defer).
  // No Blade esta atribuição vivia no layout (com o JSON de $agendaJs partilhado).
  window.HAVRE_AGENDA = <?= json_encode($agendaJs, $flagsJson) ?>;

  let currentStep = 1;
  let selectedType = null;
  let selectedDate = null;
  let selectedTime = null;

  let calDate = new Date();
  let timeSlots = [];

  // Disponibilidade vinda do servidor: data => horários livres (null = ainda não carregada)
  let disponibilidade = null;

  <?php if ($listaErros !== []): ?>
    window.__FORM_ERROS__ = <?= json_encode($listaErros, $flagsJson) ?>;
    window.__FORM_ERROS_CHAVE__ = <?= json_encode($primeiroErro, $flagsJson) ?>;
  <?php endif; ?>

  document.addEventListener('DOMContentLoaded', () => {
    const cfg = AGENDA_CONFIG;
    selectedType = document.getElementById('field-type').value || cfg.tipoPredefinido;
    timeSlots = cfg.horarios;
    selectType(selectedType);

    const utilizador = <?= json_encode($utilizadorJs, $flagsJson) ?>;
    if (utilizador) {
      const nome = document.getElementById('userName');
      const email = document.getElementById('userEmail');
      if (nome && !nome.value) nome.value = utilizador.name || '';
      if (email && !email.value) email.value = utilizador.email || '';
    }

    // Repõe data/horário de uma tentativa anterior (old())
    const campoData = document.getElementById('field-date').value;
    const campoHora = document.getElementById('field-time').value;
    if (campoData) {
      selectedDate = campoData;
      const partes = campoData.split('-').map(Number);
      calDate = new Date(partes[0], partes[1] - 1, 1);
    }
    if (campoHora) selectedTime = campoHora;

    renderTimeSlots();
    renderCalendar();
    carregarDisponibilidade();

    document.getElementById('wizard-form').addEventListener('submit', handleFinish);

    // Validação falhou no servidor → aviso e voltar ao passo certo
    const erros = window.__FORM_ERROS__ || [];
    if (erros.length) {
      showToast(erros[0], 'error');
      const chave = window.__FORM_ERROS_CHAVE__ || '';
      if (chave === 'type' || chave === 'address') {
        goToStep(1);
      } else if (selectedDate && selectedTime) {
        goToStep(3);
      } else {
        goToStep(2);
      }
    }
  });

  function carregarDisponibilidade() {
    const y = calDate.getFullYear();
    const m = String(calDate.getMonth() + 1).padStart(2, '0');
    fetch(`<?= e(rota('agendar')) ?>/disponibilidade?mes=${y}-${m}`)
      .then(r => (r.ok ? r.json() : Promise.reject(r)))
      .then(d => {
        disponibilidade = d.dias || {};
        renderCalendar();
        renderTimeSlots();
      })
      .catch(() => { disponibilidade = null; }); // sem rede: mantém as regras locais da agenda
  }

  function typeLabel(type) {
    return getAppointmentTypeLabel(type);
  }

  function selectType(type) {
    selectedType = type;
    document.getElementById('field-type').value = type;
    const cards = {
        SITE: document.getElementById('type-card-site'),
        ONLINE: document.getElementById('type-card-online')
    };
    const addrContainer = document.getElementById('address-container');

    Object.keys(cards).forEach(key => {
      cards[key].className = key === type
        ? "p-6 rounded-lg border-2 border-secondary bg-card text-center space-y-4 shadow-sm"
        : "p-6 rounded-lg border-2 border-border bg-card text-center space-y-4 hover:border-secondary transition-colors";
    });

    if (appointmentTypeNeedsAddress(type)) {
      addrContainer.classList.remove('hidden');
    } else {
      addrContainer.classList.add('hidden');
    }
  }

  function goToStep(step) {
    if (step === 2 && appointmentTypeNeedsAddress(selectedType)) {
      const addr = document.getElementById('address').value;
      if (!addr.trim()) {
        showToast('Por favor, informe o endereço para a visita', 'error');
        return;
      }
    }

    if (step === 3) {
      if (!selectedDate) {
        showToast('Por favor, selecione uma data no calendário', 'error');
        return;
      }
      if (!selectedTime) {
        showToast('Por favor, selecione um horário', 'error');
        return;
      }

      document.getElementById('field-date').value = selectedDate;
      document.getElementById('field-time').value = selectedTime;
      document.getElementById('sum-date').textContent = selectedDate;
      document.getElementById('sum-time').textContent = selectedTime;
      document.getElementById('sum-type').textContent = typeLabel(selectedType);

      if (selectedType === 'SITE') {
        document.getElementById('sum-addr-wrapper').classList.remove('hidden');
        document.getElementById('sum-addr').textContent = document.getElementById('address').value;
      } else {
        document.getElementById('sum-addr-wrapper').classList.add('hidden');
      }
    }

    currentStep = step;
    document.getElementById('wizard-step-1').classList.toggle('hidden', step !== 1);
    document.getElementById('wizard-step-2').classList.toggle('hidden', step !== 2);
    document.getElementById('wizard-step-3').classList.toggle('hidden', step !== 3);

    for (let i = 1; i <= 3; i++) {
      const dot = document.getElementById(`step-dot-${i}`);
      if (i <= step) {
        dot.className = "w-10 h-10 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center font-bold";
      } else {
        dot.className = "w-10 h-10 rounded-full bg-muted text-muted-foreground flex items-center justify-center font-bold";
      }
    }
  }

  function horariosDoDia() {
    if (disponibilidade !== null && selectedDate) {
      return disponibilidade[selectedDate] || [];
    }
    return timeSlots;
  }

  function renderTimeSlots() {
    const grid = document.getElementById('time-slots-grid');
    const slots = horariosDoDia();
    if (!slots.length) {
      grid.innerHTML = '<p class="text-sm text-muted-foreground col-span-2">Sem horários disponíveis nesta data. Escolha outro dia.</p>';
      return;
    }
    grid.innerHTML = slots.map(t => `
      <button
        type="button"
        onclick="selectTime('${t}')"
        id="time-btn-${t.replace(':', '')}"
        class="py-2.5 px-4 border border-border rounded-md text-sm font-medium hover:bg-secondary hover:text-secondary-foreground transition-colors"
      >
        ${t}
      </button>
    `).join('');
  }

  function selectTime(t) {
    selectedTime = t;
    document.querySelectorAll('#time-slots-grid button').forEach(b => {
      b.className = "py-2.5 px-4 border border-border rounded-md text-sm font-medium hover:bg-secondary hover:text-secondary-foreground transition-colors";
    });
    const btn = document.getElementById(`time-btn-${t.replace(':', '')}`);
    if (btn) btn.className = "py-2.5 px-4 bg-primary text-primary-foreground font-bold rounded-md text-sm";

    updateSelectedSummary();
  }

  function renderCalendar() {
    const year = calDate.getFullYear();
    const month = calDate.getMonth();
    const monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    document.getElementById('calendar-month-year').textContent = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysContainer = document.getElementById('calendar-days');
    daysContainer.innerHTML = '';

    for (let i = 0; i < firstDay; i++) {
      const empty = document.createElement('div');
      daysContainer.appendChild(empty);
    }

    const today = new Date();
    today.setHours(0,0,0,0);

    for (let day = 1; day <= daysInMonth; day++) {
      const dateObj = new Date(year, month, day);
      const dayOfWeek = dateObj.getDay();
      const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

      const isPast = dateObj < today;
      const isWeekend = AGENDA_CONFIG.diasIndisponiveis.includes(dayOfWeek);
      const isSemVagas = disponibilidade !== null && !Object.prototype.hasOwnProperty.call(disponibilidade, dateStr);
      const isDisabled = isPast || isWeekend || isSemVagas;

      const dayEl = document.createElement('div');
      dayEl.className = `calendar-day ${isDisabled ? 'disabled' : ''} ${selectedDate === dateStr ? 'selected' : ''}`;
      dayEl.textContent = day;

      if (!isDisabled) {
        dayEl.addEventListener('click', () => {
          if (selectedDate !== dateStr) {
            selectedTime = null;
            document.getElementById('field-time').value = '';
          }
          selectedDate = dateStr;
          document.getElementById('field-date').value = dateStr;
          renderCalendar();
          renderTimeSlots();
          updateSelectedSummary();
        });
      }

      daysContainer.appendChild(dayEl);
    }
  }

  function changeMonth(delta) {
    calDate.setMonth(calDate.getMonth() + delta);
    renderCalendar();
    carregarDisponibilidade();
  }

  function updateSelectedSummary() {
    const summaryBox = document.getElementById('selected-summary');
    const summaryText = document.getElementById('selected-summary-text');
    if (selectedDate && selectedTime) {
      summaryBox.classList.remove('hidden');
      summaryText.textContent = `${selectedDate} às ${selectedTime}`;
    }
  }

  function handleFinish(e) {
    e.preventDefault();

    const endereco = document.getElementById('address').value.trim();
    if (appointmentTypeNeedsAddress(selectedType) && !endereco) {
      showToast('Por favor, informe o endereço para a visita', 'error');
      goToStep(1);
      return;
    }
    if (!selectedDate) {
      showToast('Por favor, selecione uma data no calendário', 'error');
      goToStep(2);
      return;
    }
    if (!selectedTime) {
      showToast('Por favor, selecione um horário', 'error');
      goToStep(2);
      return;
    }

    // Assistente → formulário real (B8: grava em `appointments`)
    document.getElementById('field-type').value = selectedType;
    document.getElementById('field-date').value = selectedDate;
    document.getElementById('field-time').value = selectedTime;

    const form = document.getElementById('wizard-form');
    form.removeEventListener('submit', handleFinish);
    HTMLFormElement.prototype.submit.call(form);
  }
</script>
<?php \App\Core\View::secaoFim(); ?>
