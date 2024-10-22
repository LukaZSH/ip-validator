<?php

require_once 'vendor/autoload.php';
require_once 'app/routes.php';

use app\routes\Routes;

// Registrar as rotas
Routes::registerRoutes();

// Iniciar o roteador
Pecee\SimpleRouter\SimpleRouter::start();

