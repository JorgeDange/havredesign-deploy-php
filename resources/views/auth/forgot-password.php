<?php
/**
 * Recuperar palavra-passe — pedido de link de redefinição.
 * Convertida de backend/resources/views/auth/forgot-password.blade.php
 */
$titulo = 'Recuperar palavra-passe - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');

$flashOk   = mostrar_flash('ok');
$flashErro = mostrar_flash('erro');
$erroEmail = erro_de('email');
?>
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Recuperar palavra-passe</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">
      Esqueceu a palavra-passe? Informe o seu email e enviaremos um link para escolher uma nova.
    </p>

    <?php if ($flashOk !== null): ?>
      <div class="mb-4 font-medium text-sm text-green-600"><?= e($flashOk) ?></div>
    <?php endif; ?>
    <?php if ($flashErro !== null): ?>
      <div class="mb-4 font-medium text-sm text-red-600"><?= e($flashErro) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= e(rota('recuperar-palavra-passe')) ?>">
      <?= csrf_campo() ?>

      <div>
        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
        <input id="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="email" name="email" value="<?= e(velho('email')) ?>" required autofocus placeholder="nome@exemplo.ao">
        <?php if ($erroEmail !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroEmail) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="flex items-center justify-between mt-6">
        <a class="text-sm text-foreground/70 underline hover:text-foreground" href="<?= e(rota('entrar')) ?>">
          Voltar a entrar
        </a>

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Enviar link
        </button>
      </div>
    </form>
  </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
