<?php

define('ROOT_PATH', realpath(__DIR__) . '/');
const APP_PATH = ROOT_PATH . 'application/';
const SYSTEM_PATH = ROOT_PATH . 'system/';

/* Autoload for vendor */
require_once ROOT_PATH . 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// Cargamos las funciones básicas del núcleo
require(SYSTEM_PATH . 'functions/core.php');

set_exception_handler('exception_handler');

require_once(SYSTEM_PATH . 'libraries/cache/class.cache.php');
require_once(SYSTEM_PATH . 'libraries/router/class.controller.php');

require_once(SYSTEM_PATH . 'libraries/session/class.session.php');
require_once(SYSTEM_PATH . 'libraries/router/class.router.php');

if ($_ENV['DB_DRIVER'] !== 'none') {
    require_once(SYSTEM_PATH . 'libraries/database/class.model.php');
    require_once(SYSTEM_PATH . 'libraries/database/class.factory.php');
    require_once(SYSTEM_PATH . 'libraries/database/class.database.php');
    \Framework\Database::init();
}

if ($_ENV['REST_ONLY'] !== 'true') {
    require_once(SYSTEM_PATH . 'libraries/view/class.view.php');
    \Framework\View::init();
}

if ($_ENV['PHP_ENV'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', false);
}

\Framework\Cache::init();
\Framework\Session::init();
\Framework\Router::init();
