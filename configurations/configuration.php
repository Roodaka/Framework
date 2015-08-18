<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 'from_db' => array(
  'site' => array(
   'table' => 'config',
   'key_field' => 'clave',
   'value_field' => 'valor',
   'where' => null, // puede ser array('site_id' => 7),
   ),
  )
 );