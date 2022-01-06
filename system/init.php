<?php

// Mostramos los errores sólo si el modo desarrollo está activo.
if (DEVELOPER_MODE === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
} else {
    error_reporting(0);
    ini_set('display_errors', false);
}

// Cargamos las funciones básicas del núcleo
require(SYSTEM_PATH . 'functions/core.php');

/*
 * Asignamos la función del framework para manejar las exepciones
 */
set_exception_handler('exception_handler');

require_once(SYSTEM_PATH . 'libraries/class.database.php');
require_once(SYSTEM_PATH . 'libraries/class.cache.php');
require_once(SYSTEM_PATH . 'libraries/class.controller.php');
require_once(SYSTEM_PATH . 'libraries/class.factory.php');
require_once(SYSTEM_PATH . 'libraries/class.model.php');
require_once(SYSTEM_PATH . 'libraries/class.session.php');
require_once(SYSTEM_PATH . 'libraries/class.view.php');
require_once(SYSTEM_PATH . 'libraries/class.core.php');

/* Autoload for vendor */
require_once ROOT_PATH . 'vendor/autoload.php';

\Framework\Database::init();
\Framework\Cache::init();
\Framework\Session::init();
\Framework\Core::init();
