<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 // Modo de trabajo del cache.
 'mode' => 'file',
 // Directorio donde se encuentran los distintos handlers de cache
 'handlers_directory' => CORE_DIR.'cache_handlers'.DS,
 // Duración por defecto del cache (en segundos).
 'lifetime' => 900,
 // Subarreglo de configuraciones
 'handlers' => array(
  'apc' => array(),
  'file' => array('cache_dir' => DATA_DIR.'cached'.DS.'data'.DS),
  'memcached' => array(), // sin implementar
  'xcache' => array(),// sin implementar
  )
 );