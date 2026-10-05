/**
 * HAVREDESIGN — JavaScript do site (Laravel/Blade)
 *
 * B11 — os dados vivem no servidor:
 *   · agenda      -> window.HAVRE_AGENDA (settings.agenda, servido pelo layout)
 *   · servicos    -> Eloquent, renderizados em Blade
 *   · portfolio   -> Eloquent, renderizados em Blade
 *   · pedidos/agendamentos/utilizador -> POST + sessao do Laravel
 *
 * Removido nesta fase: localStorage (havre_*), DEFAULT_SERVICES,
 * DEFAULT_PORTFOLIO, SERVICES_SEED_VERSION, initStorage, os data accessors,
 * o estado de utilizador local e o render de header/footer/WhatsApp (agora
 * partials Blade).
 * Mantidos: assistente de agendamento, calendario, toasts, dialogs
 * personalizados (HAVRE_UI, substituem alert/confirm), accordion e filtros
 * (inline nas paginas), menu hamburguer (partial do header) e o loader.
 */

/* Limpeza automatica do legado estatico: apaga as chaves havre_* que a versao
   anterior do site gravava no localStorage. */
function limparLocalStorageLegado() {
  try {
    const chaves = [];
    for (let i = 0; i < localStorage.length; i++) {
      const chave = localStorage.key(i);
      if (chave && chave.indexOf('havre_') === 0) chaves.push(chave);
    }
    chaves.forEach((chave) => localStorage.removeItem(chave));
  } catch (erro) {
    // localStorage indisponivel (modo privado/bloqueado) - nada a limpar
  }
}


/* ==========================================================================
   CONFIGURAÇÃO DA AGENDA — único sítio para alterar horários e tipos de reunião
   ========================================================================== */
const AGENDA_CONFIG = (typeof window !== 'undefined' && window.HAVRE_AGENDA && window.HAVRE_AGENDA.horarios)
  ? window.HAVRE_AGENDA
  : {
  // Horários apresentados ao cliente (formato 24h)
  horarios: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
  // Dias em que NÃO é possível marcar (0 = domingo, 6 = sábado).
  // Para passar a atender ao sábado: remover o 6 e preencher horariosSabado.
  diasIndisponiveis: [0, 6],
  horariosSabado: [],
  // Tipo de reunião pré-selecionado no assistente
  tipoPredefinido: 'ONLINE',
  // Tipos de reunião (labels usados no assistente, dashboard e administração)
  tipos: {
    SITE: {
      label: 'Visita ao Local',
      precisaEndereco: true,
      nota: 'A visita ao local pode implicar uma taxa de deslocação, calculada conforme a localização e confirmada previamente.'
    },
    ONLINE: { label: 'Reunião Online', precisaEndereco: false, nota: 'Conversa por videochamada ou telefone, no horário combinado.' }
  }
};

// Rótulo de um tipo de reunião (qualquer página pode usar)
function getAppointmentTypeLabel(type) {
  const t = AGENDA_CONFIG.tipos[type];
  return t ? t.label : (type || '');
}

function appointmentTypeNeedsAddress(type) {
  const t = AGENDA_CONFIG.tipos[type];
  return t ? !!t.precisaEndereco : type === 'SITE';
}

