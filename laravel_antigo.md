# MEMÓRIA DO PROCESSO — Auditoria ao Website HAVREDESIGN

Documento de memória do trabalho: regista **o que foi feito, porquê, com que base e o que falta**.
Serve para retomar o projeto sem perder contexto (por outro técnico ou numa sessão futura).

---

## 1. Contexto

- **Cliente:** HAVREDESIGN — arquitetura e design de interiores, Angola (Luanda, Talatona).
- **Objeto:** website institucional com páginas Início, Sobre, Serviços, Orçamento (pacotes e preços), Formulário, Agendar, Processo, FAQ, Portefólio, Contacto e área de cliente com login.
- **Pedido:** o cliente tem um briefing com **13 secções de alterações** e quer uma **auditoria técnica**: onde mexer, o que está em falta e o que pode partir. **Sem código — um mapa de intervenção.**

---

## 2. Ficheiros do projeto

| Ficheiro | Conteúdo | Estado |
|---|---|---|
| `cm.md` | Instrução de trabalho + briefing completo (13 secções, 188 linhas) | Fonte |
| `plan.md` | **Plano de intervenção** (gap analysis): 13 tabelas por secção + entregáveis A–E + estado da execução | Criado |
| `memoria.md` | Este documento — memória do processo | Criado |
| `backend.md` | **Especificação do backend para migração a Laravel**: migrations, rotas, regras de negócio, plano B0–B8 | Criado |

---

## 2.1. Decisão de âmbito (tomada pelo cliente)

**Tudo o que depende de servidor ou base de dados fica adiado para depois de migrar o projeto para Laravel.**
Por enquanto **só se executa conteúdo estático**.

- Entra no adiamento: formulários (secção 6), agenda (secção 7), área do cliente, upload de anexos,
  consentimento persistido, painel admin, e-mails, 301 por base de dados.
- continua a fazer-se agora: secções 2, 3, 5, 8, 9, 10 (texto), 11, 12 (testes estáticos) e 13.
- Especificação completa em `backend.md`; regra prática lá definida (§12):
  **não prometer no site funcionalidades que o backend ainda não suporta**.

---

## 3. Cronologia do trabalho

1. **Pedido inicial:** «leia o cm.md».
   - Lido. Na altura continha apenas as instruções (26 linhas) e referenciava «o briefing abaixo», que **não existia**. Sinalizado o ficheiro incompleto.
2. **Pedido seguinte:** «leia novamente».
   - Relido: o ficheiro passou a 188 linhas, com o **briefing completo** nas linhas 27–188 (13 secções).
3. **Execução do briefing** nos termos do próprio `cm.md`:
   - Gap analysis com tabela por secção (8 campos pedidos: elemento afetado, estado atual provável, estado desejado, tipo de intervenção, risco, dependências, lacuna no briefing).
   - Entregáveis finais A–E.
4. **Documentação** (pedido do cliente): o conteúdo foi gravado em `plan.md`; este `memoria.md` cria a memória do processo.

---

## 4. Premissas adotadas

Todas vêm do próprio `cm.md` (instrução 2):

- O site atual **tem** as páginas listadas em §1 e uma área de cliente com login.
- O estado das funcionalidades (agenda, área do cliente, anexos) **não foi verificado** — todas as colunas «estado atual provável» são estimativas, não observações.
- Não foi feita qualquer leitura de código, CMS, base de dados ou sitemap: **não há evidência empírica**, apenas o briefing e a estrutura assumida.

> ⚠️ Isto é uma **gap analysis documental**, não uma auditoria ao site real. A Fase 0 do plano (acesso + staging + verificação) existe precisamente para validar as suposições.

---

## 5. Estrutura da análise produzida

Para cada uma das 13 secções do briefing, uma tabela com 7 colunas (a secção é o título):

1. Elemento afetado
2. Estado provável atual
3. Estado desejado
4. Tipo de intervenção — `conteúdo · estrutura · funcionalidade · design · SEO · backend`
5. Risco se não for feito
6. Dependências (o que tem de estar pronto antes)
7. **Lacuna no briefing** (o que o documento não esclarece e precisa de confirmação do cliente)

Entregáveis:
- **A)** 10 perguntas críticas antes de começar
- **B)** Ordem de execução em 6 fases (0 a 5)
- **C)** Lista de URLs prováveis para redirecionamento 301
- **D)** Checklist de testes antes de publicar
- **E)** Riscos de SEO por retirar «construção», «gestão de obras» e «urbanismo»

---

## 6. Decisões e interpretações tomadas

Registo das escolhas feitas onde o briefing era ambíguo — **todas requerem confirmação do cliente**:

| # | Ambiguidade | Interpretação adotada |
|---|---|---|
| 1 | «Construção» na identificação legal vs. como serviço | Manter a denominação legal completa; remover **apenas** enquanto oferta de serviço |
| 2 | Mobiliário: retirar do site todo ou só de Serviços? | Tratado como serviço autónomo a remover em todo o site; confirmar |
| 3 | Slug de Orçamento após renomear para «HAVRE Soluções» | Duas opções apresentadas (mudar slug + 301, ou manter `/orcamento` e mudar só o H1) — decisão pendente |
| 4 | «Serviço pretendido» vs «Solução pretendida» no formulário | Briefing diz «como alternativa aos serviços» → assumida escolha exclusiva; confirmar se é acumulativa |
| 5 | «Criar conta» na secção 6 | Condicionada ao estado real da área de cliente (secção 12); ocultar se não estiver funcional |
| 6 | Testemunhos «ocultos» | Interpretação: não devem aparecer **nem no HTML** (flag/CMS), não apenas escondidos em CSS |
| 7 | CTA «Ver portefólio» | Condicional a haver projetos publicados — criada dependência da secção 11 |
| 8 | URLs para 301 | São **hipóteses** construídas a partir das páginas assumidas; precisam do sitemap real |

**Decisões tomadas durante a Fase 1** (a executar, todas reversíveis):

| # | Situação | Decisão tomada |
|---|---|---|
| 9 | «Tipo de Projeto» do formulário tinha as opções antigas de serviço | Apenas renomeei «Arquitetura & Construção» → «Projeto Arquitetónico». As restantes opções continuam a ser de serviço e **devem ser redefinidas na secção 6 (Fase 3)** |
| 10 | Número de WhatsApp por identificar | Usei o **+244 926 184 104** por ser o que o botão flutuante já usava. **Requer confirmação** |
| 11 | 7 serviços complementares sem imagem | Placeholder com ícone em vez de imagem errada. Pedir imagens ao cliente |
| 12 | «O que inclui» dos serviços principais | Vazio (o briefing não define os itens) e o bloco auto-oculta-se. Não inventei conteúdo |
| 13 | Novos serviços ficarem presos no localStorage dos visitantes | Acrescentei `SERVICES_SEED_VERSION` que regrava o catálogo quando a versão muda. **Nota:** isto também apaga alterações manuais feitas no painel admin |
| 14 | Política/Termos sem página | Mantidos com `href="#"` — **não posso criar páginas legais sem texto aprovado** |
| 15 | Onde termina a Fase 1 e começa a Fase 3 em `orcamento.html` | Só mudei o rótulo do separador (secção 1 é site-wide). Preços, pacotes e «EXPLORAR PACOTE» ficam para a Fase 3 |

**Decisões tomadas na Fase 2:**

| # | Situação | Decisão tomada |
|---|---|---|
| 16 | Secção 2 e secção 4 do briefing pedem conjuntos de serviços diferentes nos destaques do Início | Adotada a **secção 4/9** (Design de Interiores · Fiscalização) por ser a definição canónica e estar repetida na FAQ |
| 17 | Pré-visualização do processo no Início mantinha as etapas antigas (Briefing/Conceituação/Produção) | Atualizada para coerência com a secção 8 — senão o site contradizia-se a si próprio |
| 18 | Layout dos Valores: 4 → 6 itens | Grelha de 3 colunas partida em duas secções (Missão/Visão em 2 colunas + Valores em secção própria de 6 cartões) para não rebentar o layout |
| 19 | «Ver portefólio» no hero «só com projetos publicados» | Implementado com verificação em JS (`getPortfolio().length === 0` → `hidden`), não com valor fixo |
| 20 | «Em Construção» no caso real da página Processo | Renomeado para «Durante a obra» — a descrição passa a referir fiscalização **contratada** |

---

## 7. Lacunas críticas identificadas no briefing

O briefing **não** esclarece — e bloqueia o início:

