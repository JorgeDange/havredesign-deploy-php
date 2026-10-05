<?php
/** 419 — token CSRF inválido/expirado */
\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
<section class="seccao" style="text-align:center;padding:96px 24px;">
    <p class="eyebrow">Erro 419</p>
    <h1>Sessão expirada</h1>
    <p style="max-width:520px;margin:16px auto 32px;">A sua sessão expirou. Volte atrás e tente novamente.</p>
    <a class="botao botao-primario" href="/">Voltar ao início</a>
</section>
<?php \App\Core\View::secaoFim(); ?>
