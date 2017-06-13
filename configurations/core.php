<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 'default_route' => array('controller' => 'home','method' => 'main'),
 'error_route' => array('controller' => 'error','method' => 'main',),
 'max_redirections' => 3,
 );