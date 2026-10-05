<?php
/**
 * Roteamento nomeado — equivalente a route('nome') do Laravel.
 * Se as URLs mudarem, altera-se SÓ aqui.
 */

declare(strict_types=1);

if (!function_exists('rota')) {
    function rota(string $nome, array $parametros = []): string
    {
        static $mapa = null;

        if ($mapa === null) {
            $mapa = [
                'home'                    => '/',
                'sobre'                   => '/sobre',
                'servicos'                => '/servicos',
                'havre-solucoes'          => '/havre-solucoes',
                'portfolio'               => '/portfolio',
                'processo'                => '/processo',
                'faq'                     => '/faq',
                'contacto'                => '/contacto',
                'solicitar-projeto'       => '/solicitar-projeto',
                'agendar'                 => '/agendar',
                'networking'              => '/networking',
                'privacidade'             => '/politica-de-privacidade',
                'termos'                  => '/termos-de-uso',
                'entrar'                  => '/entrar',
                'registar'                => '/registar',
                'sair'                    => '/sair',
                'conta'                   => '/conta',
                'admin'                   => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin'),
                'admin.servicos'          => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/servicos',
                'admin.portfolio'         => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/portfolio',
                'admin.solucoes'          => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/solucoes',
                'admin.pedidos'           => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/pedidos',
                'admin.agendamentos'      => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/agendamentos',
                'admin.mensagens'         => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/mensagens',
                'admin.testemunhos'       => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/testemunhos',
                'admin.definicoes'        => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/definicoes',
                'admin.utilizadores'      => '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin') . '/utilizadores',
                'recuperar-palavra-passe' => '/recuperar-palavra-passe',
                'redefinir-palavra-passe' => '/redefinir-palavra-passe',
            ];
        }

        $caminho = $mapa[$nome] ?? '/';

        foreach ($parametros as $chave => $valor) {
            $caminho = preg_replace('/\{' . preg_quote((string) $chave, '/') . '\}/', rawurlencode((string) $valor), $caminho);
        }

        return $caminho;
    }
}
