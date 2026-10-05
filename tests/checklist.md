# Checklist manual — Fase 8 (testes)

Os testes automatizados cobrem páginas, formulários, auth e segurança
(`php tests/executar.php`). Os itens abaixo exigem revisão humana.

**Pré-condições:** servidor `php -S 127.0.0.1:8090 router.php` a correr; BD `havre_design`.

## 8.6 — Responsividade e visual (navegador)

- [ ] Home (`/`): sem sobreposição de texto; imagens proporcionais; menu adaptado a telemóvel (<768 px)
- [ ] Páginas internas (sobre, serviços, portefólio, contacto): grelha responsiva sem overflow horizontal
- [ ] Formulários: campos preenchíveis em ecrã pequeno; botões com alvo ≥ 44 px
- [ ] Painel admin: navegação acessível em ecrã médio; tabelas com scroll horizontal quando necessário
- [ ] `/conta` (área do cliente): pedidos e agendamentos legíveis em telemóvel
- [ ] Fontes/acentos corretos (UTF-8, sem `Ã©`/`�`)
- [ ] Favicon e logótipo carregam (sem 404 na consola)

## 8.7 — Performance

- [ ] Página inicial < 2 s em rede local (SQL sem N+1 — verificar com `MYSQL` geral log)
- [ ] Assets servidos com cache/etag ou cabeçalhos estáticos pelo servidor
- [ ] `robots.txt` / `sitemap.xml` rápidos (gerados e sem erro)
- [ ] Sem warnings/notices nos logs após navegar (`storage/logs/php-error.log`)

## Funcionalidades manuais

- [ ] Registo de novo utilizador → entra em `/conta` sem verificação de e-mail (decisão B)
- [ ] Recuperação de palavra-passe → token chega a `storage/logs/mail.log` (transporte `log`)
- [ ] «Lembrar-me» → sessão persiste 30 dias (fechar e reabrir navegador)
- [ ] Admin: criar serviço + imagem → aparece em `/servicos`
- [ ] Admin: apagar serviço → flash «Serviço desativado.» e some da página pública
- [ ] Admin: alterar estado de pedido → cliente vê estado em `/conta`
- [ ] Admin: marcar agendamento como concluído → e-mail de estado em `mail.log`
- [ ] Admin: editar definições (contactos) → reflectido no rodapé
- [ ] 301 legacy: abrir URL antiga do site Laravel → redirecciona (37 mapeamentos)
- [ ] Throttle: 11ª submissão de contacto seguida → bloqueada (10/15 min por IP)

## Segurança (regressão manual ocasional)

- [ ] PHP desativado em `public/storage` e `public/uploads` (colocar `.php` de teste via FTP → 403/404)
- [ ] `.env` fora de `public/` e não acessível via URL
- [ ] Sessões com cookie `HttpOnly; Secure; SameSite=Strict` (ver no devtools)

**Resultado:** ______ / ______ itens OK  ·  Data: ____ / ____ / ______