1. **Qual é o número de WhatsApp** dos três apresentados.
2. **Estado real da área de cliente** (registo, login, recuperação de palavra-passe, isolamento de dados).
3. **Onde está o portfolio** «enviado anteriormente» e com que conteúdo.
4. **Infraestrutura:** existe staging? Quem tem acesso a hosting/CMS/base de dados?
5. **Sistema de agenda:** como se garantem horários reais e ausência de duplicados.
6. **Anexos:** formatos, limite de tamanho, armazenamento e destinatário.
7. **Logótipo novo:** localização e formatos (favicon, og:image).
8. **Textos legais:** existência e atualização de Política de Privacidade e Termos de Uso.
9. **Tour virtual:** se existe e o que fazer com ele.
10. **Opções do campo «estado do projeto»** e regra de escolha serviço/solução.

---

## 8. Síntese de risco

| Área | Nível | Porquê |
|---|---|---|
| SEO | **Médio-alto** | Retirar 3 serviços indexados sem 301 = perda de rankings; NAP inconsistente destrói o SEO local |
| Backend/formulários | **Alto** | Novos campos + anexos + consentimento; upload exige segurança e infraestrutura |
| Funcionalidades | **Alto** | Agenda e área de cliente não validadas; podem não existir |
| Conteúdo/contratual | **Alto** | Fórmulas sobre «acompanhamento até à execução» mudam em 4 páginas — têm de ser sincronizadas |
| Processo | **Médio** | Sem staging e sem backup, qualquer erro fica público |
| Design/layout | **Médio** | Valores 4→6, serviços 2→3+7, processo 2 blocos: layouts podem não suportar |

---

## 9. Estado atual e próximos passos

**Feito**
- [x] Leitura e validação do `cm.md` (briefing completo)
- [x] Gap analysis das 13 secções
- [x] Entregáveis A–E
- [x] Documentação em `plan.md`
- [x] Memória do processo em `memoria.md`
- [x] **Fase 0-parcial** — auditoria do código real (HTML + `js/app.js` + `database/havre_design.sql`)
- [x] **Fase 1** — secções 1, 4 e 10 executadas (lista em `plan.md` §Estado da execução)
- [x] **Fase 2** — secções 3 (Sobre), 8 (Processo) e 2 (Início) executadas
- [x] **Fase 3** — secções 5 (HAVRE Soluções), 9 (FAQ) e markup das 6 e 7 executadas
- [x] **Fase 4** — secções 11 e 13 no âmbito estático (testemunhos ocultos; portefólio e logótipo pendentes do cliente)
- [x] **Fase 5** — secções 12 e 13: testes estáticos (estrutura, ligações, imagens, contacto) e relatório
- [x] **Migração Laravel — B0, B1, B2** (2026-09-25) — backup + `git init` + `.gitignore`; projeto `backend/` (Laravel 12, `.env` → `havre_design`, `APP_NAME=HAVREDESIGN`, `APP_LOCALE=pt`); migration `2026_09_25_000000_reconcile_baseline_schema.php` (§6 completo) → 23 tabelas, `migrate:status` Ran, `verify.sql` 0 órfãs, `artisan serve` HTTP 200
- [x] **Migração Laravel — B3, B4** (2026-09-25) — 15 models com trait `HasUuid` + relações + `User::isAdmin()`; middleware `role` registado em `bootstrap/app.php`; seeders: 10 serviços (3+7) com 21 itens de «inclui», 6 projetos com `slug`/`published`, 5 HAVRE Soluções, 18 settings (contacto/redes/WhatsApp/agenda em JSON), admin com hash real (`ADMIN_PASSWORD` no `.env`); `Hash::check` true, 0 órfãs
- [x] **Migração Laravel — B5** (2026-09-25) — `layouts/app.blade.php` + `partials/{seo,header,footer,whatsapp}` com `$site` partilhado (`AppServiceProvider` → `Setting::many` → `View::share`); 14 rotas públicas (*stubs* para B6, hero real em `home`); assets copiados para `public/` (assets, `css/styles.css`, `js/app.js`, logo/favicons). Verificado: 14/14 HTTP 200, rodapé a refletir `Setting::set('contact.hours', …)`, nav ativa por `routeIs`, toasts e loader OK
- [x] **Migração Laravel — B6–B10** (2026-09-25) — 16 páginas convertidas de `ui/` para Blade; formulários reais (CSRF, validação PT, anexos, 4 e-mails); agenda real (`AgendaService` + disponibilidade + anti-duplicados); Breeze com URIs em PT e `/conta`; painel admin com 41 rotas `role:ADMIN` + 10 controllers CRUD + 21 vistas. Testado ao vivo em todas as fases
- [x] **Migração Laravel — B11** (2026-09-27) — **`localStorage` desligado**: `public/js/app.js` reescrito (695 → 133 linhas) sem seeds, *data accessors*, estado de utilizador local nem render morto de header/footer/WhatsApp; `limparLocalStorageLegado()` apaga `havre_*` no arranque; utilizador servido por `@json(auth()->user())` em `booking/create` e `project/create`; serviços/portefólio só em Eloquent+Blade; BOM de `routes/auth.php` removido. Verificado: 16 públicas + 11 admin/conta 200 sem resíduos, sintaxe `node --check`, login a servir o utilizador, submissão real de agendamento (registo depois removido)
- [x] **Migração Laravel — B12** (2026-09-27) — **`php artisan test` verde: 92 testes / 352 asserções** (era 24 falhas). *Testes:* migration `2026_09_24_000000_baseline_tables_if_missing` (schema completo só quando falta — no-op no MySQL do dump), guards no `create_users_table`, testes Breeze reescritos para **URIs em PT** e `ProfileTest` apagado (a rota `/profile` morreu na B9), 7 ficheiros de teste novos (páginas públicas, contacto, projeto, agenda, permissões/conta, segurança, SEO). **2 bugs reais encontrados:** (1) `password_reset_tokens.reset_token` em vez de `token` → a reposição de palavra-passe rebentava em produção (corrigido na reconcile + migration de rename na BD do dump); (2) `where('appt_date')` sem `whereDate()` → o cast `date` grava `YYYY-MM-DD 00:00:00` no sqlite, o *duplicado* escapava à validação e rebentava no índice UNIQUE (corrigido em `AgendaService` e `DashboardController`). *Segurança:* `SecurityHeaders` global (nosniff, SAMEORIGIN, Referrer-Policy, Permissions-Policy, COOP, `X-Powered-By` removido, **HSTS só em produção**), `.env.production.example` com `APP_DEBUG=false` + `SESSION_SECURE_COOKIE=true`, `.env` confirmado fora do Git, *throttle* dos POSTs e do login cobertos. *SEO:* `/robots.txt` e `/sitemap.xml` passam de ficheiros a **rotas** (`SeoController`), `RedirectLegacyUrls` + `RedirectSeeder` com 37 mapeamentos (`ui/*.html` + `plan.md` §C). Guarda `APP_CONFIG_CACHE` no `phpunit.xml` para um `config:cache` não mandar os testes para a BD errada
- [x] **Migração Laravel — B12.5 + remoção do atendimento em escritório** (2026-09-27) — `ADMIN_PATH` editável no `.env` (testes a correr com `ADMIN_PATH=backoffice`); cartão «Reunião no Escritório» fora de `/agendar`, «Marque uma reunião presencial» fora de `/contacto`, e-mail de confirmação neutro, tipo `OFFICE` eliminado por migration nova (`enum('SITE','ONLINE') DEFAULT 'ONLINE'`, `settings.agenda` reescrita, nova chave `process.case_local`), editor de *rótulo + nota* dos tipos de reunião e campo «Caso real → Local» em `Definições`. **`php artisan test` verde: 101 testes / 384 asserções** (ver `memoria.md` decisão 63)
- [x] **Migração Laravel — imagens responsivas** (2026-09-27) — componente `<x-responsive-image />` (+ variante `<x-responsive-hero />`) sobre o pacote `zoker/responsive-images` **v1.3.3**: `<picture>` + `<source type="image/webp">` com `srcset` por *preset*, `<img>` de fallback no **original** png/jpeg/jpg, `width/height` do ficheiro (sem upscale) contra CLS, `loading="lazy"` por omissão e `eager` + `fetchpriority="high"` só no loader e no logótipo do cabeçalho; `onerror` mantido via prop `fallback`. Disco **`web`** novo (raiz `public_path()`), `presets` (`hero`/`content`/`gallery`/`card`/`thumbnail`), `RESPONSIVE_IMAGES_QUEUE=false` nos três `.env`, variantes em `public/responsive-images/` (fora do Git). **17 imagens públicas migradas** (admin, CSS e o `background-image` do hero não tocados); alias `responsive-image` redirecionado no `AppServiceProvider` para a vista da aplicação. **`php artisan test` verde: 110 testes / 489 asserções** (ver `memoria.md` decisão 64)
- [x] **Migração Laravel — página `/networking`** (2026-09-28) — rota permanente `Route::view('/networking', 'networking')->name('networking')` + vista `networking.blade.php` (hero, QR 200×200 ou aviso, CTA «Falar connosco»); links no rodapé **e no menu de topo** (pedido posterior do cliente); **QR editável no painel** (fieldset «Networking» em `admin/definicoes`, upload → `public/images/qrcode-networking.png` no disco `web`, chave `networking.qr_image`, nome de ficheiro fixo ⇒ URL fixo); `networking.blade.php` fica **fora** da regra «sem `<img>` cru» (`ResponsiveImagesTest`); removidos a pedido a URL por baixo do QR (e no aviso de fallback), o botão «Voltar ao site» e o FAQ do menu de topo; **no sitemap** (`priority 0.5`). **`php artisan test` verde: 118 testes / 523 asserções** (ver decisão 65)
- [x] **Migração Laravel — processo em 5 etapas** (2026-09-28) — `/processo` passa de 2 blocos (5 + 6 etapas) para **um bloco**: BRIEFING · CONCEITUAÇÃO · PRODUÇÃO · APROVAÇÃO · ENTREGA; preview da home de 3 → 5 cartões (`lg:grid-cols-5`); resposta da FAQ reescrita; «defindo» → «definindo» (ver decisão 66)

