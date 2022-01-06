<?php

/**
 * libraries/functions.friendly.php
 * Cody Roodaka 2011
 * Creado el 03/04/2011 01:17 a.m.
 */

/**
 * Cortar un texto si es necesario
 * @param string $text Texto a cortar
 * @param int $max Cantidad máxima de caracteres
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function resizetext($text, $max = 100)
{
    return (isset($text[$max])) ? substr($text, 0, $max) . '&hellip;' : $text;
} // function resizetext();