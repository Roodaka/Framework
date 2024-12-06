<?php

namespace Framework;

use mysqli, mysqli_result;

class Database
{
    /**
     * Recurso MySQL
     * @var mysqli
     */
    private static ?mysqli $conn = null;

    /**
     * Cantidad de consultas realizadas
     * @var int
     */
    public static $count = 0;

    /**
     * Última consulta ejecutada
     * @var string
     */
    private static $last_query = '';


    /**
     * Constantes utilizadas en la actualización de columnas
     */
    const ALL = '*';
    const ADD = '+';
    const REST = '-';



    /**
     * Inicializador de la clase
     * @return void
     */
    public static function init(): void
    {
        if (isset($_ENV['DB_DRIVER']) === true && $_ENV['DB_DRIVER'] !== 'none') {
            self::$conn = mysqli_connect(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME']
            ) or self::error('No se pudo conectar al servidor MySQL');
        }
    }



    /**
     * Desconectar del servidor MySQL
     * @return bool
     */
    private static function disconect(): bool
    {
        return (self::$conn !== null) ? mysqli_close(self::$conn) : true;
    }



    /**
     * Procesar una consulta compleja en la base de datos
     * @param string $cons Consulta SQL
     * @param array|string $values Arreglo con los valores a reemplazar por Parse_vars
     * @param bool $ret retornar Array de datos o no.
     * @return mixed
     */
    public static function query($raw_query, $values = null): bool|Database_Result
    {
        $query = self::do_query(($values != null) ? self::parse_vars($raw_query, $values) : $raw_query);
        if (is_bool($query) === true) {
            return (bool) $query;
        } elseif (is_object($query) === true) {
            return new \Framework\Database_Result($query);
        } else {
            self::error();
            return false;
        }
    }



    /**
     * Seleccionamos campos de una tabla
     * @param string $table Nombre de la tabla objetivo
     * @param array|string $fields
     * @param array $condition Condicionante para la selección
     * @param array|int $order Ordenado
     * @param array|int $limit Límite de filas
     * @return bool|Database_Result
     */
    public static function select($table, $fields, $condition = null, $order = null, $limits = null): bool|Database_Result
    {
        $cons = 'SELECT ' . (is_array($fields) ? implode(', ', $fields) : $fields) . ' FROM ' . $table . ' ' . self::parse_where($condition) . ' ' . self::parse_order($order) . ' ' . self::parse_limits($limits);

        $query = self::do_query($cons);
        if ($query !== false) {
            return new \Framework\Database_Result($query);
        }
        return false;
    }



    /**
     * Insertar Datos en una tabla
     * @param string $table Nombre de la tabla
     * @param array $data Arreglo asosiativo con los datos
     * @return int|bool Número de filas afectadas o False.
     */
    public static function insert($table, $data): int|bool
    {
        if (is_array($data) === true) {
            $cons = 'INSERT INTO ' . $table . ' ( ' . implode(', ', array_keys($data)) . ' ) VALUES ( ' . self::parse_input($data) . ' )';
            $query = self::do_query($cons);
            // Seteamos el resultado,
            if ($query !== false) {
                return self::$conn->insert_id;
            } else {
                self::error();
            }
        }
        return false;
    }




    /**
     * Borrar una fila
     * @param string $table nombre de la tabla
     * @param array $cond Condicionantes
     * @return int Número de filas afectadas.
     */
    public static function delete($table, $cond): int
    {
        if (is_array($cond) === true) {
            $cons = 'DELETE FROM ' . $table . ' ' . self::parse_where($cond);
            $query = self::do_query($cons);
            return ($query !== false) ? (int) self::$conn->affected_rows : 0;
        }
        return 0;
    }



    /**
     * Actualizar una fila
     * @param string $table nombre de la tabla
     * @param array $array Arreglo asosiativo con los datos
     * @param array $cond Condicionantes
     * @return int Número de filas afectadas o False.
     */
    public static function update($table, $array, $cond): int
    {
        if (is_array($cond) === true) {
            $fields = array();
            foreach ($array as $field => $value) {
                $fields[] = $field . ' = ' . ((is_array($value)) ? $field . ' ' . $value[0] . ' ' . (int) $value[1] : self::parse_input($value));
            }
            $cons = 'UPDATE ' . $table . ' SET ' . implode(', ', $fields) . ' ' . self::parse_where($cond);
            $query = self::do_query($cons);
            return ($query !== false) ? self::$conn->affected_rows : 0;
        }
        return 0;
    }



