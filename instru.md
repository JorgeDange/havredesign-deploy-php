# HAVREDESIGN — Instruções de Colocação em Produção

> Website HAVREDESIGN — PHP 8.2 puro (MVC), sem frameworks, MySQL/MariaDB.
> Como colocar o site numa **hosting compartilhada (Apache)**.

---

## 1. Estado actual — pronto para hospedar?

| Verificação | Estado |
|---|---|
| Testes automatizados (`php tests/executar.php`) | OK 100/100 |
| Lint PHP (todos os ficheiros, 0 erros) | OK |
| Distorções de charset nas páginas públicas | OK (0) |
| Segurança (CSRF, headers, uploads, 404, path traversal) | OK |
| Projectos: 5 reais publicados, 0 demonstrativos | OK |
| `/havre-solucoes` (200) + 301 de `/orcamentos` | OK |
| Backup da BD criado | OK `database/backup-havre-design-2026-10-05.sql` |

**Resposta: SIM, o sistema está pronto para ser hospedado** — desde que se sigam os passos abaixo.

---

## 2. Backup da base de dados

Ficheiro já gerado:

```
database/backup-havre-design-2026-10-05.sql   (~193 KB, 23 tabelas, MariaDB 10.4)
```

Conteúdo: estrutura + dados (5 projectos publicados, 10 servicos, 5 HAVRE Solucoes, 20 definicoes, 36 redirects, 2 utilizadores, 1 mensagem de contacto, 1 agendamento).

Para gerar um backup novo:

```bash
mysqldump --user=UTILIZADOR --password --single-transaction --routines --triggers --default-character-set=utf8mb4 NOME_DA_BD > backup.sql
```

---

## 3. Pacote de deploy — o que enviar

**NAO SUBIR:**

```
backend/                   (Laravel antigo — apenas referencia)
ui/                        (mockups de design — apenas referencia)
.env                       (o .env LOCAL nunca sobe; criar um novo na hosting)
.git/
storage/logs/*             (logs locais — manter a pasta vazia)
storage/app/private/requests/*   (anexos de testes locais)
```

**SUBIR (arvore completa):**

```
.htaccess                  ← critico (redireciona para public/ + bloqueia pastas internas)
.env.example               (modelo para o .env de producao)
app/  config/  routes/  resources/     (codigo + views)
database/                  (inclui o backup .sql)
public/                    (front controller, CSS/JS, imagens, .htaccess)
public/storage/portfolio/  (23 imagens dos projectos)
public/uploads/servicos/   (imagens de servicos)
tests/                     (verificacao pos-deploy — recomendado)
storage/                   (pastas vazias: logs/, cache/, app/private/)
docs/, README.md, instru.md
```

**Destino:** a **raiz do dominio** (ex.: `public_html/`). O site foi desenhado para a raiz — NAO usar subdirectorio (`dominio.com/havredesign/`) sem ajustes ao `rota()`.

---

## 4. Pre-requisitos da hosting

- **PHP >= 8.2** com extensoes: `pdo_mysql`, `mbstring`, `curl`, `openssl`, `fileinfo`
- **MySQL 5.7+ / MariaDB 10.3+**
- **Apache com `mod_rewrite`** (habitual em hosting compartilhada)
- SSL/HTTPS activado (recomendado)

---

## 5. Passo a passo

### Passo 1 — Criar a base de dados

No painel da hosting (cPanel/Plesk):

1. Criar BD (ex.: `havre_design`)
2. Criar utilizador e **associar a BD com TODOS os privilegios**
3. Anotar: host (geralmente `localhost`), nome, utilizador, palavra-passe

### Passo 2 — Importar o backup

No **phpMyAdmin** da hosting:

1. Seleccionar a BD criada → separador **Importar**
2. Escolher `database/backup-havre-design-2026-10-05.sql`
3. Codificacao: **UTF-8** → Executar
4. Confirmar **23 tabelas**

### Passo 3 — Upload dos ficheiros

Por FTP/File Manager, subir o pacote do passo 3 para a raiz do dominio.

### Passo 4 — Criar o `.env` de producao

Na raiz, criar `.env` (a partir de `.env.example`):

```ini
APP_NAME=HAVREDESIGN
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.havredesign.ao
APP_LOCALE=pt

DB_HOST=localhost
DB_PORT=3306
DB_NAME=havre_design
DB_USER=utilizador_criado_na_hosting
DB_PASS=PALAVRA_PASSE_DA_HOSTING
DB_CHARSET=utf8mb4

SESSION_NAME=havre_sessao
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

ADMIN_PATH=admin

MAIL_FROM_ADDRESS=info@havredesign.ao
MAIL_FROM_NAME=HAVREDESIGN
MAIL_ADMIN=info@havredesign.ao
MAIL_TRANSPORT=log

LOGIN_MAX_TENTATIVAS=5
LOGIN_BLOQUEIO_SEGUNDOS=900
```

> ATENCAO: `APP_URL` = dominio **sem barra final** e **sem `/public`** (o `.htaccess` da raiz ja encaminha tudo para `public/`).
> ATENCAO: `APP_DEBUG` **nunca** `true` em producao.

### Passo 5 — Permissoes (escrita)

