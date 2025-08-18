<?php

require_once __DIR__ . '/src/auth_check.php';
require_once __DIR__ . '/vendor/autoload.php';

use app\controllers\AdminController;

$controller = new AdminController();
$controller->dashboard();