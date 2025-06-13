<?php

use Pecee\SimpleRouter\SimpleRouter;
use app\controllers\HomeController;

// Rota principal agora é a raiz "/"
SimpleRouter::get('/', [HomeController::class, 'index']);

// Rota para validação de IP
SimpleRouter::post('/validate', [HomeController::class, 'validateIP']);

// Rota para a página do formulário
SimpleRouter::get('/forms.html', function() {
    include __DIR__ . '/../forms.html';
});

// Rota para o arquivo de configuração JSON
SimpleRouter::get('/config/iframe_config.json', function() {
    $filePath = __DIR__ . '/../config/iframe_config.json';
    if (file_exists($filePath)) {
        header('Content-Type: application/json');
        readfile($filePath);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Arquivo não encontrado']);
    }
});


// --- ROTAS DE ADMINISTRAÇÃO ---

// Rota para a página de login
SimpleRouter::get('/login', function() {
    include __DIR__ . '/../login.html';
});

// Rota para o script que processa o login
SimpleRouter::post('/auth.php', function() {
    include __DIR__ . '/../auth.php';
});

// Rota para a página de admin
SimpleRouter::get('/admin', function() {
    include __DIR__ . '/../admin.php';
});

// Rota para salvar o iframe
SimpleRouter::post('/save_iframe.php', function() {
    include __DIR__ . '/../save_iframe.php';
});

// Rota para o logout
SimpleRouter::get('/logout', function() {
    include __DIR__ . '/../logout.php';
});