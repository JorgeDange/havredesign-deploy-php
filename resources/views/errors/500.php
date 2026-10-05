<?php
/** 500 — erro interno (nunca mostrar stack trace em produção) */
$debug = \App\Core\Config::booleano('APP_DEBUG', false);
$detalhe = $detalhe ?? null;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro interno — HAVREDESIGN</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#0b1a2b;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
        .caixa{text-align:center;max-width:560px;padding:32px;}
        h1{font-size:2rem;margin:0 0 8px;}
        p{color:#c9d6e3;line-height:1.6;}
        pre{background:#132539;color:#9fb6cc;padding:16px;border-radius:8px;text-align:left;overflow:auto;font-size:13px;}
        a{display:inline-block;margin-top:24px;background:#c9a24b;color:#0b1a2b;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;}
    </style>
</head>
<body>
    <div class="caixa">
        <h1>Algo correu mal</h1>
        <p>Ocorreu um erro interno. A nossa equipa foi notificada.</p>
        <?php if ($debug && $detalhe): ?>
            <pre><?= e($detalhe) ?></pre>
        <?php endif; ?>
        <a href="/">Voltar ao início</a>
    </div>
</body>
</html>