A app so escreve nestas pastas — o utilizador do PHP precisa de acesso de escrita (`755`; se a hosting exigir, `775`):

```
storage/                        (logs, cache, throttle, anexos privados)
storage/logs/
storage/cache/
storage/app/private/
public/storage/portfolio/       (uploads do portfolio via painel admin)
public/uploads/servicos/        (uploads de servicos via painel admin)
public/images/                  (QR da pagina Networking)
```

Se um upload falhar no admin, subir para `775` a pasta respectiva.

### Passo 6 — E-mail (recomendado)

O `Mailer` (`app/Core/Mailer.php`) suporta **dois transportes** (`MAIL_TRANSPORT` no `.env`):

- **`log`** (por omissao): os e-mails (ex.: recuperacao de palavra-passe) **nao sao enviados** — ficam em `storage/logs/mail.log`. Boa para testes.
- **`mail`** (ou qualquer outro valor): usa a funcao nativa `mail()` do PHP, que funciona na maioria das hostings partilhadas **desde que o dominio tenha e-mail configurado** (normalmente tem).

Para envio real em producao, pôr no `.env`:

```ini
MAIL_TRANSPORT=mail
MAIL_FROM_ADDRESS=info@havredesign.ao
MAIL_FROM_NAME=HAVREDESIGN
MAIL_ADMIN=info@havredesign.ao
```

> NOTA: nao ha suporte SMTP embutido (nao existem variaveis `MAIL_HOST`/`MAIL_PORT`/`MAIL_USERNAME`). Se a hosting exigir SMTP autenticado, e necessario adaptar `app/Core/Mailer.php`.

---

## 6. Verificacao pos-deploy (checklist)

| URL | Esperado |
|---|---|
| `https://DOMINIO/` | 200 — Home com as 6 etapas do processo |
| `https://DOMINIO/havre-solucoes` | 200 |
| `https://DOMINIO/orcamentos` | **301** → `/havre-solucoes` |
| `https://DOMINIO/sobre` | 200 |
| `https://DOMINIO/servicos` | 200 — 10 servicos |
| `https://DOMINIO/portfolio` | 200 — **exactamente 5 projectos** + filtros |
| `https://DOMINIO/processo` | 200 — 6 etapas |
| `https://DOMINIO/faq` | 200 |
| `https://DOMINIO/contacto` | 200 — enviar 1 mensagem de teste |
| `https://DOMINIO/networking` | 200 — QR visivel |
| `https://DOMINIO/sitemap.xml` | 200 — sem `/orcamentos` |
| `https://DOMINIO/robots.txt` | 200 |
| `https://DOMINIO/pagina-inexistente` | 404 sem stack trace |
| `https://DOMINIO/admin` | pede login |
| Menu do topo | «HAVRE Solucoes» (sem «Orcamento») |

**Testes automatizados (opcional, via SSH na hosting):**

```bash
php tests/executar.php     # esperado: Total: 100 | OK: 100 | Falhas: 0
```

(Necessario apontar o host no `tests/Apoio.php` ao dominio, ou correr so em ambiente local.)

**Login admin:** utilizadores no backup: `admin@havredesign.com` e `edjanepedro@gmail.com` (ambos ADMIN). Se a palavra-passe for desconhecida, usar «Recuperar palavra-passe» (requer `MAIL_TRANSPORT=mail`) ou redefinir por SQL com `password_hash()`.

---

## 7. Seguranca — confirmar em producao

- [ ] `APP_DEBUG=false` e `APP_ENV=production` no `.env`
- [ ] `.env` com permissao `644` e bloqueado via web (o `.htaccess` da raiz ja bloqueia `.env.*`)
- [ ] HTTPS activo + `SESSION_SECURE_COOKIE=true`
- [ ] Pastas internas (`app/`, `config/`, `routes/`, `storage/`, …) devolvem **403** (ja bloqueadas no `.htaccess` da raiz)
- [ ] `https://DOMINIO/.env` → 404
- [ ] `https://DOMINIO/index.php` → 404
- [ ] Palavra-passe do admin forte

---

## 8. Manutencao

- **Backup semanal da BD** (mesmo comando do passo 2 — guardar fora do servidor)
- **Logs**: `storage/logs/php-error.log` (revelar problemas sem `APP_DEBUG`)
- **Actualizacoes de seguranca**: aplicar patches PHP da hosting assim que disponiveis
- **Depois de alterar a BD**: gerar novo dump e guardar com a data

---

## 9. Problemas comuns

| Sintoma | Causa provavel / solucao |
|---|---|
| 500 Internal Server Error | Ver `storage/logs/php-error.log`; verificar extensoes PHP 8.2 |
| Paginas 404 (URLs amigaveis) | `mod_rewrite` desactivado na hosting |
| Imagens/ficheiros nao aparecem | `APP_URL` errado no `.env` (sem barra final, sem `/public`) |
| Uploads falham | Permissao de escrita na pasta (passo 5) |
| Login nao persiste | `SESSION_SECURE_COOKIE=true` exige HTTPS — activar SSL ou pôr `false` |
| E-mails nao chegam | `MAIL_TRANSPORT=log` — pôr `mail` (passo 6) |
| Charset estragado | Importar o dump com codificacao UTF-8 (passo 2) |

