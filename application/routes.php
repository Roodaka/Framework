<?php
return array(
    'default_route' => 'home',
    'error_route' => 'error',
    'routes' => array(
        'home' => array(
            'main' => false,
            'edit' => false,
            'create' => false,
            'clear' => false,
            'delete' => false
        ),
        'error' => array('render' => false),
    ),
);
