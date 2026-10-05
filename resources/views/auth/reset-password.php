<?php
/**
 * Nova palavra-passe — redefinição com token.
 * Convertida de backend/resources/views/auth/reset-password.blade.php
 */
$titulo = 'Nova palavra-passe - HAVREDESIGN';

\App\Core\View::layout('layouts/app');
\App\Core\View::secaoInicio('content');

$pedido = new \App\Core\Request();

// Blade: $request->route('token') — o Request novo não herda parâmetros de rota,
// por isso o controller pode passar $token (ou recorre ao último segmento do caminho).
$token = $token ?? $pedido->parametro('token');
if ($token === '') {
    $pedacos = explode('/', trim((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: ''), '/'));
    $token   = (string) end($pedacos);
}

// Blade: old('email', $request->email) — email pré-preenchido na query string.
$emailInicial = $pedido->query('email');

$erroEmail    = erro_de('email');
$erroPassword = erro_de('password');
$erroPasswordConfirmation = erro_de('password_confirmation');
?>
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Nova palavra-passe</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Defina uma nova palavra-passe para a sua conta.</p>

    <?= \App\Core\View::parcial('partials/flash') ?>
  <form method="POST" action="<?= e(rota('redefinir-palavra-passe')) ?>">
      <?= csrf_campo() ?>

      <input type="hidden" name="token" value="<?= e($token) ?>">

      <div>
        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
        <input id="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="email" name="email" value="<?= e(velho('email', $emailInicial)) ?>" required autofocus autocomplete="username">
        <?php if ($erroEmail !== null): ?>
          <ul class="text-sm text-red-600 space-y-1 mt-2">
            <li><?= e($erroEmail) ?></li>
          </ul>
        <?php endif; ?>
      </div>

      <div class="mt-4">
        <label for="password" class="block font-medium text-sm text-gray-700">Nova palavra-passe</label>
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

      <div class="flex items-center justify-end mt-6">
        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Repor palavra-passe
        </button>
      </div>
    </form>
  </div>
</section>
<?php \App\Core\View::secaoFim(); ?>
