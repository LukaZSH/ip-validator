<?php

use Pecee\SimpleRouter\SimpleRouter as Router;

// Rotas de Autenticação
Router::get('/login', 'app\controllers\AuthController@showLoginForm')->setName('login');
Router::post('/login', 'app\controllers\AuthController@login');
Router::get('/logout', 'app\controllers\AuthController@logout')->setName('logout');

// Rota da página inicial
Router::get('/', 'app\controllers\HomeController@showHome');

// Rota pública para acessar um evento específico via slug
Router::get('/evento/{slug}', 'app\controllers\HomeController@showEventPage');

// Grupo de rotas do painel de administração
Router::group(['prefix' => '/admin', 'middleware' => \app\middleware\AuthMiddleware::class], function () {
    Router::get('/', 'app\controllers\AdminController@dashboard');
    Router::get('/events/create', 'app\controllers\AdminController@createEventForm');
    Router::post('/events/store', 'app\controllers\AdminController@storeEvent');
    Router::get('/events/edit', 'app\controllers\AdminController@editEventForm');
    Router::post('/events/update', 'app\controllers\AdminController@updateEvent');
    Router::post('/events/delete', 'app\controllers\AdminController@deleteEvent');
    Router::get('/events/qrcode', 'app\controllers\AdminController@generateQrCode');
});

Router::start();
