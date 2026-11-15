<?php

use App\Controllers\SetupController;

$router->get('/migrate', [SetupController::class, 'migrate']);
