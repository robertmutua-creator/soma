<?php

use App\Controllers\AuthController;
use App\Controllers\SetupController;

// $router->get('/migrate', [SetupController::class, 'migrate']);
// $router->get('/seed', [SetupController::class, 'admin']);

// Login Route
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginStore']);
