# HAVREDESIGN — Website (PHP puro)

Site institucional da HAVREDESIGN (arquitectura e design de interiores, Angola),
escrito em **PHP 8.2 sem framework nenhum** (MVC, PDO, zero dependências).

> Reescrito a partir do projecto Laravel original, que fica em `backend/`
> apenas como referência (não é servido).

---

## Requisitos

- PHP 8.2+ (extensões: `pdo_mysql`, `curl`, `fileinfo`, `mbstring`)
- MySQL 8+ (ou MariaDB equivalente)
- Apache com `mod_rewrite` (produção) — ou servidor embutido PHP (desenvolvimento)

## Instalação

```bash
# 1. Base de dados (schema já existe no projecto original)
mysql -u root -p -e "CREATE DATABASE havre_design CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p havre_design < database/schema.sql   # se existir dump

# 2. Configuração
cp .env.example .env        # preencher credenciais e APP_URL

# 3. Permissões (Linux/macOS; no Windows via XAMPP basta estar tudo escrevível)
chmod -R 775 storage/ public/storage public/uploads

# 4. Servir (desenvolvimento)
php -S 127.0.0.1:8090 router.php
# abrir http://127.0.0.1:8090/
```

### Produção (Apache)

- `DocumentRoot` → `public/`
- `AllowOverride All` (o `.htaccess` da raiz reencaminha para `public/`)
- Copiar `.env` para a raiz (fora de `public/`) **nunca** para dentro
- `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `APP_URL=https://dominio`

### Configuração (`.env`)

| Variável | Função |
|---|---|
| `APP_URL` | URL base (usada no sitemap/links absolutos) |
| `DB_*` | Ligação MySQL |
| `SESSION_*` | Nome, duração (min) e `Secure` do cookie de sessão |
| `ADMIN_PATH` | Prefixo do painel (por omissão `admin`) |
| `MAIL_*` | Remetente; `MAIL_TRANSPORT=log` grava em `storage/logs/mail.log` (dev) |
| `LOGIN_MAX_TENTATIVAS` / `LOGIN_BLOQUEIO_SEGUNDOS` | Bloqueio após tentativas falhadas |

## Estrutura

```
app/
  Controllers/       rotas de aplicação (Public/, Auth/, Admin/)
  Core/              Router, Database (PDO), View, Request, Response,
                     Session, Csrf, Auth, Validator, Mailer, Logger, Config, Throttle
  Middleware/         auth, admin, csrf, headers de segurança, 301 legacy, throttle
  Models/            acesso a tabelas (prepared statements)
  Support/           Helpers (e(), rota(), csrf_campo(), …) e Agenda
public/              DocumentRoot: index.php (front controller), .htaccess, assets
resources/views/     layouts/, partials/, pages/… (PHP puro), emails/
routes/web.php       todas as rotas (públicas + admin)
storage/             logs/, app/private/ (anexos dos pedidos), app/private/throttle/
tests/               executar.php + suites/ (automação) + checklist.md (manual)
config/.env.example  modelo de configuração
backend/             projecto Laravel original (referência, não servir)
```

## Como funciona

1. `public/index.php` recebe todos os pedidos → carrega `.env` e sessão/CSRF/headers.
2. **Middleware**: 301 legacy (tabela `redirects`) → router → `auth`/`admin`/`csrf`.
3. `routes/web.php` liga caminho+método a um controller (classe estática ou invocável).
4. `View::render('caminho', dados)` → partials → layout (`layouts/app.php`).
5. Base de dados: **sempre** `Database::todos()/um()` com *prepared statements*.

## Rotas principais

| Pública | Auth | Admin (`/admin`) |
|---|---|---|
| `/`, `/sobre`, `/servicos`, `/orcamentos`, `/portfolio`, `/portfolio/{slug}`, `/processo`, `/faq`, `/contacto`, `/solicitar-projeto`, `/agendar`, `/networking`, `/politica-de-privacidade`, `/termos-de-uso` | `/entrar`, `/registar`, `/recuperar-palavra-passe`, `/redefinir-palavra-passe/{token}`, `/conta` | Dashboard, serviços, portefólio+galeria, soluções, pedidos, agendamentos, mensagens, testemunhos, definições, utilizadores |

Endpoints úteis: `/robots.txt`, `/sitemap.xml`, `/agendar/disponibilidade?mes=AAAA-MM`.

## Testes

```bash
php -S 127.0.0.1:8090 router.php   # servidor tem de estar a correr
php tests/executar.php              # 5 suites → 99/99 (exit 0)
```

Revisão manual: `tests/checklist.md` (responsividade, performance, CMS, segurança).

## Documentação

- `docs/manual-cms.md` — manual de utilização do painel
- `docs/seguranca.md` — medidas de segurança implementadas
- `docs/manutencao.md` — manutenção diária, logs, backups, troubleshoot
- `modelo.md` / `memoria.md` — plano de execução e memória do projecto
