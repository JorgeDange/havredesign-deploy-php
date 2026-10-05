CONTEXTO:
Tenho um projeto Laravel 12 funcional (C:\Users\JORGE DANGE\Documents\Edjane\backend) com:
- 23 tabelas MySQL (havre_design)
- 17 páginas públicas
- Painel de administração (CMS)
- Formulários, agenda, autenticação, área de cliente
- SEO (sitemap, robots, 301)
- 118 testes verdes

PROBLEMA:
A hospedagem de destino NÃO suporta Laravel.
Preciso de reescrever o projeto em PHP puro (sem framework),
mantendo TODAS as funcionalidades e aplicando boas práticas de segurança.

OBJETIVO:
Criar um projeto PHP puro com:
1. Arquitetura MVC
2. URLs amigáveis
3. Segurança robusta
4. Base de dados MySQL (reaproveitar as 23 tabelas)
5. Design/HTML/CSS reaproveitado do Laravel (Blade → PHP templates)
6. Zero dependências de framework (só PHP + MySQL)

---

FASE 1 — AUDITORIA DO PROJETO LARAVEL (não alterar nada)

1. Listar todas as rotas em routes/web.php e routes/admin.php
2. Listar todos os controllers em app/Http/Controllers/
3. Listar todos os models em app/Models/
4. Listar todas as views em resources/views/
5. Listar todas as migrations em database/migrations/
6. Listar todos os seeders em database/seeders/
7. Listar todas as regras de validação (Form Requests)
8. Listar todos os Mailables
9. Listar todo o middleware
10. Listar as configurações em config/

Devolver uma tabela: o que existe em Laravel → o que precisa de ser reescrito em PHP puro.

NÃO alterar nada. Só inventariar.

---

FASE 2 — ESTRUTURA DO PROJETO PHP PURO

Criar a seguinte estrutura de pastas:

/
├── public/                  ← raiz pública (DocumentRoot)
│   ├── index.php            ← front controller (ponto de entrada único)
│   ├── .htaccess            ← URLs amigáveis
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
├── app/
│   ├── Controllers/
│   │   ├── Public/
│   │   └── Admin/
│   ├── Models/
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Database.php
│   │   ├── View.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Session.php
│   │   ├── Auth.php
│   │   ├── Csrf.php
│   │   ├── Validator.php
│   │   ├── Mailer.php
│   │   ├── Logger.php
│   │   └── Config.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── SecurityHeadersMiddleware.php
│   └── Support/
│       ├── Helpers.php
│       └── Redirects.php
├── config/
│   ├── app.php
│   ├── database.php
│   └── mail.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── partials/
│   │   ├── pages/
│   │   ├── admin/
│   │   ├── auth/
│   │   └── errors/
│   └── lang/
│       └── pt/
├── routes/
│   └── web.php
├── storage/
│   ├── logs/
│   ├── cache/
│   └── uploads/
├── database/
│   ├── migrations/
│   └── seeds/
├── .env
├── .env.example
├── .htaccess               ← redireciona para public/
└── README.md

---

FASE 3 — NÚCLEO (CORE)

Criar cada classe do Core com boas práticas:

3.1. Router.php
- Suporte a GET, POST, PUT, DELETE
- Parâmetros dinâmicos: /portfolio/{slug}
- Middleware por rota
- 404 personalizado
- URLs amigáveis

3.2. Database.php
- PDO com prepared statements (NUNCA concatenar SQL)
- Singleton
- Modo de erro: exceção
- Fetch: associativo
- Charset: utf8mb4
- Suporte a transações

3.3. View.php
- Renderização de templates PHP
- Layouts com secções (equivalente ao Blade @extends/@section)
- Escape automático de variáveis (htmlspecialchars)
- Partials reutilizáveis

3.4. Request.php
- Acesso a GET, POST, FILES, SERVER
- Sanitização de input
- Deteção de método HTTP

3.5. Response.php
- JSON, HTML, redirect
- Códigos de estado HTTP

3.6. Session.php
- Início seguro de sessão
- Regeneração de ID
- Cookies: HttpOnly, Secure, SameSite=Strict

3.7. Auth.php
- Login com password_verify()
- Hash com password_hash() (bcrypt ou argon2)
- Verificação de sessão
- Logout seguro
- Middleware de proteção

3.8. Csrf.php
- Geração de token
- Validação com hash_equals()
- Token por sessão

3.9. Validator.php
- Regras: required, email, min, max, numeric, date, in, unique
- Mensagens em português
- Devolve array de erros

3.10. Mailer.php
- Envio via mail() ou PHPMailer (se permitido)
- Templates de e-mail
- Fallback para log se SMTP não configurado

3.11. Logger.php
- Registo de erros em storage/logs/
- Níveis: info, warning, error

3.12. Config.php
- Leitura de .env
- Configuração centralizada

---

FASE 4 — SEGURANÇA (obrigatório)

Aplicar TODAS estas práticas:

4.1. SQL Injection
- SEMPRE prepared statements com PDO
- NUNCA concatenar variáveis em SQL
- Validação de tipos

