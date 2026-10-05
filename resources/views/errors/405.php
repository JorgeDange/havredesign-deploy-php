<?php
/** 405 — método não permitido */
\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
<section class="seccao" style="text-align:center;padding:96px 24px;">
    <p class="eyebrow">Erro 405</p>
    <h1>Método não permitido</h1>
    <p style="max-width:520px;margin:16px auto 32px;">Este endereço não aceita o método usado.</p>
    <a class="botao botao-primario" href="/">Voltar ao início</a>
</section>
<?php \App\Core\View::secaoFim(); ?>
