<?php

declare(strict_types=1);

/**
 * Runner de testes — corre todas as suites de tests/suites/.
 *
 * Uso:
 *   1. Arrancar o servidor:  php -S 127.0.0.1:8090 router.php
 *   2. Correr os testes:     php tests/executar.php
 *
 * Sai com código 0 se tudo passar, 1 se houver falhas.
 */

require __DIR__ . '/Apoio.php';

if (!Apoio::servidorDisponivel()) {
    echo "ERRO: servidor não responde em " . Apoio::$base . "\n";
    echo "Arranque-o primeiro:  php -S 127.0.0.1:8090 router.php\n";

    exit(1);
}

$suites = glob(__DIR__ . '/suites/*.php') ?: [];
sort($suites);

if ($suites === []) {
    echo "Nenhuma suite encontrada em tests/suites/.\n";
    exit(1);
}

$t0 = microtime(true);

foreach ($suites as $suite) {
    Apoio::iniciarSuite(basename($suite, '.php'));
    require $suite;
    Apoio::terminarSuite();
}

$decorrido = round(microtime(true) - $t0, 1);
$codigo = Apoio::resumo();
echo "Tempo: {$decorrido}s\n";

exit($codigo);