**Pendente (requer o cliente)**
- [x] ~~**Backend Laravel**~~ → **em execução**: plano em **`laravel.md`** (B0→B13) · especificação em `backend.md` · **B0–B12 concluídas (2026-09-27)** — falta **B13 (publicação)**; §15 de `backend.md` lista as 11 perguntas por responder
- [x] ~~Textos legais: Política de Privacidade e Termos de Uso~~ → **criados** (`politica-privacidade.html`, `termos-uso.html`) e ligados no rodapé, no consentimento e no login; **falta validação jurídica**
- [x] ~~Definição dos itens «O que inclui» dos 3 serviços principais~~ → **redigidos 7 itens por serviço**; **falta aprovação do cliente**
- [x] ~~Testemunhos~~ → **6 versões redigidas** (3 início + 3 FAQ), **mantidas ocultas** até aprovadas
- [x] ~~Agenda dispersa em vários ficheiros~~ → centralizada em **`AGENDA_CONFIG`** (`js/app.js`)
- [ ] Imagens para os 7 serviços complementares
- [ ] **Portfolio real** (substituir os 6 placeholders de `DEFAULT_PORTFOLIO`) + `PORTFOLIO_SEED_VERSION`
- [ ] **Logótipo novo** (substituir `logo.png` / `LOGO-HAVREDESIGN.jpeg`)
- [x] ~~URL do Facebook do rodapé~~ → resolvido (redes sociais confirmadas)
- [x] ~~Confirmação do WhatsApp~~ → confirmado: `wa.me/244926184104`
- [ ] Confirmação das opções da agenda (horários, dias e «Reunião Online») — tudo em `AGENDA_CONFIG`
- [x] ~~Compressão das imagens~~ → **WebP responsivo resolvido pelo componente** (`<x-responsive-image />`); a **compressão dos originais** fica a cargo do cliente (medido: hero 863 KB, 3,58 MB servidos — avisar para fazer backup antes)
- [ ] Fase B13 da migração (`laravel.md` §7) — **B0–B12 concluídas**
- [x] ~~**`/networking` fora do sitemap**~~ → **resolvido (2026-09-28)**: `SeoController::PAGINAS` inclui `/networking` (`priority 0.5`, `changefreq yearly`) e `SeoTest` valida o `<loc>`
- [ ] **QR de `/networking`**: confirmar que a imagem foi gerada com `https://www.havredesign.ao/networking` e testar o *scan* em telemóvel (substituível em `Definições → Networking`)
- [x] ~~`php artisan test` **vermelho**: 24 falhas~~ → **resolvido no B12**: **92 testes / 352 asserções verdes** em `sqlite::memory:` (schema completo por migration + testes reescritos para as URIs em PT); hoje **118 testes / 523 asserções** (após B12.5, remoção do escritório, imagens responsivas, `/networking` e processo em 5 etapas)

**Não foi feito (fora do pedido até agora)**
- **WebP/compressão dos originais** — o **WebP responsivo já é gerado pelo componente**; a compressão manual dos originais continua a cargo do cliente (medido: hero 863 KB, 3,58 MB servidos).
- Teste **visual em navegador** — `php artisan test` cobre formulários, agenda, auth, permissões, cabeçalhos e SEO; a validação em PC/telemóvel continua por fazer (não há navegador neste ambiente).
- Teste visual em dispositivo — responsividade **aplicada** (ver `plan.md` §9), mas a **validação visual em PC/telemóvel continua por fazer** (não há navegador neste ambiente).
- ~~Todo o backend (`backend.md`) — **adiado por decisão do cliente**~~ → **migração em curso** (fases B0–B12 concluídas; ver `laravel.md`).
- **301 confirmados com analytics reais** — o `RedirectSeeder` traz as hipóteses do `plan.md` §C; é preciso confirmá-las com o sitemap e o Search Console antigos antes de publicar.
- Nenhuma publicação: as alterações estão apenas nos ficheiros locais.

---

## 10. Como retomar este trabalho

1. Abrir `plan.md` → secção **«Estado da execução»** (diz o que está feito e o que bloqueia).
2. Começar pelo **§A — 10 perguntas críticas**; sem respostas, a Fase 0 completa não arranca.
3. Executar a **Fase B** por ordem — as **5 fases estáticas estão concluídas**; o que resta depende do cliente (portefólio, logótipo, textos legais, imagens) ou da migração para Laravel (`backend.md`).
4. Usar o **§D** como checklist de saída e o **§E** para monitorizar SEO 2 e 6 semanas após publicar.
5. Atualizar este ficheiro à medida que as lacunas forem resolvidas.

---

## 11. Verificação empírica (Fase 0-parcial) — assumido vs. real

A tabela abaixo corrige as suposições da gap analysis com o que foi encontrado no código.

| Ponto | O briefing assumia | Realidade no código |
|---|---|---|
| Backend | Site com base de dados funcional | **Não existe.** `app.js` usa `localStorage` para serviços, portefólio, agendamentos, projetos e utilizadores. `database/havre_design.sql` é um schema derivado do localStorage, **não está ligado a nada** |
| Formulários | Entregam pedidos à empresa | **Não entregam** — os dados ficam no `localStorage` do visitante. Só o próprio visitante os vê, no seu navegador |
| Área de cliente | Existe com login | Existe como interface (`login.html`, `dashboard.html`, `admin.html`), mas a «palavra-passe» fica em `localStorage` — **sem autenticação real, sem isolamento de dados entre clientes** |
| Agenda | Marcações reais | Gravadas em `localStorage`, **sem bloqueio de duplicados, sem disponibilidade configurável**. ENUM da tabela `appointments` só tem `OFFICE`/`ON_SITE` (falta o terceiro tipo «online» do briefing) |
| Serviços | «Arquitetura & Construção» + «Interiores & Design» | Eram **5 serviços diferentes**: Design de Interiores, Projetos Comerciais, Projetos Residenciais, Consultoria em Design, Reforme e Retrofit |
| Menu | Divergente | **Já correspondia exatamente** à lista do briefing (8 itens + Entrar + Iniciar Projeto) |
| E-mail | Inconsistente | Rodapé tinha `info@havredesign.com`; página Contacto tinha `info@havredesign.ao` (o correto) |
| Morada | 2 variantes | **5 variantes**, incluindo typo «administação» em `contacto.html` (×2) e rodapé |
| Horário | Presente | Só em `contacto.html`; ausente do rodapé |
| WhatsApp | Por identificar | O botão flutuante já usava `wa.me/926184104` → indício de que o WhatsApp é o +244 926 184 104 (**a confirmar**) |
| Política/Termos | Existem | **Não existem** — `href="#"` no rodapé |
| Preços | 4 preços fixos | Confirmados em `orcamento.html` (350.000 / 850.000 / 1.200.000 / 1.500.000 Kz) e faixas de orçamento no formulário |
| Tour virtual | Possivelmente existe | Existe como item de um pacote em `orcamento.html` (linha «V Tour virtual») — não como página |
| Portefólio | Desatualizado | 6 itens de exemplo no `localStorage` + 8 imagens em `assets/portifolio/` |

