<?php
/**
 * HAVREDESIGN — Front controller (ponto de entrada único).
 * Tudo o que não seja ficheiro/pasta real passa por aqui (ver .htaccess).
 */

declare(strict_types=1);

// --------------------------------------------------------------------------
// 1. Erros — em produção nunca mostrar stack trace no ecrã
// --------------------------------------------------------------------------
$mostrarErros = filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN);

ini_set('display_errors', $mostrarErros ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../storage/logs/php-error.log');
error_reporting(E_ALL);

// --------------------------------------------------------------------------
// 2. Autoload simples do projecto (sem Composer)
// --------------------------------------------------------------------------
spl_autoload_register(function (string $classe): void {
    $prefixos = [
        'App\\'      => __DIR__ . '/../app/',
        'Support\\'  => __DIR__ . '/../app/Support/',
    ];

    foreach ($prefixos as $prefixo => $baseDir) {
        if (str_starts_with($classe, $prefixo)) {
            $relativo = substr($classe, strlen($prefixo));
            // App\Controllers\Public\X → app/Controllers/Public/X.php
            $ficheiro = $baseDir . str_replace('\\', '/', $relativo) . '.php';
            if (is_file($ficheiro)) {
                require $ficheiro;
                return;
            }
        }
    }
});

// Funções auxiliares (helpers)
require __DIR__ . '/../app/Support/Helpers.php';
require __DIR__ . '/../app/Support/Rotas.php';

// Polyfill mbstring (hosting sem extensão)
App\Core\MbstringPolyfill::carregar();

// --------------------------------------------------------------------------
// 3. Bootstrap
// --------------------------------------------------------------------------
App\Core\Config::carregar(dirname(__DIR__));
App\Core\Session::iniciar();
App\Core\Logger::inicializar();

// Estado de formulário do pedido anterior — limpo a cada submissão nova
// (os controllers voltam a gravar se a validação falhar; assim nunca ficam
// erros velhos presos na sessão)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    App\Core\Session::obter('_erros_validacao', null, true);
    App\Core\Session::obter('_old_input', null, true);
}

// --------------------------------------------------------------------------
// 4. Pipeline de segurança (headers em todas as respostas + 301 legacy)
// --------------------------------------------------------------------------
App\Middleware\SecurityHeaders::enviar();
App\Middleware\RedirectLegacyUrls::processar();

// --------------------------------------------------------------------------
// 5. Router → controller → resposta
// --------------------------------------------------------------------------
$router = new App\Core\Router();

require __DIR__ . '/../routes/web.php';

try {
    $router->resolver();
} catch (\Throwable $e) {
    // Nunca mostrar stack trace em produção (APP_DEBUG=false)
    App\Core\Logger::erro('app.excecao_nao_tratada', [
        'mensagem' => $e->getMessage(),
        'ficheiro' => $e->getFile() . ':' . $e->getLine(),
    ]);

    http_response_code(500);
    $debug = App\Core\Config::booleano('APP_DEBUG', false);
    $detalhe = $debug ? $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() : null;
    try {
        echo App\Core\View::render('errors/500', ['detalhe' => $detalhe]);
    } catch (\Throwable) {
        echo '<h1>Erro interno</h1>';
    }
    exit;
}
