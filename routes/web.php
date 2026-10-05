<?php

/**
 * Rotas públicas — HAVREDESIGN (PHP puro).
 * $router é criado no front controller (public/index.php).
 */

declare(strict_types=1);

use App\Controllers\AppointmentController;
use App\Controllers\ContactController;
use App\Controllers\ProjectRequestController;
use App\Controllers\SeoController;
use App\Core\Response;
use App\Core\View;

// ------------------------------------------------------------------
// Páginas públicas (views em resources/views/pages/)
// ------------------------------------------------------------------
$router->get('/', fn () => Response::html(View::render('home/index')));
$router->get('/sobre', fn () => Response::html(View::render('pages/about')));
$router->get('/servicos', fn () => Response::html(View::render('services/index')));
// /havre-solucoes é a rota principal; /orcamentos redirecciona (301 legado)
$router->get('/havre-solucoes', fn () => Response::html(View::render('solutions/index')));
$router->get('/orcamentos', fn () => Response::redirecionarPermanente(rota('havre-solucoes')));
$router->get('/portfolio', fn () => Response::html(View::render('portfolio/index')));
$router->get('/portfolio/{slug}', fn ($r) => Response::html(View::render('portfolio/show', ['slug' => $r->parametro('slug')])));
$router->get('/processo', fn () => Response::html(View::render('pages/process')));
$router->get('/faq', fn () => Response::html(View::render('pages/faq')));
$router->get('/contacto', fn () => Response::html(View::render('contact/index')));
$router->get('/solicitar-projeto', fn () => Response::html(View::render('project/create')));
$router->get('/agendar', fn () => Response::html(View::render('booking/create')));
$router->get('/networking', fn () => Response::html(View::render('networking')));
$router->get('/politica-de-privacidade', fn () => Response::html(View::render('legal/privacy')));
$router->get('/termos-de-uso', fn () => Response::html(View::render('legal/terms')));

// ------------------------------------------------------------------
// Formulários (POST + CSRF)
// ------------------------------------------------------------------
$router->post('/contacto', [ContactController::class, 'store'], ['csrf']);
$router->post('/solicitar-projeto', [ProjectRequestController::class, 'store'], ['csrf']);
$router->post('/agendar', [AppointmentController::class, 'store'], ['csrf']);
$router->get('/agendar/disponibilidade', [AppointmentController::class, 'disponibilidade']);

// ------------------------------------------------------------------
// SEO
// ------------------------------------------------------------------
$router->get('/robots.txt', [SeoController::class, 'robots']);
$router->get('/sitemap.xml', [SeoController::class, 'sitemap']);

// ------------------------------------------------------------------
// Autenticação
// ------------------------------------------------------------------
$router->grupo(['guest'], function ($r) {
    $r->get('/entrar', [App\Controllers\Auth\EntrarController::class, 'formulario']);
    $r->post('/entrar', [App\Controllers\Auth\EntrarController::class, 'entrar'], ['csrf']);
    $r->get('/registar', [App\Controllers\Auth\RegistarController::class, 'formulario']);
    $r->post('/registar', [App\Controllers\Auth\RegistarController::class, 'registar'], ['csrf']);
    $r->get('/recuperar-palavra-passe', [App\Controllers\Auth\RecuperarController::class, 'formulario']);
    $r->post('/recuperar-palavra-passe', [App\Controllers\Auth\RecuperarController::class, 'enviar'], ['csrf']);
    $r->get('/redefinir-palavra-passe/{token}', [App\Controllers\Auth\RedefinirController::class, 'formulario']);
    $r->post('/redefinir-palavra-passe', [App\Controllers\Auth\RedefinirController::class, 'redefinir'], ['csrf']);
});

$router->post('/sair', [App\Controllers\Auth\SairController::class, 'sair'], ['csrf']);

// ------------------------------------------------------------------
// Área de cliente
// ------------------------------------------------------------------
$router->grupo(['auth'], function ($r) {
    $r->get('/conta', [App\Controllers\ContaController::class, 'index']);
});

