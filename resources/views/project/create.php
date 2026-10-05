<?php
/**
 * Solicitar Projeto — formulário de pedido de proposta (+ vista de sucesso).
 * Convertida de backend/resources/views/project/create.blade.php
 */
$site     = \App\Support\Site::site();
$waNumber = $site['whatsapp.number'] ?? '';

// Sucesso do POST (flash do controller; fallback para chave simples da sessão)
$resumo = \App\Core\Session::lerFlash('project_submitted')
    ?? \App\Core\Session::obter('project_submitted');

// Houve reenvio do formulário? (velho() devolve '' quando não há old input —
// no Blade old('x') devolvia null, e é isso que os @selected de '' dependem)
$houveReenvio = is_array(\App\Core\Session::obter('_old_input', null, false));

// Utilizador autenticado → pré-preenchimento do formulário (@json no script)
$u            = utilizador();
$utilizadorJs = $u !== null ? ['name' => $u['name'] ?? '', 'email' => $u['email'] ?? ''] : null;

$titulo = 'Solicitar Projeto - HAVREDESIGN';
$meta   = 'Preencha o formulário e receba uma proposta personalizada para o seu projeto arquitetónico, de interiores ou consultoria em Luanda.';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>

  <!-- Hero Section -->
  <section class="relative py-16 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl leading-tight mb-4">
          Solicitar Projeto
        </h1>
        <p class="text-lg text-primary-foreground/80">
          Preencha o formulário abaixo e receberá uma proposta personalizada para seu projeto.
        </p>
      </div>
    </div>
  </section>

  <!-- Success View -->
  <section id="project-success-view" class="<?= $resumo ? '' : 'hidden' ?> py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="max-w-md mx-auto text-center space-y-6">
        <div class="w-20 h-20 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto">
          <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="font-serif text-2xl md:text-3xl text-foreground">Solicitação Enviada!</h2>
        <p class="text-muted-foreground">
          Recebemos sua solicitação de projeto. Nossa equipe irá analisar e entrar em contacto em até 48 horas úteis.
        </p>
        <div id="project-summary-box" class="bg-card p-6 rounded-lg border border-border text-left space-y-3 shadow-sm">
          <?php if ($resumo): ?>
            <p class="text-sm"><strong>Referência:</strong> <?= e($resumo['ref'] ?? '—') ?></p>
            <p class="text-sm"><strong>Tipo:</strong> <?= e($resumo['project_type'] ?? '—') ?></p>
            <p class="text-sm"><strong>Serviço:</strong> <?= e($resumo['service'] ?? 'Não especificado') ?></p>
            <p class="text-sm"><strong>Solução:</strong> <?= e($resumo['solution'] ?? 'Não especificado') ?></p>
            <p class="text-sm"><strong>Localização:</strong> <?= e($resumo['location'] ?? 'Não especificada') ?></p>
            <p class="text-sm"><strong>Orçamento:</strong> <?= e($resumo['budget'] ?? 'Não especificado') ?></p>
            <p class="text-sm"><strong>Prazo:</strong> <?= e($resumo['timeline'] ?? 'Não especificado') ?></p>
            <p class="text-sm"><strong>Cliente:</strong> <?= e($resumo['user_name'] ?? '') ?> (<?= e($resumo['user_email'] ?? '') ?>)</p>
          <?php endif; ?>
        </div>
        <div class="flex flex-col gap-3 pt-4">
          <a href="<?= e(url('/')) ?>" class="w-full py-3 bg-secondary text-secondary-foreground font-medium rounded-md text-center hover:bg-secondary/90">
            Voltar ao Início
          </a>
          <a href="/conta" class="w-full py-3 border border-border text-foreground font-medium rounded-md text-center hover:bg-muted">
            Ver Meus Projetos
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Form Section -->
  <section id="project-form-section" class="<?= $resumo ? 'hidden ' : '' ?>py-16">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-3 gap-12">
        <!-- Main Form -->
        <div class="lg:col-span-2">