    /**
     * Ejecutamos una consulta
     * @param string $query Cosulta SQL
     * @return mysqli_result
     */
    private static function do_query($query): bool|mysqli_result
    {
        ++self::$count;
        self::$last_query = $query;
        $execution = mysqli_query(self::$conn, $query);

        if (!empty(mysqli_error(self::$conn))) {
            self::error($query);
        }
        
        return $execution;
        
    }



    /**
     * Retornamos un error grave del servidor
     * @param string $query Consulta que origina el error
     * @return void
     */
    private static function error($error = null)
    {
        throw new Database_Exception(((self::$last_query !== '') ? '<span>Error en la consulta <i>' . self::$last_query . '</i></br>' : '') . '</p><p><b>Error MySQL</b>: ' . (($error !== null) ? $error : mysqli_error(self::$conn)) . '</p>');
    }



    /**
     * Preparamos un condicionante
     * @param array $conditions Arreglo de Condiciones
     * @return string Condiciones ya preparadas
     */
    private static function parse_where($conditions): string
    {
        if (is_array($conditions)) {
            $array = array();
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $other_values = array();
                    foreach ($value as $other_value) {
                        $other_values[] = $field . ' = ' . self::parse_input($other_value);
                    }
                    $array[] = '(' . implode(' OR ', $other_values) . ')';
                } else {
                    $array[] = $field . ' = ' . self::parse_input($value);
                }
            }
            return 'WHERE ' . implode(' AND ', $array);
        } elseif (!empty($conditions)) {
            return 'WHERE ' . $conditions;
        } else {
            return '';
        }
    }



    /**
     * Parseo de Límites de consulta.
     * @param int|array $limits Número de filas o arreglo de paginado.
     */
    private static function parse_limits($limits = null): string
    {
        $parsed = '';
        if ($limits !== null) {
            if (!is_array($limits)) {
                $parsed = 'LIMIT 0, ' . $limits;
            } else {
                $parsed = 'LIMIT ' . $limits[0] . ', ' . $limits[1];
            }
        }
        return $parsed;
    }



    /**
     * Parseo de ordenado de una consulta.
     * @param string|array $order Orden indicado.
     */
    private static function parse_order($order = null): string
    {
        return ($order !== null) ? 'ORDER BY ' . $order : '';
    }



    /**
     * Funcion encargada de realizar el parseo de la consulta SQL agregando las
     * variables de forma segura mediante la validacion de los datos.
     * En la consulta se reemplazan los ? por la variable en $params
     * manteniendo el orden correspondiente.
     * @param string $q Consulta SQL
     * @param array $params Arreglo con los parametros a insertar.
     * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    private static function parse_vars(string $raw_query, array $params): string 
    {
        // Si no es un arreglo lo convertimos
        if (!is_array($params)) {
            $params = array($params);
        }

        //Validamos que tengamos igual numero de parametros que de los necesarios.
        if (count($params) != preg_match_all("/\?/", $raw_query, $aux)) {
            throw new Database_Exception('No coinciden la cantidad de parametros necesarios con los provistos en ' . $raw_query);
        }

        //Reemplazamos las etiquetas.
        foreach ($params as $param) {
            $raw_query = preg_replace("/\?/", self::parse_input($param), $raw_query, 1);
        }

        return $raw_query;
    }


    /**
     * Función que se encarga de determinar el tipo de datos para ver si debe
     * aplicar la prevención de inyecciones SQL, si debe usar comillas o si es
     * un literal ( funcion SQL ).
     * @param mixed $input Objeto a analizar.
     * @return string Cadena segura.
     * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    public static function parse_input($input): int|string
    {
        if (is_bool($input) or $input === null or empty($input) === true) {
            return (int) $input;
        } elseif (is_null($input)) {
            return 'NULL';
        } elseif (is_array($input)) {
            return implode(', ', array_map(array('\Framework\Database', 'parse_input'), $input));
        } else {
            return '\'' . mysqli_real_escape_string(self::$conn, $input) . '\'';
        }
    }
}

/**
 * Excepción exclusiva de Database.
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class Database_Exception extends \Framework\Standard_Exception
{
}