**Consequência estratégica:** as secções 6 (formulário), 7 (agenda) e a área de cliente do briefing **exigem backend** — não são edições de conteúdo.

> **Decisão tomada (cliente):** migrar o projeto para **Laravel** e fazer o backend depois.
> Especificação completa em `backend.md` (migrations, rotas, regras de negócio, plano B0–B8).
> Enquanto isso, **só se executa conteúdo estático**.

---

## 12. Registo de alterações — Fase 1

A lista detalhada ficheiro-a-ficheiro está em `plan.md` → «Alterações feitas (Fase 1)».
Resumo do que mudou e porquê:

- **Secção 4** (a maior): o catálogo de serviços foi reescrito de raiz em `app.js` com os textos literais do briefing, dividido por `group: principal | complementar`. `servicos.html` passou a renderizar os dois blocos. Como o site guarda serviços em `localStorage`, acrescentou-se uma versão de seed (`SERVICES_SEED_VERSION`) para que os visitantes já existentes recebam o catálogo novo — sem isso, os antigos 5 serviços continuariam a aparecer.
- **Secção 1**: eliminadas as restantes ocorrências de «Arquitetura & Construção», normalizados os 4 CTAs do briefing e a grafia «HAVREDESIGN» em todos os `<title>`.
- **Secção 10**: rodapé, contacto, agendar e processo passaram a usar o mesmo e-mail, a mesma morada completa e o mesmo horário; a mensagem do WhatsApp passou a ser a do briefing.

**Verificações efetuadas:** `node --check ui/js/app.js` ✅ · sintaxe dos scripts inline das **14 páginas HTML** ✅ · pesquisa por resíduos («Arquitetura & Construção», «Criar o meu espaço», «Havre Design», âncoras antigas, e-mail antigo, typo da morada) → **0 ocorrências** ⚠️ *ainda não foi feito teste visual em navegador — ver §9.*

### Fase 2 — secções 3, 8 e 2

Lista detalhada em `plan.md` → «Alterações feitas (Fase 2)». Resumo:

- **Sobre (3):** acrescentados **Propósito** e **Filosofia** (etimologia HAVRE), substituídos Missão e Visão
  pelas fórmulas do Plano de Negócio, Valores passaram de 5 para **6**, fundadora renomeada para
  «Arq.ª Janette Rodrigues da Conceição», e removida a promessa «até à execução».
- **Processo (8):** a timeline antiga (Briefing → Entrega) foi **dividida em dois blocos** —
  Processo Comercial (5 etapas) e Fluxo Operacional (6 etapas) — mais as notas de adaptação e de
  «construção não integra a oferta».
- **Início (2):** hero com os CTAs corretos e «Ver portefólio» condicional,
  introdução reescrita, novo bloco «Soluções para diferentes necessidades» com os 3 segmentos,
  e pré-visualização do processo alinhada com a secção 8.

**Decisão registada:** a secção 2 e a secção 4 do briefing pedem conjuntos de serviços **diferentes**
(2: «Interiores e Mobiliário» e «Consultoria e Serviços Técnicos»; 4/9: «Design de Interiores» e
«Fiscalização e Acompanhamento Técnico»). **Adotou-se a secção 4/9**, por ser a definição canónica
de serviços principais e estar repetida na FAQ. *A perguntar ao cliente.*

---

### Fase 3 — secções 5, 9, 6 e 7

- **Secção 5 (`ui/orcamento.html`)** — página reescrita por inteiro como **HAVRE Soluções**:
  título e subtítulo literais do briefing, separadores de pacotes/tabelas **removidos**, 5 cartões
  (Genesis, Evolution, Ready, Guardian, Prime) com os textos exatos, **todos os preços retirados**
  («Proposta personalizada»), botões «Conhecer solução» (acordeão) + «Solicitar proposta personalizada».
  `switchTab`/`togglePackage` substituídos por `toggleSolution`. Rodapé (`app.js`): «Veja o Orçamento»
  → «HAVRE Soluções» e «HAVRE Soluções» → «Solicitar orçamento» (havia dois links iguais).
- **Secção 9 (`ui/faq.html`)** — 6 → **12 perguntas**, cobrindo literalmente as 9 linhas do briefing:
  serviços, princípios, complementares, HAVRE Soluções, processo, prazos, fiscalização, construção,
  estudo preliminar vs. execução, revisões, valores. As respostas antigas que contradiziam as secções 8
  e 9 («acompanhamos todas as etapas», «acompanhamento em pacotes») foram removidas.
- **Secção 6 (`ui/solicitar-projeto.html`)** — «Endereço do Projeto» → «Localização do Projeto»;
  acrescentados Área Aproximada, Estado do Projeto, Serviço Pretendido (11 opções), HAVRE Solução
  Pretendida (6), Segmento (4), Canal e Horário preferidos; **Anexos** com formatos e limite indicados
  mas **desativados**; **consentimento obrigatório** com ligação à Política de Privacidade. O submit
  local passou a guardar e mostrar os campos novos.
- **Secção 7 (`ui/agendar.html`)** — terceiro tipo **«Reunião Online»**; texto da taxa de deslocação
  reescrito literalmente; rótulos de tipo deixam de ser binários (`dashboard.html`, `admin.html`).

**Decisões registadas na Fase 3:**

| # | Situação | Decisão tomada |
|---|---|---|
| 21 | Anexos: o briefing pede «permitir» ficheiros | **Input desativado** com aviso explícito — um upload que só grava em `localStorage` seria falso; o envio real fica para o Laravel (`backend.md` §9) |
| 22 | «Serviço pretendido» é apresentado «como alternativa» às HAVRE Soluções | Dois campos independentes e opcionais, com nota «escolha um serviço ou uma solução» e a opção «Não sei — preciso de orientação» em ambos — evita um interruptor com mais JS e mais falhas |
| 23 | Campos novos do formulário não têm para onde ir | Guardados no `localStorage` local (mesmo comportamento pré-existente da página) e **visíveis no resumo**; nada chega à empresa — já documentado como pendente do backend |
| 24 | «Reunião Online» sem plataforma de videochamada | Descrição «Conversa por videochamada ou telefone, no horário combinado» — **não** promete ligação enviada por e-mail, que exigiria backend |
| 25 | Linha «Criar conta» do briefing | **Não aplicável**: o convite de registo já não existe no formulário (só em `login.html`) |
| 26 | Agenda real e anti-duplicados | **Não feito** — depende de disponibilidade autenticada (`backend.md` §7). A página mantém o calendário e os horários fixos atuais |
| 27 | Destino duplicado no rodapé (dois links para `orcamento.html`) | Eliminado: «HAVRE Soluções» nos links rápidos, «Solicitar orçamento» na coluna de serviços |

**Verificação da Fase 3:** 14 páginas — scripts inline + `app.js` válidos (`new Function`),
tags `<section>/<div>/<form>/<label>/<select>/<fieldset>/<p>/<span>` balanceadas, 0 resíduos de
preços, pacotes, «Tabela Referencial» e do texto antigo da taxa.

---

### Fase 4 — secções 11 e 13 (estático)

- **Testemunhos ocultos** em `index.html` e `faq.html`: a secção ficou no markup com
  `class="hidden"` e `data-testimonials="pending-update"` — é só remover o `hidden` quando os
  testemunhos forem atualizados (é o que o briefing manda: «mantém as secções ocultas»).
- **Portefólio não alterado** — o briefing remete para «o portfolio enviado anteriormente», que não
  está no repositório. Os 6 itens atuais em `DEFAULT_PORTFOLIO` são placeholders genéricos
  (Residência Moderna, Escritório Corporativo…).
- **Logótipo não alterado** — `logo.png` (122 KB) e `LOGO-HAVREDESIGN.jpeg` existem e são usados no
  cabeçalho e rodapé com fallback; a atualização exige o ficheiro novo do cliente.

### Fase 5 — testes estáticos (secções 12 e 13)

Feito (relatório completo em `plan.md` → «Relatório de testes estáticos»):

- **Estrutura:** 14 páginas, scripts válidos, tags balanceadas.
- **Ligações:** 27 destinos locais verificados — **0 em falta**, âncoras OK.
- **Correções de contacto feitas:** `wa.me/926184104` → `wa.me/244926184104` em 3 páginas;
  `tel:+926184104` → `tel:+244926184104` no rodapé. *(O número estava sem código de país:
  o link de WhatsApp não abria Angola.)*
- **Imagens:** 15/15 com `loading="lazy" decoding="async"`.
- **Pendências técnicas:** 12 MB de imagens (hero 3,2 MB); `https://facebook.com` é placeholder;
  testes funcionais e de móvel não executáveis sem navegador/backend; nada publicado.

