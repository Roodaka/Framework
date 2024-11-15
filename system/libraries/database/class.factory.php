<?php

/**
 * Implementación del patrón de diseño Factory
 * @package class.factory.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

final class Factory
{
    /**
     * Referencias de los modelos.
     * @var array
     */
    protected static $models = array();

    /**
     * Referencias de los modelos.
     * @var array
     */
    protected static $handlers = array();

    /**
     * Conteo de modelos.
     * @var integer
     */
    public static $count = 0;



    /**
     * Intentamos recrear el patrón de diseño ObjectPool con unos ligeros cambios
     * @param string $model Modelo a Cargar
     * @param integer $id Identificador
     * @param array $specified_fields Campos específicos a cargar por el modelo
     * @param bool $autoload Marcamos la autocarga de datos del modelo.
     * @param bool $protected Lo marcamos para no ser limpiado en la redirección
     * @return reference
     */
    final public static function &create(string $model, int $id = null, array $specified_fields = [], bool $autoload = true, bool $protected = false)
    {
        if ($model !== null) {
            if (class_exists($model) === false) {
                if (file_exists(APP_PATH . 'models/class.' . strtolower($model) . '.php') === true) {
                    require_once(APP_PATH . 'models/class.' . strtolower($model) . '.php');
                } else {
                    throw new Factory_Exception('No se ha podido cargar el modelo ' . $model . '.');
                }
            }

            $modelname = '\Application\Models\\' . $model;
            $modelkey = $model . (($id !== null) ? '-' . $id : '');
            if ($protected === false && $id !== null) {
                if (isset(self::$models[$modelkey]) === false) {
                    self::$models[$modelkey] = new $modelname($id, $specified_fields, $autoload);
                    if (self::$models[$modelkey] !== false) {
                        ++self::$count;
                    }
                    ++self::$count;
                }
                return self::$models[$modelkey];
            } else {
                if (isset(self::$handlers[$modelkey]) === false) {
                    self::$handlers[$modelkey] = new $modelname($id, $specified_fields, $autoload);
                    if (self::$handlers[$modelkey] !== false) {
                        ++self::$count;
                    }
                }
                return self::$handlers[$modelkey];
            }
        } else {
            throw new Factory_Exception('Se ha solicitado un nombre de Modelo nulo.');
        }
    }

    /**
     * Borramos todas las instancias
     * @return void
     */
    final public static function clear(): void
    {
        self::$models = array();
    } 

    /**
     * Procesamos un arreglo de ID's llevándolos a ser Modelos.
     * @param array $target_ids ID's Objetivo
     * @param string $object Modelo a retornar
     * @param null|array $fields Campos específicos a ser cargados por cada Modelo
     * @param boolean $autoload Autocargamos los datos
     * @param boolean $return_array Solicitamos los datos como un arreglo
     * @return array
     */
    final public static function create_from_array($model, $target_ids = array(), $fields = null, $autoload = true, $return_array = false): array
    {
        $classes = array();
        if (is_array($target_ids) === false) {
            $target_ids = array($target_ids);
        }
        foreach ($target_ids as $id) {
            $object = self::create($model, (int) $id, $fields, $autoload);
            if ($autoload === true and $return_array === true) {
                $classes[] = $object->get_array();
            } else {
                $classes[] = $object;
            }
        }

        return $classes;
    }

    /**
     * Realizamos una consulta rápida a la base de datos para obtener ID's u objetos
     * @param string $model Nombre del modelo a cargar.
     * @param null|array $condition Condicionantes para la consulta MySQL
     * @param null|array $order Valor para el ordenado de los resultados
     * @param null|array $limits Límites de otención
     * @param boolean $autoload Autocargamos los datos
     * @param boolean $return_array Solicitamos los datos como un arreglo
     * @return mixed
     */
    final public static function create_from_database($model = null, $condition = null, $order = null, $limits = null, $autoload = true, $return_array = true)
    {
        $object = self::create($model);
        $query = \Framework\Database::select($object->table, $object->primary_key, $condition, $order, $limits);
        if (!empty($query) && !is_array($query)) {
            $result = array();
            while ($row = $query->fetch()) {
                if ($autoload === true) {
                    $temp = self::create($model, $row[$object->primary_key]);
                    $result[] = ($return_array === true) ? $temp->get_array() : $temp;
                } else {
                    $result[] = $row[$object->primary_key];
                }
            }
            return $result;
        } elseif (is_array($query) === true && count($query) === 1) {
            return self::create($model, $query[$object->primary_key]);
        } else {
            return false;
        }
    }
}

/**
 * Excepción única del componente Factory
 * @access private
 */
class Factory_Exception extends \Exception { }
