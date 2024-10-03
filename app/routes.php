<?php

use Pecee\SimpleRouter\SimpleRouter;
use app\controllers\HomeController;

// Rota para a página inicial (home)
SimpleRouter::get('/ip-validator/', [HomeController::class, 'index']);

// Rota para validar o IP do usuário (POST)
SimpleRouter::post('/ip-validator/validate', [HomeController::class, 'validateIP']);
