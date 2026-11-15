<?php
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/errors.log');
error_reporting(E_ALL);

use App\Core\Router;


require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();
require_once dirname(__DIR__) . '/routes/web.php';
$router->dispatch();
