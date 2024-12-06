<?php

namespace Framework\Cache;

abstract class Base
{
    /**
     * Otener una variable cacheada (si existe)
     * @param string $name Nombre de la variable.
     * @return array|boolean
     */
    abstract public function get(string $name): mixed;
    /**
     * Cachear nuevos datos.
     * @param string $name Nombre de la variable
     * @param array $data Datos asignar
     * @param int $expires Vida de este cache
     * @return boolean Resultado de la operación.
     */
    abstract public function set(string $name, mixed $data, int $lifetime): bool;
    /**
     * Obtener el tamaño total del cache actual
     * @return int Tamaño en Bytes
     */
    abstract public function size(): int;
    /**
     * Solicitar limpieza del Cache.
     * @param string $name Opcional, limitar el borrado a una variable.
     * @return boolean
     */
    abstract public function clear(string $name): bool;
}
