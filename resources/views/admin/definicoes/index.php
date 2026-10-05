<?php
/**
 * Painel — definições do site (contacto, marca, redes, WhatsApp, agenda…).
 * Espera do controller: $valores (chave=>texto), $agenda (array decodificado),
 * $qrCaminho, $qrExiste.
 *
 * Os name dos campos são as PRÓPRIAS chaves de settings ('contact.email',
 * 'whatsapp.number', …) — o controller lê e grava directamente por aí.
 */
$titulo         = 'Definições - Administração HAVREDESIGN';
$tituloAdmin    = 'Definições';
$subtituloAdmin = 'Contacto, marca, redes sociais, WhatsApp e agenda — as alterações aparecem no site de imediato.';

$rotaAcao = rota('admin.definicoes');

// Estado antigo (após falha de validação) — senão, valores guardados
$antigo = \App\Core\Session::obter('_old_input', null, false);
$antigo = is_array($antigo) ? $antigo : [];

$valor = static function (string $campo, string $padrao = '') use ($antigo): string {
    return isset($antigo[$campo]) && is_scalar($antigo[$campo]) ? (string) $antigo[$campo] : $padrao;
};

$atual = static function (string $campo, string $padrao = '') use ($valores, $valor): string {
    return $valor($campo, (string) ($valores[$campo] ?? $padrao));
};

$inp = static function (string $campo): string {
    return 'w-full px-4 py-2.5 rounded-md border ' . (erro_de($campo) !== null ? 'border-destructive' : 'border-border')
        . ' bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary';
};

$erroDe = static function (string $campo): ?string {
    return erro_de($campo);
};

// ------------------------------------------------------------------
// Agenda
// ------------------------------------------------------------------
$rotulosTipos = ['SITE' => 'Visita ao Local', 'ONLINE' => 'Reunião Online'];
$diasNomes    = [0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];

$diasVelhos = $antigo['agenda.dias_indisponiveis'] ?? null;
$diasAtuais = is_array($diasVelhos)
    ? array_map('intval', $diasVelhos)
    : array_map('intval', (array) ($agenda['dias_indisponiveis'] ?? [0, 6]));

$horariosTexto = $valor(
    'agenda.horarios',
    implode("\n", array_map('strval', (array) ($agenda['horarios'] ?? [])))
);
$horariosSabadoTexto = $valor(
    'agenda.horarios_sabado',
    implode("\n", array_map('strval', (array) ($agenda['horarios_sabado'] ?? [])))
);
$tipoPredefinido = $valor('agenda.tipo_predefinido', (string) ($agenda['tipo_predefinido'] ?? 'ONLINE'));

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('head'); ?>
  <title><?= e($titulo) ?></title>
<?php \App\Core\View::secaoFim(); ?>

<?php \App\Core\View::secaoInicio('content'); ?>
<?php $flashOk = mostrar_flash('ok'); $flashErro = mostrar_flash('erro'); ?>
<?= \App\Core\View::parcial('admin/_topo', ['titulo' => $tituloAdmin, 'subtitulo' => $subtituloAdmin, 'aba' => 'definicoes']) ?>

<?php if (is_string($flashOk) && $flashOk !== ''): ?>
  <div class="mb-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary"><?= e($flashOk) ?></div>
<?php endif; ?>
<?php if (is_string($flashErro) && $flashErro !== ''): ?>
  <div class="mb-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?= e($flashErro) ?></div>
<?php endif; ?>

