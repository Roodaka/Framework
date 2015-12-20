<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 'mysql' => array(
  'table' => '',
  'field_hash' => '',
  'field_user' => '',
  'field_time' => '',
  'field_cookies' => ''
  ),

 'duration' => 604800, // one week.
 'algorithm' => 'sha256',

 'user_object' => null,
 'user_fields' => null,

 'cookie_life' => 300, // cinco minutos
 'cookie_name' => 'rdk_framework',
 'cookie_path' => DS,
 'cookie_domain' => $_SERVER['SERVER_NAME']
 );