**Decisões registadas nas Fases 4 e 5:**

| # | Situação | Decisão tomada |
|---|---|---|
| 28 | Testemunhos: briefing manda manter ocultas | Ocultar com classe `hidden` **em vez de apagar** — preserva o markup para reativar sem reescrever |
| 29 | Portefólio e logótipo exigem ficheiros do cliente | **Nada inventado** — registado como pendente; não se substitui conteúdo real por conteúdo escrito por nós |
| 30 | O `initStorage()` só preenche o portefólio se estiver vazio | Quando chegar o portfolio real será preciso um `PORTFOLIO_SEED_VERSION`, como já se fez com os serviços — senão os visitantes antigos não vêem a atualização |
| 31 | Imagens: 12 MB, hero a 3,2 MB | Acrescentado apenas `loading="lazy"` (reversível, sem risco). **Recompressão/`webp` não feita** — altera binários sem versionamento (o repo não é Git), exige backup e aprovação |
| 32 | WhatsApp `wa.me/926184104` em 3 páginas | Corrigido para `wa.me/244926184104` — sem código de país o link não abre o WhatsApp angolano |
| 33 | `https://facebook.com` no rodapé | ~~Mantido e reportado~~ → **Resolvido**: o cliente forneceu as redes oficiais; Facebook, Instagram e LinkedIn atualizados e ganharam `aria-label` |
| 34 | Testes funcionais (formulários, área do cliente, agenda) | **Não executados** — exigem navegador e backend; registados como não cobertos por esta entrega |
| 35 | Número do WhatsApp em falta | **Confirmado pelo cliente:** `https://wa.me/244926184104` — fecha a pendência do WhatsApp; acrescentado ícone do WhatsApp na fila de redes do rodapé |

### Redes sociais confirmadas (2026-09-25)

| Rede | URL |
|---|---|
| Instagram | `https://www.instagram.com/havredesign.ao/` |
| Facebook | `https://www.facebook.com/profile.php?id=61594010232462` |
| LinkedIn | `https://www.linkedin.com/company/havredesign/` |
| WhatsApp Business | `https://wa.me/244926184104` |

Todas aplicadas em `ui/js/app.js` → `renderFooter()` (único sítio onde as redes aparecem).
Antes: Instagram com parâmetros de QR (`havre_design?igsh=…`), Facebook placeholder, LinkedIn pessoal/antigo.

### Verificação adicional pedida pelo cliente (ícones, WhatsApp, telefone, botões)

Pedido: (1) ícones em todas as páginas + rodapé · (2) WhatsApp no flutuante e nos botões de
contacto/orçamento · (3) redes em novo separador · (4) telefone `+244 926 184 104` com `tel:` ·
(5) testar todos os botões em PC e telemóvel.

- **(1)** Como os ícones estão dentro de `renderFooter()`, estão nas 14 páginas; acrescentado o
  ícone do WhatsApp à fila (Instagram/Facebook/LinkedIn já estavam só no rodapé).
- **(2)** 6 ligações `wa.me/244926184104`: botão flutuante (`renderWhatsAppButton`), `contacto` ×2,
  `faq`, `solicitar-projeto`, rodapé. Todas com a mensagem do briefing onde faz sentido.
- **(3)** 10/10 externas com `target="_blank"` + `rel="noopener noreferrer"` (faltava o `rel` em
  `solicitar-projeto.html`).
- **(4)** `tel:+244926184104` no contacto e no rodapé; os outros dois números também em `tel:`.
- **(5)** Testes **estáticos** executados: handlers todos definidos, 0 botões mortos, 27 destinos
  internos existentes, 0 imagens sem `alt`, campos públicos com label. **Não foi possível testar em
  PC/telemóvel** — não há navegador neste ambiente; fica registado como pendente de validação manual.

**Decisões adicionais:**

| # | Situação | Decisão tomada |
|---|---|---|
| 36 | Ícones das redes só existiam no rodapé | Mantidos lá — o rodapé é partilhado pelas 14 páginas via `renderFooter()`, por isso «todas as páginas» fica cumprido sem duplicar código |
| 37 | `admin.html` tinha 14 campos só com placeholder (0 labels) | Acrescentados `<label class="sr-only">` — não altera o visual e fecha o teste de acessibilidade |
| 38 | Teste de botões em PC/telemóvel pedido pelo briefing | Feita a **parte automatizável** (destinos, handlers, labels, `target`); a validação visual/funcional em dispositivo fica **pendente de quem tiver navegador** — não se afirma como testada |

### Responsividade (aplicada a pedido)

Auditoria automática (larguras fixas, grelhas sem breakpoint, tabelas, `nowrap`, `viewport`) +
correções. Detalhe completo em `plan.md` → «9. Responsividade aplicada».

- **Bugs reais corrigidos:** toast de 280px a rebentar ecrãs de 320px; âncoras tapadas pelo
  cabeçalho sticky; dias do calendário como alvo de toque; botão flutuante sem `safe-area`.
- **Ajuste mobile:** 34 blocos de espaçamento (`py-24`/`py-20` → `py-14 md:py-24`/`py-12 md:py-20`)
  e 11 títulos (`text-3xl` → `text-2xl md:text-3xl`) — **desktop fica idêntico** (`md:` preserva o valor atual).
- **Alvos de toque:** menu hambúrguer `p-2` → `p-3`; barras do assistente de agendamento com `flex-wrap`.

**Decisões adicionais:**

