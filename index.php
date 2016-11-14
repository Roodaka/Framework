<?php

define('DEVELOPER_MODE', true);

// Mostramos los errores sólo si el modo desarrollo está activo.
if(DEVELOPER_MODE === true)
 {
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  define('SYSTEM_INIT_TIME', microtime(true));
  define('SYSTEM_INIT_RAM', memory_get_usage());
 }
else
 {
  $start = null;
  error_reporting(0);
  ini_set('display_errors', false);
 }
 
// Directorios
define('DS', DIRECTORY_SEPARATOR); // Un mero alias
define('EXT', '.php');
define('ROOT', dirname(__FILE__).DS);
define('CONFIGURATIONS_DIR', ROOT.'configurations'.DS);
define('CONTROLLERS_DIR', ROOT.'controllers'.DS);
define('FUNCTIONS_DIR', ROOT.'functions'.DS);
define('LIBRARIES_DIR', ROOT.'libraries'.DS);
define('MODELS_DIR', ROOT.'models'.DS);
define('VIEWS_DIR', ROOT.'views'.DS);
// Estados del framework
define('ENABLE_SEO', false);

// Cargamos las funciones básicas del núcleo
require(FUNCTIONS_DIR.'core'.EXT);

/*
 * Asignamos la función del framework para manejar las exepciones
 */
set_exception_handler('exception_handler');

/*
 * Cargamos los componentes base del Framework 
 */
require_once(LIBRARIES_DIR.'class.configuration'.EXT);
\Framework\Configuration::init();

require_once(LIBRARIES_DIR.'class.database'.EXT);
\Framework\Database::init();
\Framework\Configuration::load_from_db();

require_once(LIBRARIES_DIR.'class.cache'.EXT);
\Framework\Cache::init();

require_once(LIBRARIES_DIR.'class.controller'.EXT);

require_once(LIBRARIES_DIR.'class.factory'.EXT);

require_once(LIBRARIES_DIR.'class.model'.EXT);

require_once(LIBRARIES_DIR.'class.session'.EXT);
\Framework\Session::init();

require_once(LIBRARIES_DIR.'class.view'.EXT);
\Framework\View::init();

// Cargamos e iniciamos el núcleo.
require_once(LIBRARIES_DIR.'class.core'.EXT);
\Framework\Core::init();