// ------------------------------------------------------------------
// Painel de administração (prefixo obscurecido)
// ------------------------------------------------------------------
$router->grupo(['auth', 'admin'], function ($r) {
    $prefixo = '/' . \App\Core\Config::obter('ADMIN_PATH', 'admin');

    $r->get($prefixo, [App\Controllers\Admin\DashboardController::class, 'index']);
    $r->get($prefixo . '/servicos', [App\Controllers\Admin\ServiceController::class, 'index']);
    $r->get($prefixo . '/servicos/novo', [App\Controllers\Admin\ServiceController::class, 'criar']);
    $r->post($prefixo . '/servicos', [App\Controllers\Admin\ServiceController::class, 'guardar'], ['csrf']);
    $r->get($prefixo . '/servicos/{id}/editar', [App\Controllers\Admin\ServiceController::class, 'editar']);
    $r->post($prefixo . '/servicos/{id}', [App\Controllers\Admin\ServiceController::class, 'atualizar'], ['csrf']);
    $r->post($prefixo . '/servicos/{id}/alternar', [App\Controllers\Admin\ServiceController::class, 'alternar'], ['csrf']);
    $r->post($prefixo . '/servicos/{id}/apagar', [App\Controllers\Admin\ServiceController::class, 'apagar'], ['csrf']);

    // Portefólio (+ capa e galeria por upload em disco público)
    $r->get($prefixo . '/portfolio', [App\Controllers\Admin\PortfolioController::class, 'index']);
    $r->get($prefixo . '/portfolio/novo', [App\Controllers\Admin\PortfolioController::class, 'criar']);
    $r->post($prefixo . '/portfolio', [App\Controllers\Admin\PortfolioController::class, 'guardar'], ['csrf']);
    $r->post($prefixo . '/portfolio/{id}/apagar', [App\Controllers\Admin\PortfolioController::class, 'apagar'], ['csrf']);
    $r->post($prefixo . '/portfolio/{id}/galeria', [App\Controllers\Admin\PortfolioController::class, 'galeria'], ['csrf']);
    $r->post($prefixo . '/galeria/{id}/apagar', [App\Controllers\Admin\PortfolioController::class, 'apagarGaleria'], ['csrf']);
    $r->get($prefixo . '/portfolio/{id}/editar', [App\Controllers\Admin\PortfolioController::class, 'editar']);
    $r->post($prefixo . '/portfolio/{id}', [App\Controllers\Admin\PortfolioController::class, 'atualizar'], ['csrf']);

    // Soluções comerciais (tabela solutions)
    $r->get($prefixo . '/solucoes', [App\Controllers\Admin\SolutionController::class, 'index']);
    $r->get($prefixo . '/solucoes/nova', [App\Controllers\Admin\SolutionController::class, 'criar']);
    $r->post($prefixo . '/solucoes', [App\Controllers\Admin\SolutionController::class, 'guardar'], ['csrf']);
    $r->post($prefixo . '/solucoes/{id}/apagar', [App\Controllers\Admin\SolutionController::class, 'apagar'], ['csrf']);
    $r->get($prefixo . '/solucoes/{id}/editar', [App\Controllers\Admin\SolutionController::class, 'editar']);
    $r->post($prefixo . '/solucoes/{id}', [App\Controllers\Admin\SolutionController::class, 'atualizar'], ['csrf']);

    // Pedidos de orçamento (estado + detalhe e anexos)
    $r->get($prefixo . '/pedidos', [App\Controllers\Admin\ProjectRequestController::class, 'index']);
    $r->post($prefixo . '/pedidos/{id}/apagar', [App\Controllers\Admin\ProjectRequestController::class, 'apagar'], ['csrf']);
    $r->get($prefixo . '/pedidos/{id}/anexos/{anexo}/ver', [App\Controllers\Admin\ProjectRequestController::class, 'ver']);
    $r->get($prefixo . '/pedidos/{id}/anexos/{anexo}', [App\Controllers\Admin\ProjectRequestController::class, 'baixar']);
    $r->get($prefixo . '/pedidos/{id}', [App\Controllers\Admin\ProjectRequestController::class, 'mostrar']);
    $r->post($prefixo . '/pedidos/{id}', [App\Controllers\Admin\ProjectRequestController::class, 'atualizar'], ['csrf']);

    // Agendamentos (PENDING/CONFIRMED/CANCELLED + e-mail ao cliente)
    $r->get($prefixo . '/agendamentos', [App\Controllers\Admin\AppointmentController::class, 'index']);
    $r->get($prefixo . '/agendamentos/{id}', [App\Controllers\Admin\AppointmentController::class, 'mostrar']);
    $r->post($prefixo . '/agendamentos/{id}', [App\Controllers\Admin\AppointmentController::class, 'atualizar'], ['csrf']);

    // Mensagens de contacto (new/replied/closed)
    $r->get($prefixo . '/mensagens', [App\Controllers\Admin\ContactMessageController::class, 'index']);
    $r->post($prefixo . '/mensagens/{id}/apagar', [App\Controllers\Admin\ContactMessageController::class, 'apagar'], ['csrf']);
    $r->get($prefixo . '/mensagens/{id}', [App\Controllers\Admin\ContactMessageController::class, 'mostrar']);
    $r->post($prefixo . '/mensagens/{id}', [App\Controllers\Admin\ContactMessageController::class, 'atualizar'], ['csrf']);

    // Testemunhos (aprovação de conteúdos — D6)
    $r->get($prefixo . '/testemunhos', [App\Controllers\Admin\TestimonialController::class, 'index']);
    $r->get($prefixo . '/testemunhos/{id}/editar', [App\Controllers\Admin\TestimonialController::class, 'editar']);
    $r->post($prefixo . '/testemunhos/{id}/apagar', [App\Controllers\Admin\TestimonialController::class, 'apagar'], ['csrf']);
    $r->post($prefixo . '/testemunhos/{id}/alternar', [App\Controllers\Admin\TestimonialController::class, 'alternar'], ['csrf']);
    $r->post($prefixo . '/testemunhos/{id}', [App\Controllers\Admin\TestimonialController::class, 'atualizar'], ['csrf']);

    // Definições (settings: contacto, marca, redes, WhatsApp, agenda)
    $r->get($prefixo . '/definicoes', [App\Controllers\Admin\SettingController::class, 'index']);
    $r->post($prefixo . '/definicoes', [App\Controllers\Admin\SettingController::class, 'atualizar'], ['csrf']);

    // Utilizadores (papel USER/ADMIN)
    $r->get($prefixo . '/utilizadores', [App\Controllers\Admin\UserController::class, 'index']);
    $r->post($prefixo . '/utilizadores/{id}/papel', [App\Controllers\Admin\UserController::class, 'papel'], ['csrf']);
});
