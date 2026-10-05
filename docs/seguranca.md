# Documentação de segurança — HAVREDESIGN

Resumo das proteções implementadas (validáveis com `tests/suites/05_seguranca.php`
e `tests/checklist.md`).

## 1. Injecção SQL

- **Prepared statements obrigatórios:** todo o acesso passa por
  `Database::todos()/um()/exec()` com PDO e *placeholders* — nenhuma concatenação
  de valores na SQL.
- Nomes de tabelas/colunas nunca vêm do utilizador (apenas da aplicação).

## 2. XSS (cross-site scripting)

- Escape automático em todas as views: helper `e()` = `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`.
- `velho()` (redisplay de inputs) e `erro_de()` escapam ao sair.
- Saídas de confiança só via `<?= ... ?>` de variáveis já escapadas.

## 3. CSRF (falsificação de pedido)

- Token por sessão (`Csrf::token()`) em **todos** os formulários (`csrf_campo()`).
- Verificação com `hash_equals()` no middleware `csrf` (rota-marcada `'csrf'`).
- Falha → **HTTP 419** com página de erro (sem stack trace).

## 4. Autenticação

- `password_hash()` (bcrypt/argon) + `password_verify()`; nunca hashes reversíveis.
- Regeneração do ID de sessão no login.
- Bloqueio por tentativas: `LOGIN_MAX_TENTATIVAS` (5) em
  `LOGIN_BLOQUEIO_SEGUNDOS` (900 s) por conta+IP.
- Mensagens de erro genéricas (não revelam se o e-mail existe).
- Recuperação de palavra-passe: token aleatório **guardado como hash** na BD,
  expira, é de uso único; e-mail com link. A resposta do form é sempre igual
  (sem *user enumeration*).
- Sessão em cookies `HttpOnly`, `Secure` (em produção), `SameSite=Strict`.
- «Lembrar-me»: cookie opaco de 30 dias, HttpOnly+Secure+SameSite=Strict.

## 5. Autorização

- Middleware `auth` (exige sessão) e `admin` (exige papel `ADMIN`) nas rotas `/admin`.
- Área `/conta` exige sessão; cada utilizador vê apenas os **seus** registos
  (filtros por `user_id`/`user_email`).

## 6. Headers de segurança

Enviados em todas as respostas (middleware global):

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN` (+ CSP `frame-ancestors 'self'`)
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy` (limitado)
- HSTS **apenas** quando `APP_URL` é `https://` (produção)

## 7. Uploads

- Tipo real verificado com `finfo` (não pelo nome/extensão).
- Extensão na allowlist (imagens), tamanho máximo, nome gerado aleatoriamente.
- Pedidos de projeto: disco **privado** `storage/app/private/requests/{id}/` —
  servidos só pelo controller admin (não existem em `public/`).
- Serviços/portefólio: `public/storage/` — PHP não executável lá (ver `.htaccess`).
- Testado: ficheiro `.php` é rejeitado e nunca gravado.

## 8. Ficheiros e configuração

- `.env` na raiz, **fora** de `public/`, fora do Git (`.gitignore`).
- Erros: `APP_DEBUG=false` em produção; 500 genérico + log em
  `storage/logs/php-error.log` (nunca stack traces no HTML).
- `.htaccess` impede acesso directo a `index.php`, `storage/` e outros ficheiros
  sensíveis em `public/`.

## 9. Abuso de formulários

- **Honeypot** nos 3 formulários públicos (+ recuperação): campo invisível
  preenchido por bots → submissão ignorada sem gravação.
- **Throttle** (`app/Core/Throttle.php`): 10 submissões / 15 min por IP+acção,
  em ficheiros de `storage/app/private/throttle/`.
- Anti-duplicados da agenda: slot ocupado/blackout rejeitado.

## 10. Anti-open-redirect

- Os 301 legacy só redireccionam para caminhos iniciados em `/` (nunca `//` ou URLs externas).

## 11. Verificação contínua

```bash
php tests/executar.php    # inclui a suite 05_seguranca
```

Cobertura actual: CSRF (419), 404/405, headers, path traversal, `.env` inacessível,
uploads `.php`, 301 legacy, HEAD, ausência de stack traces.