<form method="POST" action="<?= e($rotaAcao) ?>" enctype="multipart/form-data" novalidate class="space-y-6">
  <?= csrf_campo() ?>

  <div class="grid gap-6 lg:grid-cols-2 items-start">
    <!-- Contacto -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Contacto</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Aparece no rodapé, na página de contacto e nas mensagens automáticas.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label for="contact_email" class="block text-sm font-medium text-foreground mb-1.5">E-mail</label>
          <input id="contact_email" name="contact[email]" type="email" autocomplete="email"
                 value="<?= e($atual('contact.email')) ?>" placeholder="info@havredesign.ao" class="<?= e($inp('contact.email')) ?>" />
          <?php if ($erroDe('contact.email') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.email')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="contact_phone_1" class="block text-sm font-medium text-foreground mb-1.5">Telefone 1</label>
          <input id="contact_phone_1" name="contact[phone_1]" type="text" autocomplete="tel"
                 value="<?= e($atual('contact.phone_1')) ?>" placeholder="+244 926 184 104" class="<?= e($inp('contact.phone_1')) ?>" />
          <?php if ($erroDe('contact.phone_1') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.phone_1')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="contact_phone_2" class="block text-sm font-medium text-foreground mb-1.5">Telefone 2</label>
          <input id="contact_phone_2" name="contact[phone_2]" type="text"
                 value="<?= e($atual('contact.phone_2')) ?>" placeholder="+244 926 334 650" class="<?= e($inp('contact.phone_2')) ?>" />
          <?php if ($erroDe('contact.phone_2') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.phone_2')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="contact_phone_3" class="block text-sm font-medium text-foreground mb-1.5">Telefone 3</label>
          <input id="contact_phone_3" name="contact[phone_3]" type="text"
                 value="<?= e($atual('contact.phone_3')) ?>" placeholder="+244 939 718 811" class="<?= e($inp('contact.phone_3')) ?>" />
          <?php if ($erroDe('contact.phone_3') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.phone_3')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="contact_whatsapp" class="block text-sm font-medium text-foreground mb-1.5">WhatsApp (visível)</label>
          <input id="contact_whatsapp" name="contact[whatsapp]" type="text"
                 value="<?= e($atual('contact.whatsapp')) ?>" placeholder="+244 926 184 104" class="<?= e($inp('contact.whatsapp')) ?>" />
          <?php if ($erroDe('contact.whatsapp') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.whatsapp')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="contact_hours" class="block text-sm font-medium text-foreground mb-1.5">Horário (rodapé)</label>
          <input id="contact_hours" name="contact[hours]" type="text"
                 value="<?= e($atual('contact.hours')) ?>" placeholder="Segunda a sexta — 09h às 18h" class="<?= e($inp('contact.hours')) ?>" />
          <p class="text-xs text-muted-foreground mt-1">Texto numa só linha.</p>
          <?php if ($erroDe('contact.hours') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.hours')) ?></p><?php endif; ?>
        </div>

        <div class="sm:col-span-2">
          <label for="contact_address_full" class="block text-sm font-medium text-foreground mb-1.5">Morada</label>
          <textarea id="contact_address_full" name="contact[address_full]" rows="2" class="<?= e($inp('contact.address_full')) ?>"><?= e($atual('contact.address_full')) ?></textarea>
          <?php if ($erroDe('contact.address_full') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.address_full')) ?></p><?php endif; ?>
        </div>

        <div class="sm:col-span-2">
          <label for="contact_hours_lines" class="block text-sm font-medium text-foreground mb-1.5">Horário (página de contacto)</label>
          <textarea id="contact_hours_lines" name="contact[hours_lines]" rows="3" class="<?= e($inp('contact.hours_lines')) ?>"><?= e($atual('contact.hours_lines')) ?></textarea>
          <p class="text-xs text-muted-foreground mt-1">Um período por linha, tal como deve aparecer no site.</p>
          <?php if ($erroDe('contact.hours_lines') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('contact.hours_lines')) ?></p><?php endif; ?>
        </div>
      </div>
    </fieldset>

    <!-- Marca -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Marca</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Nome e slogan usados no site, no SEO e nos e-mails.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label for="brand_name" class="block text-sm font-medium text-foreground mb-1.5">Nome</label>
          <input id="brand_name" name="brand[name]" type="text"
                 value="<?= e($atual('brand.name')) ?>" placeholder="HAVREDESIGN" class="<?= e($inp('brand.name')) ?>" />
          <?php if ($erroDe('brand.name') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('brand.name')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="brand_legal_name" class="block text-sm font-medium text-foreground mb-1.5">Nome legal</label>
          <input id="brand_legal_name" name="brand[legal_name]" type="text"
                 value="<?= e($atual('brand.legal_name')) ?>" placeholder="HAVREDESIGN — Arquitetura e Construção, Lda." class="<?= e($inp('brand.legal_name')) ?>" />
          <?php if ($erroDe('brand.legal_name') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('brand.legal_name')) ?></p><?php endif; ?>
        </div>

        <div class="sm:col-span-2">
          <label for="brand_tagline" class="block text-sm font-medium text-foreground mb-1.5">Slogan</label>
          <input id="brand_tagline" name="brand[tagline]" type="text"
                 value="<?= e($atual('brand.tagline')) ?>" placeholder="Arquitetura como Refúgio, excelência em cada detalhe." class="<?= e($inp('brand.tagline')) ?>" />
          <?php if ($erroDe('brand.tagline') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('brand.tagline')) ?></p><?php endif; ?>
        </div>
      </div>
    </fieldset>

    <!-- Redes sociais -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Redes sociais</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Endereços completos do rodapé, começados por https://.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label for="social_instagram" class="block text-sm font-medium text-foreground mb-1.5">Instagram</label>
          <input id="social_instagram" name="social[instagram]" type="url"
                 value="<?= e($atual('social.instagram')) ?>" placeholder="https://www.instagram.com/havredesign.ao/" class="<?= e($inp('social.instagram')) ?>" />
          <?php if ($erroDe('social.instagram') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('social.instagram')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="social_facebook" class="block text-sm font-medium text-foreground mb-1.5">Facebook</label>
          <input id="social_facebook" name="social[facebook]" type="url"
                 value="<?= e($atual('social.facebook')) ?>" placeholder="https://www.facebook.com/…" class="<?= e($inp('social.facebook')) ?>" />
          <?php if ($erroDe('social.facebook') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('social.facebook')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="social_linkedin" class="block text-sm font-medium text-foreground mb-1.5">LinkedIn</label>
          <input id="social_linkedin" name="social[linkedin]" type="url"
                 value="<?= e($atual('social.linkedin')) ?>" placeholder="https://www.linkedin.com/company/…" class="<?= e($inp('social.linkedin')) ?>" />
          <?php if ($erroDe('social.linkedin') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('social.linkedin')) ?></p><?php endif; ?>
        </div>
      </div>
    </fieldset>

    <!-- WhatsApp -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">WhatsApp</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Botão flutuante e mensagem pré-preenchida da conversa.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label for="whatsapp_number" class="block text-sm font-medium text-foreground mb-1.5">Número (só algarismos, com indicativo)</label>
          <input id="whatsapp_number" name="whatsapp[number]" type="text"
                 value="<?= e($atual('whatsapp.number')) ?>" placeholder="244926184104" class="<?= e($inp('whatsapp.number')) ?>" />
          <p class="text-xs text-muted-foreground mt-1">Usado em https://wa.me/… — ex.: 244926184104.</p>
          <?php if ($erroDe('whatsapp.number') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('whatsapp.number')) ?></p><?php endif; ?>
        </div>

        <div class="sm:col-span-2">
          <label for="whatsapp_open_message" class="block text-sm font-medium text-foreground mb-1.5">Mensagem inicial</label>
          <textarea id="whatsapp_open_message" name="whatsapp[open_message]" rows="4" class="<?= e($inp('whatsapp.open_message')) ?>"><?= e($atual('whatsapp.open_message')) ?></textarea>
          <?php if ($erroDe('whatsapp.open_message') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('whatsapp.open_message')) ?></p><?php endif; ?>
        </div>
      </div>
    </fieldset>

    <!-- Networking (QR Code da página /networking) -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Networking</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        QR Code da página permanente /networking. O ficheiro carregado substitui sempre
        <span class="font-mono">images/qrcode-networking.png</span>.
      </p>

      <div class="grid gap-4">
        <div>
          <label for="networking_qr_image" class="block text-sm font-medium text-foreground mb-1.5">Imagem do QR Code</label>
          <input id="networking_qr_image" name="qr_image" type="file" accept="image/png,image/jpeg,image/webp"
                 class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-muted file:text-foreground hover:file:bg-muted/70" />
          <p class="text-xs text-muted-foreground mt-1">
            PNG ou JPEG quadrado (máx. 4 MB). Deixe vazio para manter a imagem actual.
          </p>
        </div>

        <?php if (!empty($qrExiste)): ?>
          <div>
            <p class="text-sm font-medium text-foreground mb-2">Imagem actual</p>
            <img src="<?= e(asset($qrCaminho)) ?>" alt="QR Code actual da página Networking"
                 width="120" height="120"
                 class="block w-[120px] h-[120px] rounded-md border border-border bg-white p-1" />
          </div>
        <?php endif; ?>
      </div>
    </fieldset>

    <!-- Agenda -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full lg:col-span-2">
      <h2 class="text-lg font-semibold text-foreground">Agenda</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        Horários do agendamento online, tipo pré-definido e o texto apresentado para cada tipo de reunião.
      </p>

      <div class="grid gap-4">
        <div>
          <label for="agenda_horarios" class="block text-sm font-medium text-foreground mb-1.5">Horários</label>
          <textarea id="agenda_horarios" name="agenda[horarios]" rows="5" spellcheck="false"
                    class="<?= e($inp('agenda.horarios')) ?> font-mono"><?= e($horariosTexto) ?></textarea>
          <p class="text-xs text-muted-foreground mt-1">Um horário por linha, no formato HH:MM (ex.: 09:00).</p>
          <?php if ($erroDe('agenda.horarios') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('agenda.horarios')) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="agenda_horarios_sabado" class="block text-sm font-medium text-foreground mb-1.5">Horários de sábado</label>
          <textarea id="agenda_horarios_sabado" name="agenda[horarios_sabado]" rows="3" spellcheck="false"
                    class="<?= e($inp('agenda.horarios_sabado')) ?> font-mono"><?= e($horariosSabadoTexto) ?></textarea>
          <p class="text-xs text-muted-foreground mt-1">Um horário por linha. Pode ficar vazio.</p>
          <?php if ($erroDe('agenda.horarios_sabado') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('agenda.horarios_sabado')) ?></p><?php endif; ?>
        </div>

        <div>
          <span class="block text-sm font-medium text-foreground mb-1.5">Dias indisponíveis</span>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <?php foreach ($diasNomes as $numero => $dia): ?>
              <label class="flex items-center gap-2 px-3 py-2 rounded-md border <?= $erroDe('agenda.dias_indisponiveis') !== null ? 'border-destructive' : 'border-border' ?> bg-card text-sm text-foreground cursor-pointer hover:bg-muted transition-colors">
                <input type="checkbox" name="agenda[dias_indisponiveis][]" value="<?= (int) $numero ?>"
                       <?= in_array($numero, $diasAtuais, true) ? 'checked' : '' ?>
                       class="h-4 w-4 rounded border-border" />
                <?= e($dia) ?>
              </label>
            <?php endforeach; ?>
          </div>
          <p class="text-xs text-muted-foreground mt-1">Marque os dias em que não é possível agendar.</p>
          <?php if ($erroDe('agenda.dias_indisponiveis') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('agenda.dias_indisponiveis')) ?></p><?php endif; ?>
        </div>

        <div class="sm:max-w-md">
          <label for="agenda_tipo_predefinido" class="block text-sm font-medium text-foreground mb-1.5">Tipo de reunião pré-definido</label>
          <select id="agenda_tipo_predefinido" name="agenda[tipo_predefinido]" class="<?= e($inp('agenda.tipo_predefinido')) ?>">
            <?php foreach ($rotulosTipos as $codigo => $rotulo): ?>
              <option value="<?= e($codigo) ?>" <?= $tipoPredefinido === $codigo ? 'selected' : '' ?>>
                <?= e($codigo . ' — ' . (string) (($agenda['tipos'][$codigo]['label'] ?? null) ?: $rotulo)) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if ($erroDe('agenda.tipo_predefinido') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('agenda.tipo_predefinido')) ?></p><?php endif; ?>
        </div>

        <div class="border-t border-border pt-5 mt-2">
          <h3 class="text-sm font-semibold text-foreground mb-1">Textos dos tipos de reunião</h3>
          <p class="text-xs text-muted-foreground mb-4">
            Rótulo e nota apresentados no site, no resumo do agendamento e nos e-mails.
          </p>

          <div class="grid gap-4 sm:grid-cols-2">
            <?php foreach (array_keys($rotulosTipos) as $codigo): ?>
              <?php
                $campoLabel = 'agenda.tipos.' . $codigo . '.label';
                $campoNota  = 'agenda.tipos.' . $codigo . '.nota';
                $tipoAtual  = is_array($agenda['tipos'][$codigo] ?? null) ? $agenda['tipos'][$codigo] : [];
                $labelAtual = $valor($campoLabel, (string) ($tipoAtual['label'] ?? $codigo));
                $notaAtual  = $valor($campoNota, (string) ($tipoAtual['nota'] ?? ''));
              ?>
              <div class="rounded-lg border border-border bg-background p-4 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary"><?= e($codigo) ?></p>

                <div>
                  <label for="agenda_tipo_label_<?= e($codigo) ?>" class="block text-sm font-medium text-foreground mb-1.5">Rótulo</label>
                  <input id="agenda_tipo_label_<?= e($codigo) ?>" name="agenda[tipos][<?= e($codigo) ?>][label]" type="text" maxlength="120"
                         value="<?= e($labelAtual) ?>" class="<?= e($inp($campoLabel)) ?>" />
                  <?php if ($erroDe($campoLabel) !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe($campoLabel)) ?></p><?php endif; ?>
                </div>

                <div>
                  <label for="agenda_tipo_nota_<?= e($codigo) ?>" class="block text-sm font-medium text-foreground mb-1.5">Nota</label>
                  <textarea id="agenda_tipo_nota_<?= e($codigo) ?>" name="agenda[tipos][<?= e($codigo) ?>][nota]" rows="3" maxlength="300"
                            class="<?= e($inp($campoNota)) ?>"><?= e($notaAtual) ?></textarea>
                  <p class="text-xs text-muted-foreground mt-1">Deixe vazio para não mostrar nota.</p>
                  <?php if ($erroDe($campoNota) !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe($campoNota)) ?></p><?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <p class="text-xs text-muted-foreground mt-3">
            A morada só é pedida na visita ao local — esse comportamento não é editável aqui.
          </p>
        </div>
      </div>
    </fieldset>

    <!-- Caso real (página Processo) -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full lg:col-span-2">
      <h2 class="text-lg font-semibold text-foreground">Caso real</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        Local apresentado no estudo de caso «Casa Vila Nova», na página Processo.
      </p>

      <div class="grid gap-4">
        <div class="sm:max-w-2xl">
          <label for="process_case_local" class="block text-sm font-medium text-foreground mb-1.5">Local</label>
          <input id="process_case_local" name="process[case_local]" type="text" maxlength="255"
                 value="<?= e($atual('process.case_local')) ?>"
                 placeholder="Benfica, Via Expressa, Bairro Tchinguari, Rua 1, Talatona, Luanda"
                 class="<?= e($inp('process.case_local')) ?>" />
          <p class="text-xs text-muted-foreground mt-1">Deixe vazio para esconder o campo no site.</p>
          <?php if ($erroDe('process.case_local') !== null): ?><p class="text-xs text-destructive mt-1"><?= e($erroDe('process.case_local')) ?></p><?php endif; ?>
        </div>
      </div>
    </fieldset>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-6">
    <p class="text-sm text-muted-foreground">Todas as secções são guardadas num único passo.</p>
    <button type="submit"
            class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
      Guardar definições
    </button>
  </div>
</form>

<?= \App\Core\View::parcial('admin/_rodape') ?>
<?php \App\Core\View::secaoFim(); ?>
