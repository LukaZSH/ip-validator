<?php

use Pecee\SimpleRouter\SimpleRouter;
use app\controllers\HomeController;


// Rota para a página inicial (home)
SimpleRouter::get('/', [HomeController::class, 'index']);
SimpleRouter::get('/ip-validator/', [HomeController::class, 'index']);

// Rota para validar o IP do usuário (POST)
SimpleRouter::post('/ip-validator/validate', [HomeController::class, 'validateIP']);

// Rota para acessar o forms.html
SimpleRouter::get('/ip-validator/forms.html', function() {
    include 'forms.html';
});

// Rota para acessar o iframe_config.json
SimpleRouter::get('/ip-validator/config/iframe_config.json', function() {
    $filePath = __DIR__ . '/../config/iframe_config.json';

    if (file_exists($filePath)) {
        header('Content-Type: application/json');
        echo file_get_contents($filePath);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Arquivo não encontrado']);
    }
});

// Nova rota para salvar o código do iframe (POST)
SimpleRouter::post('/save_iframe.php', function() {
    include 'save_iframe.php';
});
