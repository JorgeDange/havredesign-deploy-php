# Guia de manutenção — HAVREDESIGN

Rotina operacional para manutenção do site em produção.

## 1. Diário / semanal

- [ ] Dashboard `/admin`: pedidos novos e agendamentos pendentes.
- [ ] E-mails: confirmar chegada (em produção, `MAIL_TRANSPORT` deve enviar
      de verdade — ver `docs/…` do `.env`; em `log` ficam em `storage/logs/mail.log`).
- [ ] `storage/logs/php-error.log` — procurar `WARNING`/`ERROR` novas.
- [ ] Banda/estado do site (uptime).

## 2. Backups

**Base de dados** (diário, reter 30 dias):

```bash
mysqldump -u root -p --single-transaction havre_design > backups/havre_$(date +%F).sql
```

**Ficheiros** (semanal): `storage/`, `public/storage/`, `public/uploads/`, `.env`.

Guardados fora do servidor (e pelo menos uma cópia fora do país/oficina).
**Restauro:** `mysql -u root -p havre_design < backup.sql` + copiar `storage/`.

## 3. Actualizações de conteúdo (CMS)

Ver `docs/manual-cms.md`. Pontos críticos:

- Publicar imagem nova → comprimir antes (o site serve o original).
- Alterar definições → guardar e verificar rodapé/contactos.
- Nunca apagar anexos de pedidos sem backup (discos privados).

## 4. Após alterações de código

```bash
# 1. Lint de todos os PHP (sem framework, não há "composer run")
Get-ChildItem -Recurse -Filter *.php app,routes,public,resources,tests |
    ForEach-Object { php -l $_.FullName }

# 2. Suite de testes
php -S 127.0.0.1:8090 router.php   # numa consola
php tests/executar.php             # noutra → 99/99

# 3. Checklist manual (visual/responsividade) quando houver mudanças de UI
#    → tests/checklist.md
```

## 5. Troubleshooting rápido

| Sintoma | Verificar |
|---|---|
| 500 em toda a app | `storage/logs/php-error.log`; `.env` (DB_*); permissões de `storage/` |
| Erro de ligação BD | `DB_HOST/DB_USER/DB_PASS`; MySQL a correr |
| CSRF 419 em formulários | sessão perdida/cookie bloqueado (`SESSION_SECURE_COOKIE` vs HTTPS) |
| Login recusado | `LOGIN_MAX_TENTATIVAS` — limpar throttle em `storage/app/private/throttle/` |
| Imagem não aparece | caminho na BD (`storage/…`) vs ficheiro em `public/storage/…` |
| E-mail não sai | `MAIL_TRANSPORT`; ver `storage/logs/mail.log` |
| Links antigos a 404 | tabela `redirects` (`active=1`); ver middleware `RedirectLegacyUrls` |
| 403/404 em URLs novas | `.htaccess` (Apache com `AllowOverride All`) ou rotas em `routes/web.php` |

## 6. Logs

- `storage/logs/php-error.log` — erros PHP/500
- `storage/logs/app.log` — eventos da aplicação (envio de e-mails, etc.)
- `storage/logs/mail.log` — e-mails gerados com `MAIL_TRANSPORT=log`

Rodar: comprimir/mover mensalmente; nunca deixar crescer sem limite.

## 7. Segurança periódica

- [ ] Mensal: revistar papéis em `/admin/utilizadores`.
- [ ] Mensal: correr `php tests/executar.php`.
- [ ] Trimestral: actualizar PHP do servidor (8.2.x) e revisar `.env`.
- [ ] Sempre: verificar que `.env` **não** é acessível por URL.

## 8. Anos/bombeiros

1. **BD corrompida/restaurar:** parar escritas → restaurar dump → correr testes.
2. **Suspeita de compromisso:** mudar todas as palavras-passe (admin + BD),
   revogar sessões (apagar `storage/sessions` ou equivalente), rever logs,
   restaurar código de cópia limpa.
3. **Domínio/HTTPS novo:** actualizar `APP_URL` no `.env` (afecta sitemap) e
   `SESSION_SECURE_COOKIE=true`.
