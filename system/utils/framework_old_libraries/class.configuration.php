<?php

/**
 * Manejo de configuraciones del sistema, tanto del framework como del sitio
 * @package class.configuration.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

final class Configuration
{
    /**
     * Arreglo de configuración
     * @var array
     */
    private static $configuration = array();

    /**
     * Arreglo de configuración
     * @var array
     */
    public static $variables = array();

    /**
     * Inicializamos el sistema de carga de configuraciones.
     * Éste cargará la lista de configuraciones obtenibles
     * desde la base de datos como la configuración del sitio.
     */
    public static function init()
    {
        self::$configuration = require_once(APP_PATH . 'configurations/configuration.php');
    }


    /*
    public static function load_from_db()
    {
        if (!empty(self::$configuration['from_db'])) {
            foreach (self::$configuration['from_db'] as $variable => $data) {
                $select = \Framework\Database::select($data['table'], array($data['key_field'], $data['value_field']), $data['where'], null, 50);
                if (!empty($select)) {
                    while ($row = $select->fetch()) {
                        self::$variables[$variable][$row[$data['key_field']]] = $row[$data['value_field']];
                    }
                }
            }
        }
    }
    */


    /**
     * Obtenemos un arreglo de configuraciones
     * @param string $name Nombre de la configuración a cargar
     * @param string $field Campo específico a cargar de esa configuración.
     * @return boolean
     * @author Cody Roodaka <roodakazo@gmail.com>
     */
    public static function get($name, $field = null)
    {
        if (array_key_exists($name, self::$variables) === true) {
            if (!empty($field)) {
                if (array_key_exists($field, self::$variables[$name]) === true) {
                    return self::$variables[$name][$field];
                } else {
                    throw new Configuration_Exception('Se intenta cargar un campo de configuraci&oacute;n inv&aacute;lido (' . $name . '[' . $field . ']).');
                }
            }
        } elseif (is_file(APP_PATH . 'configurations/' . $name . '.php') === true) {
            self::$variables[$name] = require_once(APP_PATH . 'configurations/' . strtolower($name) . '.php');
        } else {
            throw new Configuration_Exception('Se intenta cargar un conjunto de configuraciones inexistente (' . $name . ').');
        }
        return self::$variables[$name];
    } // public static function get();
} // final class Configuration();


/**
 * Excepción exclusiva del componente Configuration
 * @access private
 */
class Configuration_Exception extends \Framework\Standard_Exception
{
} // class Context_Exception();