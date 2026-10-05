# MEMÓRIA DO PROJECTO — HAVREDESIGN (reescrita PHP puro)

Documento de memória do trabalho. Serve para retomar o projecto sem perder contexto
(novo assistente, sessão futura ou outro técnico). Atualizar a cada fase concluída.

---

## 1. Contexto

- **Cliente:** HAVREDESIGN — arquitetura e design de interiores, Angola (Luanda, Talatona).
- **Projecto original:** Laravel 12 funcional em `backend/` (neste directorio — `link.md` tem o
  caminho antigo, já não usado):
  - 23 tabelas MySQL (`havre_design`), 17 páginas públicas, painel admin (CMS),
    formulários, agenda, autenticação, área de cliente, SEO (sitemap/robots/301),
    118 testes verdes (523 asserções).
- **Projecto novo:** `C:\xampp\htdocs\havredesign` — **PHP puro (sem framework)**,
  porque a hospedagem de destino **não suporta Laravel**.
- **Restrições:** só PHP 8.2+ e MySQL; zero dependências de framework; comentários em
  português; PSR-12; aplicar todas as boas práticas de segurança; testar cada fase antes
  de avançar; se algo for ambíguo, **parar e perguntar**.

## 2. Ficheiros de contexto

| Ficheiro | Conteúdo | Estado |
|---|---|---|
| `plan.md` | **Plano oficial da reescrita** (9 fases: auditoria → estrutura → core → segurança → URLs → migração → dados → testes → documentação) + entregáveis esperados | Fonte |
| `laravel_antigo.md` | Memória do processo Laravel (auditoria estática + migração B0–B12, decisões 1–67) | Lido |
| `link.md` | Caminho antigo do backend (obsoleto — agora é `backend/`) | Fonte |
| `memoria.md` | Este documento | Criado |
| `modelo.md` | **Plano de remodelação executável** (o que vou executar, por ordem) | Criado |

## 3. Trajecto até agora

1. Leitura de `plan.md` — objectivo: reescrever tudo em PHP puro mantendo funcionalidades.
2. Backend Laravel movido para `backend/` (dentro do directorio de trabalho) — auditável.
3. Leitura de `laravel_antigo.md` — contexto histórico:
   - Site estático inicial com `localStorage` (sem backend real).
   - Migração Laravel B0–B12 concluída: schema reconciliado (23 tabelas), models/migrations,
     formulários com validação + e-mails, agenda com anti-duplicados, auth Breeze (URIs em PT),
     painel admin (41 rotas, 10 controllers CRUD), headers de segurança, sitemap/robots como
     rotas, 301 legacy (37 mapeamentos), 118 testes verdes.
   - Pendente do cliente: imagens dos 7 serviços, portefólio real, logótipo, validação dos
     textos legais, QR do `/networking`, compressão de imagens, B13 (publicação).
4. Estado do directorio actual: `backend/` (Laravel completo) + documentos de contexto —
   **o código em PHP puro ainda não foi criado**.
5. **Fase 1 (auditoria) concluída** — inventário completo em `modelo.md` §1.1:
   79 rotas (21 web + 43 admin + 15 auth), 24 controllers, 15 models, ~74 vistas Blade,
   5 Form Requests, 7 Mailables, 3 middleware + framework, `AgendaService`/`Media`,
   7 seeders, 12 configs, 23 tabelas confirmadas no MySQL `havre_design`, 118 testes.
   Decisões A–E respondidas (`modelo.md` §1.2).
6. **Fases 2, 3 e 5 concluídas** — estrutura de pastas, front controller (`public/index.php`),
   `.htaccess` (raiz + public) + `router.php` (dev com `php -S`), `.env`/`.gitignore`,
   assets copiados do `backend/`. Core completo: 12 classes (Router, Database, View, Request,
   Response, Session, Csrf, Auth, Validator, Mailer, Logger, Config) + 6 middleware + helpers
   (`e()`, `rota()`, `csrf_campo()`). Layouts/partials Blade → PHP (header, footer, whatsapp).
   **Smoke test OK:** `/` 200 · 404 · 405 · 419 (CSRF) · headers de segurança presentes.
7. **Fase 6 concluída** — todas as vistas convertidas (Blade → PHP puro) e controllers criados:
   - 14 páginas públicas (home, sobre, serviços, soluções, portefólio+detalhe, processo, faq,
     contacto, solicitar-projeto, agendar, networking, privacy, terms) + 4 de auth
     (entrar, registar, recuperar, redefinir) + `/conta` → todas 200 com `$titulo`/`$meta`.
   - 3 formulários públicos + recuperação de palavra-passe → BD + e-mails (padrão `Mailer`,
     templates em `resources/views/emails/`, transporte `log` por omissão): validação com
     `Validator`, honeypot, **throttle 10/15 min por IP+acção** (`app/Core/Throttle.php`),
     flash via `partials/flash.php`.
   - Anexos do pedido de projeto: disco **privado** `storage/app/private/requests/{id}/`
     (nome aleatório, extensão/size allowlist, `.php` rejeitado — testado).
   - **Painel admin completo**: 10 controllers (`app/Controllers/Admin/`) + 19 rotas,
     partials `admin/_topo.php`/`_rodape.php` (abas + flash), 20 views admin
     (dashboard, serviços, portefólio+galeria, soluções, pedidos+anexos, agendamentos,
     mensagens, testemunhos, definições, utilizadores) — 19/19 → 200.
   - SEO: `SeoController` (robots.txt + sitemap.xml gerados da BD).
   - Correcções: `View::render` propaga `$titulo`/`$meta` ao layout; `parcial()` exige `<?=`;
     flash lida uma só vez; `Validator` (UTF-8 reparado, regra `integer` com `0`);
     `_erros_validacao` limpo a cada POST novo (`public/index.php`).
