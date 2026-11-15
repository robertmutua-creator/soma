<?php

use App\Controllers\SetupController;

// $router->get('/migrate', [SetupController::class, 'migrate']);
$router->get('/seed', [SetupController::class, 'admin']);