<?= \App\Core\View::parcial('partials/flash') ?>
  <form id="project-request-form" method="POST" action="<?= e(rota('solicitar-projeto')) ?>" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_campo() ?>
            <!-- Honeypot: invisível a humanos, apanha bots (backend.md 6.4) -->
            <div class="hidden" aria-hidden="true">
              <label for="homepage">Homepage</label>
              <input type="text" id="homepage" name="homepage" tabindex="-1" autocomplete="off" />
            </div>
            <!-- Personal Info -->
            <div class="space-y-6">
              <h2 class="font-serif text-2xl text-foreground">Informações Pessoais</h2>
              <div class="grid sm:grid-cols-2 gap-6">
                <div class="form-group">
                  <label for="userName" class="form-label">Nome Completo *</label>
                  <input id="userName" name="user_name" type="text" required value="<?= e(velho('user_name')) ?>" placeholder="Seu nome" class="form-input" />
                  <?php if (erro_de('user_name')): ?><p class="form-error"><?= e(erro_de('user_name')) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                  <label for="userEmail" class="form-label">E-mail *</label>
                  <input id="userEmail" name="user_email" type="email" required value="<?= e(velho('user_email')) ?>" placeholder="seu@email.com" class="form-input" />
                  <?php if (erro_de('user_email')): ?><p class="form-error"><?= e(erro_de('user_email')) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                  <label for="userPhone" class="form-label">Telefone *</label>
                  <input id="userPhone" name="user_phone" type="text" required value="<?= e(velho('user_phone')) ?>" placeholder="+244 926 184 104" class="form-input" />
                  <?php if (erro_de('user_phone')): ?><p class="form-error"><?= e(erro_de('user_phone')) ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                  <label for="address" class="form-label">Localização do Projeto</label>
                  <input id="address" name="location" type="text" value="<?= e(velho('location')) ?>" placeholder="Província, Município, Bairro" class="form-input" />
                  <?php if (erro_de('location')): ?><p class="form-error"><?= e(erro_de('location')) ?></p><?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Project Info -->
            <div class="space-y-6">
              <h2 class="font-serif text-2xl text-foreground">Detalhes do Projeto</h2>
              <div class="grid sm:grid-cols-2 gap-6">
                <div class="form-group">
                  <label for="projectType" class="form-label">Tipo de Projeto *</label>
                  <select id="projectType" name="project_type" required class="form-select">
                    <option value="">Selecione o tipo</option>
                    <option value="Projeto Arquitetônico" <?= velho('project_type') === 'Projeto Arquitetônico' ? 'selected' : '' ?>>Projeto Arquitetônico</option>
                    <option value="Visualização & Consultoria" <?= velho('project_type') === 'Visualização & Consultoria' ? 'selected' : '' ?>>Visualização & Consultoria</option>
                    <option value="Interiores & Design" <?= velho('project_type') === 'Interiores & Design' ? 'selected' : '' ?>>Interiores & Design</option>
                    <option value="Outro" <?= velho('project_type') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                  </select>
                  <?php if (erro_de('project_type')): ?><p class="form-error"><?= e(erro_de('project_type')) ?></p><?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="area" class="form-label">Área Aproximada (m²)</label>
                  <input id="area" name="area_approx" type="text" inputmode="decimal" value="<?= e(velho('area_approx')) ?>" placeholder="Ex.: 120" class="form-input" />
                  <?php if (erro_de('area_approx')): ?><p class="form-error"><?= e(erro_de('area_approx')) ?></p><?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="projectStage" class="form-label">Estado do Projeto</label>
                  <select id="projectStage" name="project_stage" class="form-select">
                    <option value="">Selecione o estado</option>
                    <option value="Em definição" <?= velho('project_stage') === 'Em definição' ? 'selected' : '' ?>>Em definição</option>
                    <option value="Estudo preliminar em curso" <?= velho('project_stage') === 'Estudo preliminar em curso' ? 'selected' : '' ?>>Estudo preliminar em curso</option>
                    <option value="Projeto desenvolvido" <?= velho('project_stage') === 'Projeto desenvolvido' ? 'selected' : '' ?>>Projeto desenvolvido</option>
                    <option value="Obra em curso" <?= velho('project_stage') === 'Obra em curso' ? 'selected' : '' ?>>Obra em curso</option>
                    <option value="Outro" <?= velho('project_stage') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                  </select>
                  <?php if (erro_de('project_stage')): ?><p class="form-error"><?= e(erro_de('project_stage')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2">
                  <label for="budget" class="block text-sm font-medium text-foreground">Orçamento Previsto</label>
                  <select id="budget" name="budget" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione a faixa</option>
                    <option value="Até 50.000 Kz" <?= velho('budget') === 'Até 50.000 Kz' ? 'selected' : '' ?>>Até 50.000 Kz</option>
                    <option value="50.000 - 150.000 Kz" <?= velho('budget') === '50.000 - 150.000 Kz' ? 'selected' : '' ?>>50.000 - 150.000 Kz</option>
                    <option value="150.000 - 300.000 Kz" <?= velho('budget') === '150.000 - 300.000 Kz' ? 'selected' : '' ?>>150.000 - 300.000 Kz</option>
                    <option value="300.000 - 500.000 Kz" <?= velho('budget') === '300.000 - 500.000 Kz' ? 'selected' : '' ?>>300.000 - 500.000 Kz</option>
                    <option value="Acima de 500.000 Kz" <?= velho('budget') === 'Acima de 500.000 Kz' ? 'selected' : '' ?>>Acima de 500.000 Kz</option>
                    <option value="A definir" <?= velho('budget') === 'A definir' ? 'selected' : '' ?>>A definir</option>
                  </select>
                  <?php if (erro_de('budget')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('budget')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2 sm:col-span-2">
                  <label for="timeline" class="block text-sm font-medium text-foreground">Prazo Desejado</label>
                  <select id="timeline" name="timeline" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione o prazo</option>
                    <option value="Urgente (menos de 1 mês)" <?= velho('timeline') === 'Urgente (menos de 1 mês)' ? 'selected' : '' ?>>Urgente (menos de 1 mês)</option>
                    <option value="1 a 3 meses" <?= velho('timeline') === '1 a 3 meses' ? 'selected' : '' ?>>1 a 3 meses</option>
                    <option value="3 a 6 meses" <?= velho('timeline') === '3 a 6 meses' ? 'selected' : '' ?>>3 a 6 meses</option>
                    <option value="6 meses a 1 ano" <?= velho('timeline') === '6 meses a 1 ano' ? 'selected' : '' ?>>6 meses a 1 ano</option>
                    <option value="Sem prazo definido" <?= velho('timeline') === 'Sem prazo definido' ? 'selected' : '' ?>>Sem prazo definido</option>
                  </select>
                  <?php if (erro_de('timeline')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('timeline')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2 sm:col-span-2">
                  <label for="serviceRequested" class="block text-sm font-medium text-foreground">Serviço Pretendido</label>
                  <p class="text-xs text-muted-foreground -mt-1">Escolha um serviço ou uma HAVRE Solução. Se ainda não souber, escolha «Não sei — preciso de orientação».</p>
                  <?php $servicos = \App\Core\Database::todos('SELECT id, title FROM services WHERE active = 1 ORDER BY sort_order'); ?>
                  <select id="serviceRequested" name="service_id" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione um serviço</option>
                    <?php foreach ($servicos as $s): ?>
                      <option value="<?= e($s['id']) ?>" <?= velho('service_id') === $s['id'] ? 'selected' : '' ?>><?= e($s['title']) ?></option>
                    <?php endforeach; ?>
                    <option value="" <?= ($houveReenvio && velho('service_id') === '') ? 'selected' : '' ?>>Não sei — preciso de orientação</option>
                  </select>
                  <?php if (erro_de('service_id')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('service_id')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2 sm:col-span-2">
                  <label for="solutionRequested" class="block text-sm font-medium text-foreground">Ou HAVRE Solução Pretendida</label>
                  <?php
                    $subtitulos = [
                      'GENESIS' => 'Arquitetura de Raiz',
                      'EVOLUTION' => 'Transformação de Espaços',
                      'READY' => 'Regularização e Licenciamento',
                      'GUARDIAN' => 'Fiscalização e Acompanhamento Técnico',
                      'PRIME' => 'Solução Exclusiva',
                    ];
                    $solucoes = \App\Core\Database::todos('SELECT id, name, code FROM solutions WHERE active = 1 ORDER BY sort_order');
                  ?>
                  <select id="solutionRequested" name="solution_id" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione uma solução</option>
                    <?php foreach ($solucoes as $sol): ?>
                      <option value="<?= e($sol['id']) ?>" <?= velho('solution_id') === $sol['id'] ? 'selected' : '' ?>><?= e($sol['name']) ?> — <?= e($subtitulos[$sol['code']] ?? $sol['code']) ?></option>
                    <?php endforeach; ?>
                    <option value="" <?= ($houveReenvio && velho('solution_id') === '') ? 'selected' : '' ?>>Não sei — preciso de orientação</option>
                  </select>
                  <?php if (erro_de('solution_id')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('solution_id')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2">
                  <label for="segment" class="block text-sm font-medium text-foreground">Segmento</label>
                  <select id="segment" name="segment" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione o segmento</option>
                    <option value="Investimento imobiliário residencial" <?= velho('segment') === 'Investimento imobiliário residencial' ? 'selected' : '' ?>>Investimento imobiliário residencial</option>
                    <option value="Habitação própria" <?= velho('segment') === 'Habitação própria' ? 'selected' : '' ?>>Habitação própria</option>
                    <option value="Comércio e serviços" <?= velho('segment') === 'Comércio e serviços' ? 'selected' : '' ?>>Comércio e serviços</option>
                    <option value="Outro" <?= velho('segment') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                  </select>
                  <?php if (erro_de('segment')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('segment')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2">
                  <label for="replyChannel" class="block text-sm font-medium text-foreground">Canal Preferido para Resposta</label>
                  <select id="replyChannel" name="preferred_channel" required class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione o canal</option>
                    <option value="email" <?= velho('preferred_channel') === 'email' ? 'selected' : '' ?>>E-mail</option>
                    <option value="phone" <?= velho('preferred_channel') === 'phone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="whatsapp" <?= velho('preferred_channel') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                  </select>
                  <?php if (erro_de('preferred_channel')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('preferred_channel')) ?></p><?php endif; ?>
                </div>

                <div class="space-y-2">
                  <label for="replyTime" class="block text-sm font-medium text-foreground">Horário Preferido</label>
                  <select id="replyTime" name="preferred_time" class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
                    <option value="">Selecione o horário</option>
                    <option value="Manhã" <?= velho('preferred_time') === 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                    <option value="Tarde" <?= velho('preferred_time') === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                    <option value="Fim do dia" <?= velho('preferred_time') === 'Fim do dia' ? 'selected' : '' ?>>Fim do dia</option>
                    <option value="Indiferente" <?= velho('preferred_time') === 'Indiferente' ? 'selected' : '' ?>>Indiferente</option>
                  </select>
                  <?php if (erro_de('preferred_time')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('preferred_time')) ?></p><?php endif; ?>
                </div>
              </div>

              <div class="space-y-2">
                <label for="description" class="block text-sm font-medium text-foreground">Descrição do Projeto *</label>
                <textarea
                  id="description"
                  name="description"
                  required
                  rows="6"
                  placeholder="Descreva seu projeto com detalhes: tipo de ambiente, metragem aproximada, necessidades específicas, preferências de estilo, etc."
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary"
                ><?= e(velho('description')) ?></textarea>
                <?php if (erro_de('description')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('description')) ?></p><?php endif; ?>
              </div>
            </div>

            <!-- Attachments -->
            <div class="space-y-4">
              <div>
                <h2 class="font-serif text-2xl text-foreground">Anexos</h2>
                <p class="text-sm text-muted-foreground mt-1">
                  Planta, croqui, fotografias e documento do terreno. Formatos aceites: PDF, JPG, PNG ou DWG, até 10 MB por ficheiro.
                </p>
              </div>
              <div id="projectDropzone" class="rounded-md border-2 border-dashed border-border bg-muted px-5 py-8 text-center transition-colors focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/30" data-drag="off">
                <input
                  id="projectFiles"
                  name="projectFiles[]"
                  type="file"
                  multiple
                  accept=".pdf,.jpg,.jpeg,.png,.webp,.zip,.dwg"
                  class="sr-only"
                  aria-describedby="projectFilesHint"
                />
                <label for="projectFiles" class="inline-flex items-center gap-2 px-5 py-2.5 bg-secondary text-secondary-foreground rounded-md text-sm font-medium cursor-pointer hover:bg-secondary/90 transition-colors select-none">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                  Escolher ficheiros
                </label>
                <p id="projectFilesHint" class="mt-3 text-sm text-muted-foreground">
                  ou <span class="font-medium text-foreground">arraste e solte</span> os ficheiros aqui
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                  Até <strong class="font-medium text-foreground">5 ficheiros</strong> · 10 MB cada · PDF, JPG, PNG, WEBP, ZIP ou DWG
                </p>

                <ul id="projectFileList" class="mt-5 space-y-2 text-left" hidden></ul>
                <p id="projectFileAviso" class="mt-3 text-xs text-destructive text-left" hidden></p>

                <?php if (erro_de('projectFiles')): ?><p class="text-xs text-destructive mt-3 text-left"><?= e(erro_de('projectFiles')) ?></p><?php endif; ?>
                <?php
                  // @error('projectFiles.*') — erro de um ficheiro específico (chave 'projectFiles.0', …)
                  $erroFicheiroIndice = null;
                  $errosValidacao = \App\Core\Session::obter('_erros_validacao', null, false);
                  if (is_array($errosValidacao)) {
                      foreach ($errosValidacao as $chaveErro => $mensagemErro) {
                          if (str_starts_with((string) $chaveErro, 'projectFiles.')) {
                              $erroFicheiroIndice = (string) $mensagemErro;
                              break;
                          }
                      }
                  }
                ?>
                <?php if ($erroFicheiroIndice !== null): ?><p class="text-xs text-destructive mt-3 text-left"><?= e($erroFicheiroIndice) ?></p><?php endif; ?>
              </div>
            </div>

            <!-- Consent -->
            <div class="space-y-4">
              <label class="flex items-start gap-3 text-sm text-muted-foreground">
                <input id="privacyConsent" name="privacy" type="checkbox" value="1" required aria-label="Autorizo o tratamento dos meus dados pessoais" class="mt-1 h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
                <span>
                  Autorizo o tratamento dos meus dados pessoais de acordo com a
                  <a href="<?= e(rota('privacidade')) ?>" class="underline text-foreground hover:text-secondary">Política de Privacidade</a>
                  e concordo em ser contactado sobre este pedido. *
                </span>
              </label>
              <?php if (erro_de('privacy')): ?><p class="text-xs text-destructive mt-1"><?= e(erro_de('privacy')) ?></p><?php endif; ?>
            </div>

            <button
              type="submit"
              class="inline-flex items-center justify-center px-8 py-3.5 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors"
            >
              Enviar Solicitação
              <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-muted p-6 rounded-lg border border-border space-y-4">
            <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-serif text-lg text-foreground">Como Funciona</h3>
            <ol class="space-y-3 text-sm text-muted-foreground">
              <li class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xs shrink-0 font-bold">1</span>
                Preencha o formulário com os detalhes do seu projeto
              </li>
              <li class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xs shrink-0 font-bold">2</span>
                Nossa equipe analisa sua solicitação
              </li>
              <li class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xs shrink-0 font-bold">3</span>
                Entramos em contacto para agendar reunião
              </li>
              <li class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xs shrink-0 font-bold">4</span>
                Apresentamos proposta personalizada
              </li>
            </ol>
          </div>

          <div class="bg-primary text-primary-foreground p-6 rounded-lg space-y-4">
            <h3 class="font-serif text-lg">Prefere Conversar?</h3>
            <p class="text-sm text-primary-foreground/80">
              Se tiver dúvidas ou preferir explicar seu projeto por telefone, entre em contacto conosco.
            </p>
            <div class="space-y-2 pt-2">
              <a href="<?= e(rota('agendar')) ?>" class="block w-full text-center py-2.5 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors text-sm">
                Agendar conversa
              </a>
              <a href="https://wa.me/<?= e($waNumber) ?>" target="_blank" rel="noopener noreferrer" class="block w-full text-center py-2.5 border border-primary-foreground/30 text-white font-medium rounded-md hover:bg-white/10 transition-colors text-sm">
                WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Pré-preenche com o cliente autenticado (sessão do Laravel — B9/B11).
    const utilizador = <?= json_encode($utilizadorJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    if (utilizador) {
      const nome = document.getElementById('userName');
      const email = document.getElementById('userEmail');
      if (nome && !nome.value) nome.value = utilizador.name || '';
      if (email && !email.value) email.value = utilizador.email || '';
    }

    // ---- Anexos: dropzone com lista de ficheiros escolhidos ----
    const zona = document.getElementById('projectDropzone');
    const entrada = document.getElementById('projectFiles');
    const lista = document.getElementById('projectFileList');
    const aviso = document.getElementById('projectFileAviso');
    if (!zona || !entrada || !lista) return;

    const MAX_FICHEIROS = 5;
    const MAX_BYTES = 10 * 1024 * 1024;
    const EXTENSOES = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'dwg'];

    const escapar = (texto) => String(texto).replace(/[&<>"']/g, (c) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const extensao = (nome) => (nome.split('.').pop() || '').toLowerCase();

    const tamanho = (bytes) => (bytes >= 1048576
      ? (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB'
      : Math.max(1, Math.round(bytes / 1024)) + ' KB');

    const aceitavel = (f) => EXTENSOES.includes(extensao(f.name)) && f.size > 0 && f.size <= MAX_BYTES;

    function mostrarAviso(textos) {
      const mensagem = textos.filter(Boolean).join(' ');
      if (!aviso) return;
      aviso.textContent = mensagem;
      aviso.hidden = !mensagem;
    }

    function definir(ficheiros) {
      const dados = new DataTransfer();
      ficheiros.forEach((f) => dados.items.add(f));
      entrada.files = dados.files;
      pintar();
    }

    function aplicar(novos, substituir) {
      const atuais = substituir ? [] : Array.from(entrada.files || []);
      const validos = novos.filter(aceitavel);
      const recusados = novos.length - validos.length;
      const combinados = [...atuais, ...validos];
      const excedentes = Math.max(0, combinados.length - MAX_FICHEIROS);
      const avisos = [];
      if (recusados > 0) {
        avisos.push(recusados === 1
          ? '1 ficheiro ignorado: formato não aceite ou superior a 10 MB.'
          : recusados + ' ficheiros ignorados: formato não aceite ou superior a 10 MB.');
      }
      if (excedentes > 0) {
        avisos.push('Máximo de ' + MAX_FICHEIROS + ' anexos — ' + excedentes + ' ignorado(s).');
      }
      definir(combinados.slice(0, MAX_FICHEIROS));
      mostrarAviso(avisos);
    }

    function pintar() {
      const ficheiros = Array.from(entrada.files || []);
      lista.innerHTML = '';
      lista.hidden = ficheiros.length === 0;

      ficheiros.forEach((f, i) => {
        const item = document.createElement('li');
        item.className = 'flex items-center gap-3 bg-card border border-border rounded-md px-3 py-2';
        item.innerHTML =
          '<span class="shrink-0 text-secondary" aria-hidden="true">' +
          '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 5.172a4 4 0 015.656 5.656l-7.5 7.5a6 6 0 01-8.486-8.486l7.5-7.5a4 4 0 015.656 5.656L10.5 16.5"/></svg>' +
          '</span>' +
          '<span class="min-w-0 flex-1">' +
          '<span class="block truncate text-sm text-foreground">' + escapar(f.name) + '</span>' +
          '<span class="block text-xs text-muted-foreground">' + tamanho(f.size) + '</span>' +
          '</span>' +
          '<button type="button" data-remover="' + i + '" aria-label="Remover ' + escapar(f.name) + '" ' +
          'class="shrink-0 rounded p-1.5 text-muted-foreground hover:text-destructive hover:bg-muted transition-colors">' +
          '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
          '</button>';
        lista.appendChild(item);
      });
    }

    entrada.addEventListener('change', () => aplicar(Array.from(entrada.files || []), true));

    lista.addEventListener('click', (evento) => {
      const botao = evento.target.closest('button[data-remover]');
      if (!botao) return;
      const indice = Number(botao.dataset.remover);
      definir(Array.from(entrada.files || []).filter((_, i) => i !== indice));
      mostrarAviso([]);
    });

    ['dragenter', 'dragover'].forEach((nome) => {
      zona.addEventListener(nome, (evento) => {
        evento.preventDefault();
        zona.classList.add('border-secondary', 'bg-secondary/10');
      });
    });

    ['dragleave', 'drop'].forEach((nome) => {
      zona.addEventListener(nome, (evento) => {
        evento.preventDefault();
        zona.classList.remove('border-secondary', 'bg-secondary/10');
      });
    });

    zona.addEventListener('drop', (evento) => {
      const soltos = Array.from((evento.dataTransfer && evento.dataTransfer.files) || []);
      if (soltos.length) aplicar(soltos, false);
    });

    // Clicar na zona (fora do botão e da lista) abre o seletor de ficheiros.
    zona.addEventListener('click', (evento) => {
      if (evento.target.closest('label, li, button')) return;
      entrada.click();
    });

    pintar();
  });
</script>
<?php \App\Core\View::secaoFim(); ?>