8. **Fase 8 executada** — testes formalizados em `tests/` (decisão D):
   - `tests/executar.php` (runner) + `tests/Apoio.php` (HTTP via curl, login admin,
     asserções, cookies partilhados) + 5 suites em `tests/suites/` (páginas, auth,
     formulários, admin, segurança) → **99/99 verdes** (`php tests/executar.php`).
   - `tests/checklist.md` para itens manuais (8.6 responsividade, 8.7 performance,
     e-mails, «Lembrar-me», throttle, revisão visual do admin).
   - Correcções: `RedirectLegacyUrls` usava colunas inexistentes (`source`/`target`
     → `source_path`/`target_path` + `active`); agora 301 legacy testado (3 reais).
   - Suporte a **HEAD** adicionado (Router resolve como GET; `Response::enviar()`
     suprime corpo) — `HEAD /` → 200.

9. **Fase 9 concluída** — documentação: `README.md` (instalação, configuração, estrutura,
   rotas, testes), `docs/manual-cms.md` (manual do CMS), `docs/seguranca.md` (11 áreas
   de segurança) e `docs/manutencao.md` (backups, logs, troubleshooting).

## 4. Regras inamovíveis (resumo do `plan.md`)

- **Não** usar frameworks (Laravel, Symfony, CodeIgniter) nem Composer para frameworks.
- Arquitectura MVC com front controller único (`public/index.php`).
- SQL: **sempre** prepared statements PDO — nunca concatenar.
- XSS: escape automático nas views (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`).
- CSRF: token em todos os POST, validação com `hash_equals()`.
- Auth: `password_hash()`/`password_verify()`, regeneração de sessão, rate limiting.
- Sessões: cookies HttpOnly + Secure + SameSite=Strict.
- Headers de segurança (nosniff, SAMEORIGIN, Referrer-Policy, Permissions-Policy, HSTS só em produção).
- Uploads: MIME real (finfo), extensão, tamanho, nome gerado, sem execução de PHP na pasta.
- Erros: nunca mostrar stack trace em produção; log em `storage/logs/`.
- `.env` fora do Git; credenciais nunca no código.

## 5. Decisões tomadas

| # | Decisão | Nota |
|---|---|---|
| 1 | Reaproveitar a BD existente (23 tabelas `havre_design`) | Sem reescrever schema, salvo necessidade |
| 2 | Estrutura de pastas conforme `plan.md` Fase 2 | `public/` como DocumentRoot |
| 3 | Conteúdo/documentação em português | Nome de classes/métodos em PT quando fizer sentido |
| 4 | Imagens: servir só o original (decisão A) | Sem WebP/srcset em PHP puro; compressão a cargo do cliente |
| 5 | Verificação de e-mail: remover (decisão B) | Registo entra direto; sem `/verificar-email` |
| 6 | «Lembrar-me»: manter (decisão C) | Cookie 30 dias HttpOnly+Secure+SameSite=Strict |
| 7 | Testes: scripts PHP + checklist (decisão D) | `tests/` com asserts próprios, executáveis por CLI |
| 8 | Tabelas Laravel (`jobs`, `cache`, …): ignorar (decisão E) | Ficam na BD sem uso |

## 6. Estado e próximos passos

**Feito**
- [x] Leitura de `plan.md` e `laravel_antigo.md`
- [x] `memoria.md` criado
- [x] `modelo.md` criado (plano de remodelação)
- [x] Fase 1 — auditoria do backend Laravel (`modelo.md` §1.1) + decisões A–E (`modelo.md` §1.2)
- [x] Fase 2 — estrutura de pastas, front controller, .htaccess, .env, .gitignore, assets
- [x] Fase 3 — Core (12 classes) + smoke test OK
- [x] Fase 4 — 10/10 itens (uploads e throttle feitos na Fase 6: `Throttle.php`, anexos)
- [x] Fase 5 — URLs amigáveis (htaccess + router.php + 301 legacy)
- [x] Fase 6 — páginas públicas, auth, 3 formulários, painel admin (10 controllers),
      SEO (robots/sitemap), flashes/honeypot/throttle
- [x] Fase 8 — 8.1–8.5 formalizados em `tests/` (99/99 + 301 legacy corrigido)
- [x] Fase 9 — documentação: `README.md`, `docs/manual-cms.md`, `docs/seguranca.md`,
      `docs/manutencao.md`

**Pendente** → ver `modelo.md` (7.4 opcional)
- 7.4 verificação de integridade da BD (opcional) · 8.6/8.7 em `tests/checklist.md`

**Bloqueios**
- Nenhum — o backend Laravel está em `backend/` (auditoria desbloqueada).

## 7. Como retomar

1. Ler `modelo.md` → secção «Estado da execução».
2. Continuar na fase assinalada como `EM CURSO`.
3. Atualizar §3 e §6 deste ficheiro a cada fase concluída.