| # | Situação | Decisão tomada |
|---|---|---|
| 39 | Reduzir espaçamentos e títulos no telemóvel | Usar sempre o par `valor-sm md:valor-atual` — **nenhum ecrã ≥768px muda de aspeto**, só o telemóvel fica mais denso |
| 40 | `body { overflow-x: hidden }` escondia eventos de overflow | Preferiu-se corrigir as causas (toast, `break-word`, `flex-wrap`) em vez de confiar no `overflow-x` |
| 41 | Grelhas com `grid-cols-2` sem breakpoint no `index` e no `agendar` | **Mantidas**: são mosaicos pequenos/horários que funcionam a 2 colunas em 320px |
| 42 | Responsividade sem navegador para validar | Auditoria **estática** + mudanças de classes de baixo risco; a verificação visual em dispositivo continua **pendente** e não é dada como concluída |
| 43 | Mapa no `contacto.html` | Em vez de apenas o link, **iframe do Google Maps** (`output=embed`, sem API key) com a **mesma morada que já estava na página**; «Onde Estamos» + «Como chegar» mantidos abaixo. Verificado: 14 páginas OK |
| 44 | Logótipo — onde usar | Já usado no cabeçalho e no rodapé; **acrescentado** (a) `favicon`/`apple-touch-icon` em todas as páginas (não existia nenhum) e (b) a imagem no `login.html`, que ainda usava a marca só em texto |
| 45 | Loading screen | Ecrã de carregamento com **logo + barra animada** em todas as páginas. Esconde no evento `load` (JS) **com fallback só em CSS aos 3,5 s** — se o `app.js` falhar, o site nunca fica bloqueado. `z-index: 9998` para não cobrir os toasts |
| 46 | Texto do hero com «cor errada» (pedido do cliente) | Causa real: **219 classes `cor/NN` mortas** em todo o site (Tailwind CDN sem `tailwind.config` ignora a opacidade → a cor ficava herdada, navy sobre navy). **Fix global:** `tailwind.config` em todas as páginas com as mesmas cores de `styles.css` (cores base não mudam); o parágrafo do hero passou ainda a `text-primary-foreground` puro (branco, igual ao H1) |
| 47 | «O que inclui» dos 3 serviços principais | O briefing não define os itens (não inventámos até aqui). Por pedido do utilizador, **redigimos 7 itens por serviço** em `DEFAULT_SERVICES` + bump de `SERVICES_SEED_VERSION` → **ficam por aprovar** |
| 48 | Testemunhos | **6 versões redigidas** (3 `index`, 3 FAQ), em PT-PT e genéricas; **secções continuam `hidden`** com `data-testimonials="pending-update"` — só aparecem quando o cliente aprovar |
| 49 | Textos legais | Criadas `politica-privacidade.html` e `termos-uso.html` (modelo completo, PT-PT, direitos do titular, lei angolana) e **todas as ligações `href="#"` substituídas** (rodapé, consentimento, login). **Validação jurídica pendente** — não se afirma como texto aprovado |
| 50 | Agenda por fechar | **`AGENDA_CONFIG`** passa a ser a única fonte: horários, dias indisponíveis (sáb/dom), tipo pré-selecionado e rótulos dos tipos de reunião. `agendar.html`, `dashboard.html` e `admin.html` passaram a usar `getAppointmentTypeLabel()` — alterar a agenda é editar um objeto |
| 51 | Imagens | **Compressão fica a cargo do utilizador** («as imagens eu farei a compressão»); o trabalho técnico do site prossegue sem ela |
| 52 | Base de dados «já temos» | Verificada a 2026-09-25: o dump **não estava importado em lado nenhum**; importado com sucesso no XAMPP MariaDB 10.4 (porta 3306) → 7 tabelas InnoDB/utf8mb4, 0 órfãs. Detetados **11 desvios** (enum `appointments.type` errado para o site, colunas do formulário em falta, seed com 5 serviços antigos, tabelas novas do `backend.md` por criar, hash de admin inválido). Serviço `MySQL80` (MySQL 8) existe mas está parado e não arranca sem admin |
| 53 | **Front-end em Blade** (decisão do cliente) | «vamos migrar o front end para blade e a lógica integrar com funcionalidade real — rotas e comunicação com a base de dados». **`laravel.md` reescrito por completo**: opção «estático + API» abandonada; plano B0→B13 com mapeamento página→rota→view, layouts/partials Blade a substituírem `renderHeader`/`renderFooter`, formulários com CSRF+validação+e-mail, agenda lida de `settings`, auth Breeze e admin em Blade. `ui/` fica como referência/rollback até B13 |
| 54 | **B0–B2 executadas** (2026-09-25) | Backup `database/backup-2026-09-25.sql`, `.gitignore`, `git init` (primeiro commit **por pedir**); `backend/` criado (Laravel 12, PHP 8.2.12), `.env` ligado a `havre_design`, `APP_NAME=HAVREDESIGN`, `APP_LOCALE=pt`. A migration padrão `create_users_table` foi **registada manualmente** em `migrations` (o `users` do dump já existe — conflito com o UUID). Schema reconciliado por `2026_09_25_000000_reconcile_baseline_schema.php` (guards `hasColumn`/`hasTable`, idempotente): enum `appointments.type` → `OFFICE/SITE/ONLINE` (valores do `AGENDA_CONFIG`), `project_requests` com as colunas de `backend.md` §4.6 + FKs, `services.group`, `portfolio_items.slug/status/segment`, novas tabelas `solutions`, `contact_messages`, `attachments`, `settings`, `redirects`, `testimonials`, `availability_slots`, `appointment_blackouts` e padrão `password_reset_tokens`/`sessions`. Resultado: 23 tabelas InnoDB/utf8mb4, tudo Ran, `verify.sql` 0 órfãs, `artisan serve` HTTP 200. Divergências `laravel.md` §6 vs `backend.md` §4 resolvidas a favor de `backend.md` |
| 55 | **B3–B5 executadas** (2026-09-25) | Models (trait `HasUuid`, `role` fora do `$fillable`, `ServiceInclude`/`PortfolioGallery` com `$timestamps = false`), middleware alias `role`, seeders idempotentes (10 serviços/21 «inclui», 6 projetos com slug, 5 soluções, 18 settings, admin com `ADMIN_PASSWORD` no `.env`), layout Blade com `$site` partilhado e 14 rotas públicas (*stubs* até B6). Rotas `/entrar`, `/conta`, `/admin`, `/sair` **ainda não existem** — o header já aponta para elas (B9/B10) |
| 56 | **B6–B7 executadas** (2026-09-25) | **B6:** 16 páginas de `ui/` convertidas para Blade (consultas Eloquent em `@php` na view, portefólio por `{slug}` com 404, testemunhos ocultos omitidos, JS da página em `@push('scripts')`); 13/13 GET 200 e `view:cache` sem erros. **B7:** `ContactController`/`ProjectRequestController` + Form Requests `StoreContactMessageRequest`/`StoreProjectRequestRequest` (honeypot `homepage`, `throttle:5,1`) + 4 Mailables (markdown) + `lang/pt/validation.php` + `meta csrf-token` no layout. **Decisões:** anexos no mesmo `POST` (em vez de `POST /anexos` separado), 10 MB e 5 ficheiros, `pdf/jpg/jpeg/png/webp/zip/dwg` em `storage/app/private/requests/{id}`; selects de serviço/solução passam a vir da BD (`service_id`/`solution_id`, «Não sei» = nulo); `preferred_channel` guarda `email/phone/whatsapp`; o contacto **não tem caixa de consentimento** no `ui/` → `privacy_consented_at` fica nulo em `contact_messages`; `MAIL_ADMIN` no `.env`. Verificado com submissão real (registo na BD + anexo em disco + 4 `Message-ID` no log, erros de validação em PT, honeypot sem registo); **e-mails só chegam em log — SMTP por configurar (D3)** |
| 57 | **B8 executada** (2026-09-25) | `AgendaService` + `AppointmentController` (`POST /agendar`, `GET /agendar/disponibilidade?mes=`) + `StoreAppointmentRequest`. **Decisões:** fonte da agenda é `settings.agenda` servida como `window.HAVRE_AGENDA` (o `app.js` guarda só defaults); `availability_slots` vale como template semanal **quando existir** (tabela vazia → usa os horários das settings); antecedência mínima de **1 hora** (prazo a definir com o cliente); duplicados rejeitados no servidor **e** pelo índice UNIQUE da tabela (mesmo `CANCELLED` bloqueia — se o cliente quiser remarcar slots cancelados, retira-se o índice); erros de data/hora aparecem como toast + passo certo do assistente (os campos de data/hora são *hidden*, sem `@error` visível). Bugs corrigidos em teste: variável `$hora` inexistente nos mailables, `$errors->keys()->first()` sobre array, BOM introduzido por script PowerShell. Admin confirma/cancela → **B10** |
| 58 | **B9 executada** (2026-09-25) | Breeze 2.4 stack Blade. **Decisões:** **URIs em PT** (`/entrar`, `/registar`, `/sair`, `/recuperar-palavra-passe`, `/redefinir-palavra-passe`, `/confirmar-palavra-passe`, `/verificar-email`) mantendo os ***names* do Breeze** — os controllers (`redirect()->intended(route('dashboard'))`, `route('login')`, `password.*`) dependem deles; renomear *names* exigiria editar os controllers do vendor. Vistas de auth reescritas para `@extends('layouts.app')` (cromo completo do site) em vez de usar o layout `guest` do Breeze; `x-input-*` mantidos, botões com cores do tema; `lang/pt/auth.php` + `passwords.php`. `/conta` é o `route('dashboard')` do Breeze (mostra pedidos+agendamentos do próprio, com estados traduzidos); `profile`/`dashboard` do Breeze removidos (fora do âmbito, voltam se o cliente pedir perfis). `/admin` ganhou placeholder já com `role:ADMIN` (USER→403). **Riscos registados:** `breeze:install` sobrescreve `routes/web.php` e `layouts/app.blade.php` e exige `welcome.blade.php` — backups em `backend/_backup_b9/`; `npm install`+`vite build` criaram `public/build` (o site continua no Tailwind CDN — D7 pendente). Testado: registo/login/logout, erro PT, ADMIN→/admin, isolamento A/B, 16 GETs 200 |
| 59 | **B10 executada** (2026-09-25) | Painel admin completo: `routes/admin.php` (`prefix admin` + `['auth','role:ADMIN']`, 41 rotas, *names* **sem prefixo** — `servicos.index`, `portfolio.*`, … — para não partir chamadas existentes), `Admin\DashboardController` + 9 controllers CRUD, 21 vistas sob `resources/views/admin/` com `admin/layout.blade.php` (`@extends('layouts.app')`, nav ativa por `request()->routeIs`, flashes `ok`/`erro`, resumo de `$errors`), `app/Support/Media.php` (`Media::url/thumbnail` = mesmo contrato `$imagem` das vistas públicas), `php artisan storage:link`. **Decisões:** definições são **um único POST** com 5 fieldsets (notação de array `contact[email]`), agenda gravada por **substituição parcial** das 4 chaves editáveis (preserva `tipos`/labels); e-mails de estado de agendamento em `AppointmentStatusMail` com `try/catch` + `Log::error('appointment.status_mail_failed')` (D3: só log); utilizadores não podem alterar o próprio papel; uploads de capa → `storage/{services,portfolio}/`, galeria → `storage/portfolio/`, anexos de pedidos já existentes → disco privado (B7). **Correções em teste:** vista de definições rebentava com `Array to string conversion` quando `old()` devolvia array (`agenda.tipo_predefinido`) → helper `comoTexto()`; `Number::format` exige extensão `intl` **em falta** nesta PHP 8.2.12 → evitar (só afetou um comando CLI, o site não usa). **Testado ao vivo:** serviço editado → visível no site (critério B10), definições roundtrip (marcador gravado + homepage refletida + `tipos`/`horarios` preservados + original restaurado), agendamento → `CONFIRMED` com e-mail «O seu agendamento foi confirmado» no log, mensagens show/PUT/DELETE com flashes, permissões (anónimo 302, USER 403, ADMIN 200), 16 admin + 16 público 200, `view:cache` ok; BD limpa (só admin, 0 agendamentos/pedidos/mensagens), 46 temporários apagados. **Pendente:** repositório Git **sem commits** (o `git init` da B0 não tem primeiro commit — por pedir); checklist visual §13 |
| 60 | **B11 executada** (2026-09-27) | `localStorage` desligado do site Laravel. **Decisões:** (a) serviços e portefólio **não** passaram por `@json` — já eram Eloquent + Blade desde a B6, e nenhum JS os consumia, portanto não se criou um `window.HAVRE_SERVICES` sem consumidor; a agenda mantém-se como `window.HAVRE_AGENDA` (é o único JS que precisa de dados); (b) o pré-preenchimento de nome/e-mail das views `booking/create` e `project/create` passou de `getCurrentUser()` (localStorage) para `@json(auth()->user() ? [...])` — continuando a não preencher para anónimos; (c) o ficheiro tinha **695 linhas**, das quais ~560 eram legado — `DEFAULT_SERVICES`, `DEFAULT_PORTFOLIO`, `SERVICES_SEED_VERSION`, `initStorage`, 11 *data accessors* e os `renderHeader`/`renderFooter`/`renderWhatsAppButton`/`renderLucideIcon` que a B5/B6 substituíram por partials Blade (morram, mas ficaram a servir links `*.html`); ficaram **133 linhas**; (d) a limpeza `havre_*` corre no `DOMContentLoaded` e é *best-effort* (`try/catch` para `localStorage` bloqueado) — se a página estática `ui/` for servida da mesma origem, o `initStorage()` dela volta a semear, logo não se parte nada; (e) ao testar, descobriu-se que **toda a resposta HTTP começava com um BOM** — estava em `routes/auth.php`, escrito por um script PowerShell na B9; removido (era invisível no HTML mas quebrava `ConvertFrom-Json` e é lixo no `Content-Type: application/json`). **Testado:** 16 públicas + 11 admin/conta 200 e sem `localStorage`/`*.html`/resíduos de render; `node --check` de `app.js` e dos scripts inline das 7 páginas principais; login de ADMIN → `const utilizador = {"name":"Administrador"}` (anónimo → `null`); `/agendar/disponibilidade` JSON limpo; submissão real de agendamento criou registo e foi apagada; `view:cache` ok. **Não coberto:** teste visual em navegador; ~~`php artisan test` continua vermelho (24 falhas, âmbito B12)~~ -> resolvido no B12 (92 testes verdes) |
| 61 | **B12 executada** (2026-09-27) | `php artisan test` **verde (92 testes / 352 asserções)**. **Decisões:** (a) em vez de tentar recriar o schema do dump à mão, criou-se `2026_09_24_000000_baseline_tables_if_missing.php` **antes** da reconcile, com guards `hasTable` — no MySQL do dump é no-op, no `sqlite::memory:` dá o schema todo; (b) a reconcile passou a criar `password_reset_tokens` com coluna **`token`** (a versão antiga criava `reset_token` e a reposição de palavra-passe rebentava com *Column not found*) + migration de rename para a BD já existente; (c) a troca de `enum` do `appointments` ficou **MySQL-only** — um `->change()` em sqlite reconstrói a tabela; (d) testes Breeze reescritos para as **URIs em PT** e `ProfileTest` **apagado** (a rota `/profile` não existe desde a B9); (e) **bug corrigido**: `where('appt_date')` → `whereDate()` — o cast `date` grava `2026-10-12 00:00:00` no sqlite, a comparação de strings falhava, o *duplicado* passava pela validação e morria no índice UNIQUE; (f) **`robots.txt` e `sitemap.xml` são rotas**, não ficheiros — o domínio canónico vem de `config('app.url')` e o sitemap da BD acompanha o portefólio publicado (`public/robots.txt` foi removido porque o servidor o servia estático e ignorava a rota); (g) `RedirectLegacyUrls` corre **antes do router** (apanha URLs que hoje dão 404) e só em GET/HEAD; `RedirectSeeder` idempotente com 37 mapeamentos (16 do `ui/*.html` + 21 hipóteses do `plan.md` §C — **por confirmar com analytics**); (h) `SecurityHeaders` global com **HSTS condicionado a `APP_ENV=production`** e `X-Powered-By` removido; (i) `.env.production.example` versionável (`.gitignore` ganhou `!.env.production.example`) para `APP_DEBUG=false` e `SESSION_SECURE_COOKIE=true` ficarem documentados sem pôr segredos em risco; (j) `phpunit.xml` ganhou **`APP_CONFIG_CACHE` próprio** — sem isto um `php artisan config:cache` do ambiente fazia os testes correrem contra a BD **mysql** (verificado: correram em sqlite e a BD ficou intacta). **Em aberto:** D7 (Tailwind por Vite), imagens (cliente), testes visuais, Search Console |
| 62 | **B12.5 executada (2026-09-27)** | Caminho do painel deixa de ser fixo e passa a ser editável no `.env`. **Decisões:** (a) a variável é `ADMIN_PATH` lida em `config/admin.php` (e não `env()` solto no código) — só assim funciona com `config:cache`; (b) o valor é saneado (sem barras, 3–32 caracteres, `[a-z0-9-_]`) e há lista de caminhos **reservados** às páginas públicas (`sobre`, `servicos`, `conta`, `up`, …) — em vez de rebentar as rotas, um valor inválido volta ao default `admin`; (c) mudou-se **só o `prefix`**, os *names* das rotas mantêm-se, por isso `route('admin')` continuou a servir de destino de login, de link no header, de link na `/conta` e de `Disallow:` no `robots.txt` — não ficou nenhuma string `/admin` viva; (d) **`/admin.html` passou a redirecionar para `/`**: um 301 devolve `Location: <caminho>` a qualquer visitante, o que exporia o segredo e anularia todo o obscurecimento; (e) os testes correm com **`ADMIN_PATH=backoffice`** no `phpunit.xml` — não é cosmético: é a prova de que nada ficou hardcoded, porque os 92 testes falhariam. **Verificado ao vivo:** com `ADMIN_PATH=gestao-havre`, `/admin` → 404, `/gestao-havre` → 302 `/entrar`, login a redirecionar para lá, painel 200 («Painel - Administração HAVREDESIGN»), header e `/conta` com o link novo, `robots.txt` com `Disallow: /gestao-havre`; depois reposto em `admin`. **Lembrete:** alterar o valor exige `php artisan optimize:clear` (as rotas ficam em cache com o caminho antigo). **Limites:** isto é *security through obscurity* — complementa, não substitui, `role:ADMIN` + `auth` + o *rate limit* do login. |
| 63 | **Remoção do atendimento em escritório** (2026-09-27) | Auditoria de 193 ficheiros + 5 fases (audição → plano → execução → testes → relatório). **Decisões do cliente:** (1) a morada institucional **mantém-se** e continua editável em `Definições → Contacto` (já existia); (2) o **iframe do Google Maps mantém-se** na página Contacto; (3) o valor `OFFICE` é **removido da BD** numa migration nova (`enum('SITE','ONLINE') DEFAULT 'ONLINE'`); (4) o e-mail de confirmação perde «Ficamos a aguardar por si no dia e hora combinados»; (5) o «Local» do case study *Casa Vila Nova* (`pages/process:208`) passa a ser **editável no painel**, com o texto atual por omissão; (6) apaga-se «Marque uma reunião presencial ou online» (`contact/index:269`); (7) o tipo pré-selecionado passa a **`ONLINE`**; (8) **novo editor dos tipos de reunião** (rótulo + nota de `SITE`/`ONLINE`) em `Definições`, porque a copy muda com frequência e estava hardcoded no seeder. **Execução:** `2026_09_27_000000_remove_office_meeting_type.php` (nova; nunca se editam as antigas) faz 3 coisas — reescreve `settings.agenda` (sem `OFFICE`, predef `ONLINE`, nota de `ONLINE` = «Conversa por videochamada ou telefone, no horário combinado.»), insere `process.case_local` e, **só em MySQL**, troca o enum (mesma regra da B12: um `->change()` em sqlite reconstrói a tabela); `SettingsSeeder` alinhado para instalações novas; `in:OFFICE,SITE,ONLINE` → `in:SITE,ONLINE` em `StoreAppointmentRequest` e `SettingController`; cartão OFFICE removido de `booking/create` (grid 3 → 2 colunas); `public/js/app.js`, `AgendaService`, `AppointmentController`, `Admin\AppointmentController` e o `admin/definicoes` sem qualquer menção a OFFICE. **Fora de âmbito (não tocado):** portefólio (`PortfolioSeeder` «Escritório Corporativo», `portfolio/escritorio-corporativo`), Eventos/Networking, as migrations antigas, publicação (B13) e Git (sem commits). **Testes:** **101 verdes / 384 asserções** (eram 92/352) — novos `SiteSettingsTest` (7) + 2 em `AppointmentTest` (rejeição de `OFFICE` e ausência do cartão). **Verificado ao vivo:** `/agendar`, `/contacto`, `/processo`, `/` 200 e sem `selectType('OFFICE')`, «Reunião no Escritório» nem «presencial»; `/admin/definicoes` com os fieldsets novos e sem `value="OFFICE"`; BD com `enum('SITE','ONLINE')` default `ONLINE`, `settings.agenda` sem `OFFICE` e `process.case_local` presente; sem U+FFFD nos valores gravados. |
| 64 | **Imagens responsivas** (2026-09-27) | Componente `<x-responsive-image />` (+ `<x-responsive-hero />`) sobre o pacote `zoker/responsive-images` **v1.3.3**. **Decisões do cliente (4):** (1) o hero da home **mantém o `background-image`** em CSS — só se otimiza o ficheiro, não se migra para `<picture>`; (2) os números são os **medidos** (hero 863 KB, 3,58 MB servidos — os «12 MB / 3,2 MB» do briefing não existem); (3) o `onerror` **mantém-se** (o componente aceita `fallback`, que o reproduz); (4) **sem AVIF** — entradas png/jpeg/jpg, `<source>` WebP e `<img>` no original. **Decisões técnicas:** disco novo **`web`** (raiz `public_path()`) porque o disco `public` não serve `public/assets/…`; `RESPONSIVE_IMAGES_QUEUE=false` nos três `.env` (com `QUEUE_CONNECTION=database` e sem *worker* a fila nunca teria gerado as variantes); *presets* `hero [1920,1600,1200]`, `content`/`gallery [1200,800,400]`, `card [800,400]`, `thumbnail [400,200]` com **alvo = `min(máx. do preset, largura original)`** (nunca upscale) e `gallery = content` de propósito para a colisão da *cache key* (`md5(disk|path|width|height)`); `sizes` com omissão `100vw` e ajustes por grelha. **Bug encontrado:** `<x-responsive-image>` servia a vista do **pacote** porque a classe tem precedência sobre a vista — resolvido com `Blade::component('components.responsive-image', 'responsive-image')` no `AppServiceProvider`, dentro de `booted()` (a primeira tentativa falhou: os argumentos de `component($classe, $alias)` estavam trocados e escrevia-se antes do pacote). **Migração:** 17 imagens públicas; admin, CSS e `solutions/index` intocados; closures `$imagem()` órfãos removidos; o `alt` do retrato da fundadora foi reparado (tinha U+FFFD). **Testes:** `ResponsiveImagesTest` (9) → **110 verdes / 489 asserções**; 10 páginas 200 com 3–9 `<picture>` e 53 variantes WebP geradas. **Fora de âmbito:** conteúdo do portefólio, Eventos/Networking, atendimento presencial, compressão dos originais, B13 e Git. |
| 65 | **Página `/networking` (QR permanente)** (2026-09-28) | Rota `Route::view('/networking', 'networking')->name('networking')` + vista `resources/views/networking.blade.php`. **Decisões do cliente:** (1) a URL é **permanente** — nunca muda, nunca redireciona (sem 301) e nunca é renomeada, porque o QR impresso aponta para ela; (2) a página é **só cromo + QR**: sem serviços, sem soluções, sem portefólio, sem PDF, sem CMS próprio e sem formulário; (3) o link fica no rodapé **e no menu de topo** — a FASE 5 previa «só rodapé», o cliente pediu depois o menu; (4) **o QR passa a ser editável no painel** (em vez de um ficheiro colocado à mão): *fieldset* «Networking» em `admin/definicoes` com `<input type="file" name="qr_image">`, validado (`image` + `mimes:png,jpg,jpeg,webp` + `max:4096`) e gravado **sempre no mesmo caminho** `public/images/qrcode-networking.png` (disco `web`, URL fixo) + chave `networking.qr_image` (criada sem migration — `Setting::set` faz `updateOrCreate`); (5) **`networking.blade.php` fica de fora** da regra «nenhuma vista pública com `<img>` cru» (`ResponsiveImagesTest`) — o QR é imagem estática escaneável e não deve ter `<picture>`/WebP/srcset. **Decisões técnicas:** `Route::view` (sem closure) para a rota ficar declarativa; aviso «O QR Code será publicado em breve» enquanto o ficheiro não existir; teste guarda e **repor o original** no `tearDown` para nunca apagar o QR do cliente; removidos a pedido o URL por baixo do QR, o botão «Voltar ao site» e o FAQ do menu de topo. **Testes:** `NetworkingTest` (8) → **118 verdes / 523 asserções**. **Resolvido no mesmo dia (2026-09-28):** `/networking` **passou a constar no sitemap** (`SeoController::PAGINAS`, `priority 0.5`, `changefreq yearly`, `<loc>` coberto pelo `SeoTest`) e o endereço **saiu também do aviso de fallback** (fica só «O QR Code será publicado em breve.»). |
| 66 | **Processo em 5 etapas** (2026-09-28) | O cliente forneceu o texto das etapas e pediu a substituição. **Decisões:** (1) os **2 blocos** de `/processo` («Processo Comercial» 5 + «Fluxo Operacional» 6) dão lugar a **um único bloco** — BRIEFING · CONCEITUAÇÃO · PRODUÇÃO · APROVAÇÃO · ENTREGA (mantida a estrutura de *timeline* com números); (2) o preview da home passa de **3 para 5 cartões** (`lg:grid-cols-5`); (3) a **resposta da FAQ** sobre o processo foi reescrita com as 5 etapas (manteve «As etapas são adaptadas…») para não contradizer a página; (4) ao texto enviado corrigiu-se **só** «defindo» → «definindo» (resto literal, incluindo «suas necessidades, seus objectivos»); (5) **não tocados:** Caso Real *Casa Vila Nova*, notas «Etapas adaptadas»/«Sobre a execução da obra», o restante menu e as páginas de serviços/soluções. |
| 67 | **Imagens do portefólio não carregavam após guardar** (2026-09-30) | Dois bugs encadeados, ambos afetavam só os uploads (as imagens do seeder em `public/assets/` serviam sempre). **(1) `public/storage` não era um symlink** — era uma pasta normal (só `.gitignore` e uma `portfolio/` vazia), provavelmente destruída ao extrair o `backend.zip`; o upload gravava em `storage/app/public/portfolio/`, mas a web root é `public/` → 404. **Atenção:** `php artisan storage:link` **não corrige isto sozinho** — recusa se `public/storage` já existir; foi preciso apagar a pasta e correr o comando outra vez. **(2) URLs de imagem com a porta errada:** `filesystems.disks.{web,public}.url` era `APP_URL` = `http://localhost:8001`, mas a app corre em `php artisan serve` na **8000** (o `asset()` do `Media::url` usa a raiz do pedido e estava certo; o `Storage::url()` do componente e do pacote usava o `APP_URL` e estava errado) → todo o `<img src>` apontava para uma porta sem servidor, e o `srcset` **gravado na cache do `zoker/responsive-images`** (cache na BD) misturava 8000/8001 conforme a altura em que fora gerado — por isso umas imagens apareciam e outras não. **Correção:** discos com URL **relativa** (`web.url=''`, `public.url='/storage'`) — as URLs passam a resolver contra o host do pedido e a cache deixa de concretizar host/porta; `APP_URL` alinhado para `http://localhost:8000` (com nota no `.env`: tem de coincidir com a porta do `serve`); `php artisan optimize:clear` para varrer a cache velha. `ResponsiveImagesTest`: 2 asserções passam a esperar caminho relativo. **Verificado ao vivo:** `/portfolio` e o detalhe sem qualquer `:8001`, todas as imagens com GET 200, upload de 2 imagens na galeria → 302 + visíveis + 200 (testes removidos a seguir) e **127 testes verdes / 551 asserções**. |



---

*Tagline final do briefing:* **HAVREDESIGN — Arquitetura como Refúgio, excelência em cada detalhe.**
