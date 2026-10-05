<?php
/** 403 — acesso negado */
\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
<section class="seccao" style="text-align:center;padding:96px 24px;">
    <p class="eyebrow">Erro 403</p>
    <h1>Acesso negado</h1>
    <p style="max-width:520px;margin:16px auto 32px;">Não tem permissão para aceder a esta página.</p>
    <a class="botao botao-primario" href="/">Voltar ao início</a>
</section>
<?php \App\Core\View::secaoFim(); ?>
