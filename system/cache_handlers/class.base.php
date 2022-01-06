<?php

namespace Framework\Cache;

/**
 * Clase abstracta para estructurar los handlers del Cache.
 */
abstract class Base
{
    /**
     * Otener una variable cacheada (si existe)
     * @param string $name Nombre de la variable.
     * @param boolean $return Retornar los datos almacenados u obtener un booleano validando su existencia
     * @return array|boolean
     */
    abstract public function get($name, $return = true);
    /**
     * Cachear nuevos datos.
     * @param string $name Nombre de la variable
     * @param array $data Datos asignar
     * @param integer $expires Vida de este cache
     * @return boolean Resultado de la operación.
     */
    abstract public function set($name, $data, $lifetime);
    /**
     * Obtener el tamaño total del cache actual
     * @return integer Tamaño en Bytes
     */
    abstract public function size();
    /**
     * Solicitar limpieza del Cache.
     * @param string $name Opcional, limitar el borrado a una variable.
     * @return boolean
     */
    abstract public function clear($name = '');
}
