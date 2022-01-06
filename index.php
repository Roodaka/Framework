<?php

define('ROOT_PATH', realpath(__DIR__) . '/');
const APP_PATH = ROOT_PATH . 'application/';
const SYSTEM_PATH = ROOT_PATH . 'system/';

/* Autoload for vendor */
require_once ROOT_PATH . 'vendor/autoload.php';

// Cargamos las funciones básicas del núcleo
require(SYSTEM_PATH . 'functions/core.php');

set_exception_handler('exception_handler');

require_once(SYSTEM_PATH . 'libraries/class.cache.php');
require_once(SYSTEM_PATH . 'libraries/class.controller.php');
require_once(SYSTEM_PATH . 'libraries/class.model.php');
require_once(SYSTEM_PATH . 'libraries/class.factory.php');
require_once(SYSTEM_PATH . 'libraries/class.database.php');
require_once(SYSTEM_PATH . 'libraries/class.session.php');
require_once(SYSTEM_PATH . 'libraries/class.view.php');
require_once(SYSTEM_PATH . 'libraries/class.core.php');

$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

if ($_ENV['PHP_ENV'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
} else {
    error_reporting(0);
    ini_set('display_errors', false);
}

\Framework\Cache::init();
\Framework\Database::init();
\Framework\Session::init();
\Framework\View::init();
\Framework\Core::init();
