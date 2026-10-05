<?php
/**
 * Entrar — formulário de login.
 * Convertida de backend/resources/views/auth/login.blade.php
 */
$titulo = 'Entrar - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');

$flashOk      = mostrar_flash('ok');
$flashErro    = mostrar_flash('erro');
$erroEmail    = erro_de('email');
$erroPassword = erro_de('password');
?>
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Entrar</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Aceda à sua área para acompanhar pedidos e agendamentos.</p>

    <?php if ($flashOk !== null): ?>
      <div class="mb-4 font-medium text-sm text-green-600"><?= e($flashOk) ?></div>
    <?php endif; ?>
    <?php if ($flashErro !== null): ?>
      <div class="mb-4 font-medium text-sm text-red-600"><?= e($flashErro) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= e(rota('entrar')) ?>">
      <?= csrf_campo() ?>

      <div>
        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
        <input id="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="email" name="email" value="<?= e(velho('email')) ?>" required autofocus autocomplete="username" placeholder="nome@exemplo.ao">
        <?php if ($erroEmail !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroEmail) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="mt-4">
        <label for="password" class="block font-medium text-sm text-gray-700">Palavra-passe</label>
        <input id="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="password" name="password" required autocomplete="current-password">
        <?php if ($erroPassword !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroPassword) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center gap-2">
          <input id="remember_me" type="checkbox" class="rounded border-border text-primary focus:ring-secondary" name="remember">
          <span class="text-sm text-foreground/80">Manter sessão iniciada</span>
        </label>
      </div>

      <div class="flex items-center justify-between mt-6">
        <a class="text-sm text-foreground/70 underline hover:text-foreground" href="<?= e(rota('recuperar-palavra-passe')) ?>">
          Esqueceu a palavra-passe?
        </a>

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Entrar
        </button>
      </div>
    </form>

    <p class="text-sm text-foreground/70 mt-6 pt-5 border-t border-border">
      Ainda não tem conta?
      <a href="<?= e(rota('registar')) ?>" class="font-medium text-secondary hover:underline">Registe-se</a>
    </p>
  </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
