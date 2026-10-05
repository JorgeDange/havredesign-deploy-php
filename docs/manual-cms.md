# Manual de utilização — Painel administrativo (CMS)

**Aceder:** `https://dominio/admin` (ou `ADMIN_PATH` definido no `.env`).
**Entrar:** `/entrar` com o e-mail e palavra-passe de administrador.

> O painel é apenas para papéis `ADMIN`. Utilizadores `USER` entram em `/conta`
> (área do cliente: os seus pedidos e agendamentos).

---

## 1. Dashboard (`/admin`)

Quadro com contagens e atalhos: pedidos novos, agendamentos, mensagens,
estados dos conteúdos.

## 2. Serviços

- **Lista:** estado (ativo/inativo), ordem, imagem.
- **Novo:** `+ Novo serviço` → nome, slug (gerado do nome se vazio), descrição,
  imagem (opcional), estado.
- **Editar:** clique em «Editar» no item.
- **Ativar/Desativar:** botão na lista — o serviço some/entra em `/servicos`.
- **Apagar:** confirma; o serviço deixa de aparecer no site.

Imagens: JPEG/PNG/WebP, até 5 MB (a app valida tipo real e tamanho).
Os ficheiros ficam em `public/storage/`.

## 3. Portefólio

- **Itens:** título, slug, categoria, descrição, imagem de capa, estado.
- **Galeria:** em cada item, «Galeria» → adicionar/remover imagens.
- **Detalhe público:** `/portfolio/{slug}`.

## 4. HAVRE Soluções (`/admin/solucoes`)

CRUD igual aos serviços (título, descrição, imagem) — aparecem em `/orcamentos`.

## 5. Pedidos de projeto (`/admin/pedidos`)

- Lista com estado: `new`, `in_review`, `approved`, `rejected` (+ e-mail ao cliente).
- **Abrir pedido:** dados do cliente, descrição, anexos.
  - Anexos ficam em disco **privado** — ver/baixar só pelo painel
    (`/admin/pedidos/{id}/anexos/{anexo}`), nunca por URL pública.
- **Alterar estado** no formulário → guarda e envia e-mail ao cliente.

## 6. Agendamentos (`/admin/agendamentos`)

- Lista por data/estado; abrir para ver detalhe.
- Estados: `pending` → `confirmed`/`cancelled`/`completed`.
- Alterar estado → e-mail de notificação ao cliente.
- Slots já ocupados ficam indisponíveis no formulário público automaticamente.

## 7. Mensagens (`/admin/mensagens`)

Mensagens do `/contacto`. Abrir para ler; marcar como lida; apagar quando não
forem necessárias.

## 8. Testemunhos (`/admin/testemunhos`)

Aprovação de testemunhos:
- **Ativar/Desativar** — só os ativos aparecem no site.
- **Editar** — nome, cargo, texto, fotografia.
- **Apagar** — definitivo.

## 9. Definições (`/admin/definicoes`)

Definições gerais guardadas na tabela `settings` (contactos, morada, redes
sociais, textos do rodapé). Editar e «Guardar» — reflexo imediato no site.

## 10. Utilizadores (`/admin/utilizadores`)

- Lista de contas com papel (`USER`/`ADMIN`).
- **Alterar papel** com o botão próprio (requer sessão de admin).
- Registo público em `/registar` cria contas `USER`; para um admin novo,
  criar o utilizador na BD ou alterar o papel aqui.

---

## Boas práticas

- Antes de publicar imagens: comprimir (ex.: TinyPNG) — o site serve o original.
- Slug: usar chaves curtas e sem acentos (ex.: `casa-talatona`).
- Depois de publicar, verificar `/servicos`, `/portfolio` e o e-mail
  (`storage/logs/mail.log` em desenvolvimento).
- Sair do painel com **Sair** em sessões partilhadas.
