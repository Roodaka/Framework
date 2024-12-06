<?php

namespace Framework;

use mysqli_result;

/**
 * Clase para manipular resultados de consultas MySQL, esta clase no es
 * comunmente accesible y es creada por LittleDB
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class Database_Result
{
    /**
     * Recurso MySQL
     * @var mysqli_result
     */
    private mysqli_result $resource;

    /**
     * Resultado de la consulta
     * @var array
     */
    private $result = array();

    /**
     * Posición
     * @var int
     */
    private $position = 0;

    /**
     * Nro de filas
     * @var int
     */
    public $rows = 0;



    /**
     * Inicializar los datos
     * @param mysqli_result $conn Recurso de conexión SQL
     * @author Cody Roodaka <roodakazo@gmail.com>
     */
    public function __construct(mysqli_result $resource)
    {
        if (is_object($resource) === true) {
            $this->resource = $resource;
            $this->position = 0;
            $this->rows = $this->resource->num_rows;
        }
    }



    /**
     * Cuando destruimos el objeto limpiamos la consulta.
     * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    public function __destruct()
    {
        $this->free();
    }



    /**
     * Limpiamos la consulta
     * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     * @return bool
     */
    private function free(): bool
    {
        if (is_object($this->resource)) {
            $this->resource->free();
        }

        return true;
    }



    /**
     * Devolvemos el array con los datos de la consulta
     * @param string $field Campo objetivo.
     * @param string $default Valor a retornar si el campo no existe o está vacío.
     * @return array|string Todos los campos o sólo uno
     */
    public function to_array($field = '', string $default = ''): array|string
    {
        $result = [];

        if ($this->position < $this->rows) {
            $this->result[$this->position] = $this->resource->fetch_assoc();

            if (!empty($field)) // Pedimos un campo en especial
            {
                $result = (isset($this->result[$this->position][$field])) ? $this->result[$this->position][$field] : $default;
            } else {
                $result = $this->result[$this->position];
            }
            ++$this->position;

        }
        return $result;
    }
}
