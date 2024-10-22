<?php

require_once 'vendor/autoload.php'; // Autoload das dependências do Composer
require_once 'app/routes.php'; // Arquivo de rotas

// Inicia o roteador
Pecee\SimpleRouter\SimpleRouter::start();
