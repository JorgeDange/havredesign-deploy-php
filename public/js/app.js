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
 * Mantidos: assistente de agendamento, calendario, toasts, accordion e filtros
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