4.2. XSS (Cross-Site Scripting)
- Escape automático em todas as views: htmlspecialchars($var, ENT_QUOTES, 'UTF-8')
- Content-Security-Policy header
- Nunca usar echo direto de input do utilizador

4.3. CSRF (Cross-Site Request Forgery)
- Token em todos os formulários POST
- Validação no servidor
- hash_equals() para comparação

4.4. Autenticação
- password_hash() com PASSWORD_DEFAULT
- password_verify() para login
- Regeneração de sessão após login
- Rate limiting de tentativas de login
- Bloqueio temporário após N tentativas

4.5. Sessões
- session_regenerate_id(true) após login
- Cookie: HttpOnly, Secure, SameSite=Strict
- Tempo de expiração
- Destruição completa no logout

4.6. Headers de segurança
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy
- Strict-Transport-Security (HSTS) em produção
- Content-Security-Policy

4.7. Uploads
- Validar tipo MIME real (finfo)
- Validar extensão
- Limitar tamanho
- Renomear ficheiros (nunca usar nome original)
- Guardar fora da raiz pública quando possível
- Bloquear execução de PHP na pasta de uploads

4.8. Formulários públicos
- Honeypot (campo escondido)
- Rate limiting por IP (throttle)
- Validação server-side
- Mensagens de erro genéricas (não revelar detalhes)

4.9. Erros
- Em produção: nunca mostrar stack trace
- Log de erros em ficheiro
- Página 404 e 500 personalizadas

4.10. Configuração
- .env fora do Git
- Credenciais nunca no código
- APP_DEBUG=false em produção

---

FASE 5 — URLs AMIGÁVEIS

5.1. .htaccess na raiz:
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]

5.2. public/.htaccess:
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]

5.3. Rotas amigáveis:
- /servicos → ServicosController
- /portfolio → PortfolioController
- /portfolio/{slug} → PortfolioController::show
- /networking → NetworkingController
- /contacto → ContactoController
- /solicitar-projeto → ProjetoController
- /agendar → AgendaController

5.4. Todas as URLs antigas (.html) → 301 para as novas

---

FASE 6 — MIGRAÇÃO DAS FUNCIONALIDADES

Reescrever, uma a uma:

6.1. Páginas públicas (17)
- Home, Sobre, Serviços, HAVRE Soluções, Portefólio, Projeto individual,
  Processo, FAQ, Contacto, Solicitar Projeto, Agendar, Networking,
  Política, Termos, Entrar, Registar, Recuperar palavra-passe

6.2. Formulários (3)
- Contacto → guarda na BD + e-mail
- Solicitar Projeto → guarda na BD + anexos + e-mail
- Agendar → guarda na BD + validação de horários + e-mail

6.3. Autenticação
- Registo, login, logout, recuperação de palavra-passe
- Área de cliente (/conta) com histórico

6.4. Painel de administração (CMS)
- CRUD de serviços
- CRUD de portefólio + galeria
- CRUD de soluções
- CRUD de pedidos, agendamentos, mensagens
- Definições (contactos, marca, agenda, networking)
- Utilizadores

6.5. SEO
- sitemap.xml
- robots.txt
- 301 via tabela redirects
- Metadados por página

---

FASE 7 — MIGRAÇÃO DOS DADOS

7.1. Reaproveitar a base de dados MySQL existente (23 tabelas)
7.2. Criar script de migração se necessário
7.3. Criar seeders em PHP puro (scripts SQL)
7.4. Verificar integridade após migração

---

FASE 8 — TESTES

8.1. Testes manuais de todas as páginas
8.2. Testes de formulários (validação, envio, e-mail)
8.3. Testes de autenticação (login, logout, permissões)
8.4. Testes de segurança (SQL injection, XSS, CSRF)
8.5. Testes de URLs amigáveis
8.6. Testes de responsividade
8.7. Testes de performance

---

FASE 9 — DOCUMENTAÇÃO

9.1. README.md com:
- Como instalar
- Como configurar
- Como correr
- Estrutura de pastas

9.2. Manual de utilização do CMS
9.3. Documentação de segurança
9.4. Guia de manutenção

---

REGRAS FINAIS:
- Não usar frameworks (nem Laravel, nem Symfony, nem CodeIgniter)
- Não usar Composer para dependências de framework
- Usar apenas PHP 8.2+ e MySQL
- Aplicar TODAS as boas práticas de segurança
- Código comentado em português
- Nomes de classes e métodos em português quando fizer sentido
- Seguir PSR-12 para estilo de código
- Testar cada fase antes de avançar
- Se algo for ambíguo, parar e perguntar

---

ENTREGA ESPERADA:

No final, devolver:
1. Estrutura de pastas criada
2. Ficheiros do Core implementados
3. Página de exemplo (/servicos) funcional
4. Formulário de exemplo (/contacto) funcional
5. Login funcional
6. Painel admin funcional (pelo menos 1 CRUD)
7. Documentação
8. Lista do que falta