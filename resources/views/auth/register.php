<?php
/**
 * Criar conta — formulário de registo.
 * Convertida de backend/resources/views/auth/register.blade.php
 */
$titulo = 'Criar conta - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');

$erroName                 = erro_de('name');
$erroEmail                = erro_de('email');
$erroPassword             = erro_de('password');
$erroPasswordConfirmation = erro_de('password_confirmation');
?>
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Criar conta</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Registe-se para acompanhar os seus pedidos e agendamentos.</p>

    <?= \App\Core\View::parcial('partials/flash') ?>
  <form method="POST" action="<?= e(rota('registar')) ?>">
      <?= csrf_campo() ?>

      <div>
        <label for="name" class="block font-medium text-sm text-gray-700">Nome</label>
        <input id="name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="name" value="<?= e(velho('name')) ?>" required autofocus autocomplete="name">
        <?php if ($erroName !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroName) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="mt-4">
        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
        <input id="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="email" name="email" value="<?= e(velho('email')) ?>" required autocomplete="username" placeholder="nome@exemplo.ao">
        <?php if ($erroEmail !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroEmail) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="mt-4">
        <label for="password" class="block font-medium text-sm text-gray-700">Palavra-passe</label>
        <input id="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="password" name="password" required autocomplete="new-password">
        <?php if ($erroPassword !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroPassword) ?></li>
          </ul>
        <?php endif; ?>
        <p class="text-xs text-muted-foreground mt-1">Mínimo de 8 caracteres.</p>
      </div>

      <div class="mt-4">
        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar palavra-passe</label>
        <input id="password_confirmation" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password">
        <?php if ($erroPasswordConfirmation !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroPasswordConfirmation) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="flex items-center justify-between mt-6">
        <a class="text-sm text-foreground/70 underline hover:text-foreground" href="<?= e(rota('entrar')) ?>">
          Já tem conta? Entrar
        </a>

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Registar
        </button>
      </div>
    </form>
  </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
