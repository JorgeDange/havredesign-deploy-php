<?php
/**
 * Home — página inicial.
 * TODO Fase 6: converter o conteúdo completo de home/index.blade.php.
 */
$site = \App\Support\Site::site();
\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');
?>
<section class="seccao" style="padding:96px 24px;text-align:center;max-width:960px;margin:0 auto;">
    <p class="eyebrow">Arquitetura e Design de Interiores</p>
    <h1 style="font-family:Georgia,serif;font-size:clamp(2rem,5vw,3.5rem);margin:12px 0 16px;">
        Arquitetura como refúgio,<br>excelência em cada detalhe.
    </h1>
    <p style="max-width:640px;margin:0 auto 32px;line-height:1.7;color:#4b5563;">
        Criamos ambientes que acolhem, inspiram e transformam a forma como as pessoas vivem os espaços.
        Projeto arquitetónico, design de interiores e fiscalização em Luanda.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a class="botao botao-primario" href="<?= rota('servicos') ?>">Ver serviços</a>
        <a class="botao" href="<?= rota('contacto') ?>">Falar connosco</a>
    </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
