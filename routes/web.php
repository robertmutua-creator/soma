<?php

use App\Controllers\Admin\SchoolAdminsController;
use App\Controllers\Admin\SchoolController;
use App\Controllers\AuthController;
use App\Controllers\School\ClassesController;
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
$router->get('/admin/schools', [SchoolController::class, 'index']);
$router->post('/admin/schools/save', [SchoolController::class, 'save']);
$router->post('/admin/schools/delete', [SchoolController::class, 'delete']);

// School Admins Management
$router->get('/admin/admins', [SchoolAdminsController::class, 'index']);
$router->post('/admin/admins/save', [SchoolAdminsController::class, 'save']);
$router->post('/admin/admins/delete', [SchoolAdminsController::class, 'delete']);


// Schools
// Classes Management
$router->get('/schools/classes', [ClassesController::class, 'index']);
$router->post('/schools/classes/save', [ClassesController::class, 'save']);
$router->post('/schools/classes/delete', [ClassesController::class, 'delete']);
