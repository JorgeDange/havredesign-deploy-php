CONTEXTO:
Website HAVREDESIGN em PHP puro (MVC) — funcional.
Os 5 projetos reais JÁ ESTÃO na BD.

DOCUMENTOS DE REFERÊNCIA:
1. Dossier Técnico (briefing)
2. Mapa de Conteúdos (especificação página a página) ← REFERÊNCIA
3. Dossiers de projetos (5 projetos reais)

OBJETIVO:
1. Remover «Orçamento» do menu e substituir por «HAVRE Soluções»
2. Remover «Caso Real — Casa Vila Nova» da página Processo
3. Eliminar todos os projetos demonstrativos da BD
4. Garantir que o público só vê os 5 projetos reais
5. Atualizar o Processo para 6 etapas
6. Confirmar serviços conforme Mapa de Conteúdos
7. Atualizar textos conforme Mapa de Conteúdos
8. Preparar pré-visualização

---

FASE 1 — MENU: REMOVER «ORÇAMENTO» E SUBSTITUIR POR «HAVRE SOLUÇÕES»

1.1. No menu principal (header), localizar o item «Orçamento»

1.2. Substituir por «HAVRE Soluções»

1.3. O item deve apontar para a rota de HAVRE Soluções
     (atualmente /orcamentos — ver Fase 2)

1.4. O menu final deve ser:
     Início | Sobre | Serviços | HAVRE Soluções | Portefólio |
     Processo | FAQ | Contacto | Entrar | Iniciar Projeto

1.5. O botão «Iniciar Projeto» mantém-se como CTA principal

1.6. Verificar em TODAS as views:
     - Header (partial)
     - Footer (partial)
     - Qualquer menu mobile
     - Qualquer breadcrumb

1.7. Procurar por:
     - «Orçamento»
     - «Orçamentos»
     - «orcamento»
     - «orcamentos»
     - «Explorar pacote»
     - «Ver orçamento»
     - «Solicitar orçamento»

1.8. Substituir por:
     - «HAVRE Soluções»
     - «Conhecer HAVRE Soluções»
     - «Solicitar proposta personalizada»

---

FASE 2 — URL: /orcamentos → /havre-solucoes

2.1. Verificar a rota atual de HAVRE Soluções
     (provavelmente /orcamentos)

2.2. Criar nova rota /havre-solucoes

2.3. Criar 301 de /orcamentos para /havre-solucoes
     (via tabela redirects ou .htaccess)

2.4. Atualizar todos os links internos:
     - Header
     - Footer
     - Home
     - Serviços
     - Networking
     - Qualquer outro

2.5. Atualizar o sitemap.xml

2.6. Garantir que /havre-solucoes responde 200

2.7. Garantir que /orcamentos responde 301 → /havre-solucoes

---

FASE 3 — REMOVER «CASO REAL — CASA VILA NOVA»

3.1. Procurar em todas as views:
     - «Casa Vila Nova»
     - «Caso Real»
     - «380 m²»
     - Imagens associadas

3.2. Procurar na BD (settings, portfolio_items)

3.3. Remover tudo (não ocultar)

3.4. Garantir 404 ou 301 na URL antiga

---

FASE 4 — AUDITORIA DA BD

4.1. Listar TODOS os portfolio_items:
     - Identificar os 5 reais (manter)
     - Identificar os demonstrativos (eliminar):
       * Casa Vila Nova
       * Residência Moderna
       * Escritório Corporativo
       * Apartamento Luxo
       * Clínica Médica genérica
       * Villa Sol Nascente
       * Showroom Havre
       * Quaisquer outros

4.2. Devolver tabela: id, título, slug, status → ação (manter/eliminar/arquivar)

---

FASE 5 — ELIMINAR DEMONSTRATIVOS

5.1. Para cada demonstrativo:
     - Eliminar da BD (ou status=ARCHIVED sem URL)
     - Eliminar imagens associadas
     - Eliminar referências em redirects
     - Garantir 0 ocorrências

---

FASE 6 — VERIFICAR QUE O PÚBLICO SÓ VÊ OS 5

6.1. Verificar /portfolio → só 5 cartões
6.2. Verificar cada /portfolio/{slug} → só os 5 reais
6.3. Verificar Home → destaques só dos 5
6.4. Verificar pesquisa interna (se houver)

---

FASE 7 — ATUALIZAR PROCESSO PARA 6 ETAPAS

7.1. Conforme o Mapa de Conteúdos:
     1. Briefing
     2. Conceção
     3. Desenvolvimento
     4. Apresentação e ajustes
     5. Acompanhamento
     6. Entrega

7.2. Atualizar:
     - A view /processo
     - O preview na Home
     - A resposta da FAQ

---

FASE 8 — SERVIÇOS (conforme Mapa de Conteúdos)

8.1. Confirmar que os serviços ativos são:
     1. Projeto Arquitetónico
     2. Design de Interiores
     3. Fiscalização e Acompanhamento
     4. Consultoria Técnica
     5. Topografia e Levantamento Técnico
     6. Modelação 3D e Renderização
     7. Medições e Orçamento
     8. Licenciamento
     9. Croqui de Localização (se confirmado)

8.2. Remover: Mobiliário, Construção, Urbanismo,
     Estudo de Viabilidade, Gestão de Obra

8.3. Atualizar descrições e CTAs conforme Mapa de Conteúdos

---

FASE 9 — TEXTOS (conforme Mapa de Conteúdos)

9.1. Atualizar:
     - Home: hero, sobre resumido, serviços, soluções, processo, portefólio, fecho
     - Sobre: propósito, empresa, fundadora, missão, visão, valores, fecho
     - Serviços: 9 com resumos do Mapa
     - Portefólio: introdução, filtros, cartões
     - Processo: 6 etapas
     - FAQ: 11 perguntas
     - Networking: blocos
     - Contacto: título, texto, formulário, contacto

---

FASE 10 — IMAGENS GENÉRICAS

10.1. Eliminar todas as que não são dos dossiers
10.2. Manter apenas institucionais (se aprovadas)

---

FASE 11 — PRÉ-VISUALIZAÇÃO

11.1. Preparar ambiente
11.2. Listar páginas públicas
11.3. Enviar ao cliente

---

FASE 12 — RELATÓRIO

12.1. Devolver:
      - Projetos eliminados
      - Projetos mantidos (5 reais)
      - Menu atualizado (Orçamento → HAVRE Soluções)
      - URL atualizada (/orcamentos → /havre-solucoes)
      - Textos atualizados
      - Serviços atualizados
      - Confirmação de 0 demonstrativos
      - Link de pré-visualização

---

REGRAS FINAIS:
- Eliminar (não ocultar)
- Manter só os 5 projetos reais
- Menu: «HAVRE Soluções» substitui «Orçamento»
- URL: /havre-solucoes (com 301 de /orcamentos)
- Não inventar conteúdo
- Seguir o Mapa de Conteúdos
- Se algo for ambíguo, parar e perguntar