// Toast Notifications System
function showToast(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <span class="flex-1">${message}</span>
    <button onclick="this.parentElement.remove()" class="text-white hover:opacity-80">&times;</button>
  `;
  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 4000);
}

// Setup IntersectionObserver for Scroll Animations
function setupScrollReveal() {
  // Selecciona apenas elementos abaixo do fold — exclui a hero e o header
  // para evitar que fiquem com opacity:0 e nunca sejam revelados.
  const candidatos = document.querySelectorAll(
    '.reveal-on-scroll, .card-hover-effect'
  );

  if (!('IntersectionObserver' in window)) {
    // Fallback: mostrar tudo se o browser não suportar IntersectionObserver
    candidatos.forEach(el => el.classList.add('visible'));
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // revela uma vez e para de observar
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

  candidatos.forEach((el, index) => {
    const rect = el.getBoundingClientRect();
    // Se já está visível no viewport ao carregar → mostrar imediatamente
    if (rect.top < window.innerHeight && rect.bottom > 0) {
      el.classList.add('visible');
    } else {
      if (index % 3 === 1) el.classList.add('delay-100');
      if (index % 3 === 2) el.classList.add('delay-200');
      observer.observe(el);
    }
  });
}


// Global App Initialization
document.addEventListener('DOMContentLoaded', () => {
  limparLocalStorageLegado();
  setTimeout(setupScrollReveal, 100);
});

// Ecrã de loading com o logótipo — esconde assim que a página termina de carregar
function hidePageLoader() {
  const loader = document.getElementById('page-loader');
  if (!loader) return;
  loader.classList.add('is-hidden');
  setTimeout(() => {
    if (loader.parentNode) loader.parentNode.removeChild(loader);
  }, 700);
}

window.addEventListener('load', hidePageLoader);
if (document.readyState === 'complete') hidePageLoader();


/* ==========================================================================
   DIÁLOGOS PERSONALIZADOS — substituem alert() e confirm() nativos
   Uso em scripts:
     await HAVRE_UI.aviso('Guardado com sucesso.')          // no lugar de alert()
     const ok = await HAVRE_UI.confirmar('Apagar item?')    // no lugar de confirm()
   Uso em formulários (sem JS inline): colocar no <form>
     data-confirm="mensagem"
     data-confirm-acao="Apagar"          (texto do botão principal)
     data-confirm-tipo="perigo|primario" (vermelho/azul — por omissão perigo)
   ========================================================================== */
const HAVRE_UI = (() => {
  const ICONES = {
    perigo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
    primario: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    sucesso: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>'
  };

  let overlay = null;
  let resolver = null;
  let overflowAnterior = '';

  function construir() {
    overlay = document.createElement('div');
    overlay.className = 'hd-overlay';
    overlay.hidden = true;

    const caixa = document.createElement('div');
    caixa.className = 'hd-dialogo';
    caixa.setAttribute('role', 'alertdialog');
    caixa.setAttribute('aria-modal', 'true');
    caixa.setAttribute('aria-labelledby', 'hd-dialogo-titulo');
    caixa.setAttribute('aria-describedby', 'hd-dialogo-mensagem');

    caixa.innerHTML = `
      <div class="hd-dialogo__icone" data-icone></div>
      <h3 class="hd-dialogo__titulo" id="hd-dialogo-titulo"></h3>
      <p class="hd-dialogo__mensagem" id="hd-dialogo-mensagem"></p>
      <div class="hd-dialogo__acoes">
        <button type="button" class="hd-botao hd-botao--fantasma" data-cancelar></button>
        <button type="button" class="hd-botao" data-confirmar></button>
      </div>`;

    overlay.appendChild(caixa);

    overlay.querySelector('[data-cancelar]').addEventListener('click', () => fechar(false));
    overlay.querySelector('[data-confirmar]').addEventListener('click', () => fechar(true));
    // Clique fora da caixa cancela (equivalente ao botão Cancelar)
    overlay.addEventListener('mousedown', (evento) => {
      if (evento.target === overlay) fechar(false);
    });

    document.body.appendChild(overlay);
  }

  function abrir(opcoes) {
    if (!overlay) construir();

    return new Promise((resolverPromise) => {
      resolver = resolverPromise;

      const tipo = opcoes.tipo || 'primario';
      const ehPerigo = tipo === 'perigo';

      overlay.querySelector('[data-icone]').innerHTML = ICONES[tipo] || ICONES.primario;
      overlay.querySelector('[data-icone]').className = 'hd-dialogo__icone hd-dialogo__icone--' + tipo;
      overlay.querySelector('#hd-dialogo-titulo').textContent = opcoes.titulo || '';
      overlay.querySelector('#hd-dialogo-mensagem').textContent = opcoes.mensagem || '';

      const botaoCancelar = overlay.querySelector('[data-cancelar]');
      const botaoConfirmar = overlay.querySelector('[data-confirmar]');

      botaoCancelar.textContent = opcoes.cancelarTexto || 'Cancelar';
      botaoConfirmar.textContent = opcoes.acao || 'Confirmar';
      botaoConfirmar.className = 'hd-botao hd-botao--' + (ehPerigo ? 'perigo' : 'primario');
      botaoCancelar.hidden = !!opcoes.semCancelar;

      overflowAnterior = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      overlay.hidden = false;

      // Em acções destrutivas o foco inicial é Cancelar (Enter não apaga por engano)
      (opcoes.semCancelar || !ehPerigo ? botaoConfirmar : botaoCancelar).focus();
    });
  }

  function fechar(resultado) {
    if (!overlay || overlay.hidden) return;
    overlay.hidden = true;
    document.body.style.overflow = overflowAnterior;

    const concluido = resolver;
    resolver = null;
    if (concluido) concluido(resultado);
  }

  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && overlay && !overlay.hidden) {
      evento.preventDefault();
      fechar(false);
    }
  });

  /** Substituto de alert() — resolve quando o utilizador carrega em OK. */
  function aviso(mensagem, opcoes = {}) {
    return abrir({
      titulo: opcoes.titulo || '',
      mensagem,
      tipo: opcoes.tipo || 'sucesso',
      acao: opcoes.acao || 'OK',
      semCancelar: true
    });
  }

  /** Substituto de confirm() — resolve true (aceite) ou false (cancelado/ESC). */
  function confirmar(mensagem, opcoes = {}) {
    return abrir({
      titulo: opcoes.titulo || 'Tem a certeza?',
      mensagem,
      tipo: opcoes.tipo || 'perigo',
      acao: opcoes.acao || 'Confirmar',
      cancelarTexto: opcoes.cancelarTexto
    });
  }

  return { aviso, confirmar };
})();

// Formulários com data-confirm="..." — confirmação personalizada sem JS inline.
// (requestSubmit re-dispara o submit; sem data-confirm segue a submissão normal)
document.addEventListener('submit', (evento) => {
  const form = evento.target;
  if (!(form instanceof HTMLFormElement)) return;

  const texto = form.getAttribute('data-confirm');
  if (!texto) return;

  evento.preventDefault();

  HAVRE_UI.confirmar(texto, {
    titulo: form.getAttribute('data-confirm-titulo') || undefined,
    acao: form.getAttribute('data-confirm-acao') || undefined,
    tipo: form.getAttribute('data-confirm-tipo') || 'perigo'
  }).then((aceite) => {
    if (aceite) {
      form.removeAttribute('data-confirm');
      form.requestSubmit();
    }
  });
});
