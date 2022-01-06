<?php

// global
const DEVELOPER_MODE = true;
const ENABLE_SEO = false;
const MAX_REDIRECTIONS = 3;
const PROJECT_NAME = 'rdk_framework';

// cache
const CACHE_CONFIG = array(
    'handler' => 'file', // none, file [(not implemented) => apc, memcached, xcache]
    'path' => APP_PATH . 'cached/data/', // for file
    'expiration' => 900, // in seconds
);

// site config in db
const DYNAMIC_CONFIGURATION = array(
    'site' => array(
        'table' => 'config',
        'key_field' => 'clave',
        'value_field' => 'valor',
        'where' => null, // puede ser array('site_id' => 7),
    ),
);

// session
const SESSION_CONFIG = array(
    'duration' => 604800, // one week.
    'algorithm' => 'sha256',
    'use_cookies' => true,
    'handler' => 'mysql',
);

// database
const DATABASE_CONFIG = array(
    // Dominio del servidor
    'host' => 'localhost',
    // Puerto del servidor
    'port' => null,
    // Usuario
    'user' => 'root',
    // Contraseña
    'pass' => '',
    // Nombre de la Base de Datos
    'name' => ''
);