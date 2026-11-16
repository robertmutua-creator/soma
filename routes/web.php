<?php

use App\Controllers\Admin\SchoolController;
use App\Controllers\AuthController;
use App\Controllers\SetupController;
use App\Core\Controller;

// $router->get('/migrate', [SetupController::class, 'migrate']);
// $router->get('/seed', [SetupController::class, 'admin']);

// Auth Route
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginStore']);
$router->get('/dashboard', [Controller::class, 'dashboard']);
$router->post('/logout', [AuthController::class, 'logout']);


// Schools Management
$router->get('/schools', [SchoolController::class, 'index']);
$router->post('/schools/save', [SchoolController::class, 'save']);
$router->post('/schools/delete', [SchoolController::class, 'delete']);
