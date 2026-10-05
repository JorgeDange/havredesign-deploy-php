<?php
/** 404 — página não encontrada */
\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
<section class="seccao" style="text-align:center;padding:96px 24px;">
    <p class="eyebrow">Erro 404</p>
    <h1>Página não encontrada</h1>
    <p style="max-width:520px;margin:16px auto 32px;">O endereço que procurou não existe ou foi movido.</p>
    <a class="botao botao-primario" href="/">Voltar ao início</a>
</section>
<?php \App\Core\View::secaoFim(); ?>
