<?php
// app/routes.php

use Pecee\SimpleRouter\SimpleRouter as Router;

// Rotas Públicas (acessadas pelos alunos)
Router::get('/', 'app\controllers\HomeController@showHome');
Router::post('/validate', 'app\controllers\HomeController@validateIp');
Router::get('/get-iframe-content', 'app\controllers\HomeController@getIframeContent');


// Agrupa todas as rotas de admin sob um mesmo "middleware" de verificação de login
Router::group(['prefix' => '/admin', 'middleware' => \app\middleware\AuthMiddleware::class], function () {

    // Rota principal do dashboard
    Router::get('/', 'app\controllers\AdminController@dashboard');

    // Rota para mostrar o formulário de criação de evento
    Router::get('/events/create', 'app\controllers\AdminController@createEventForm');

    // Rota para processar o formulário
    Router::post('/events/store', 'app\controllers\AdminController@storeEvent');

});

Router::start();
