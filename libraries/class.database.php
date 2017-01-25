<?php
/**
 * Clase de Abstracción de bases de datos sencilla y de fácil implementación
 * @package class.littledb.php
 * @author Cody Roodaka <roodakazo@hotmail.com>
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version  $Revision: 0.2.9
 * @access public
 * @see https://github.com/roodaka/littledb
 */

namespace Framework;



defined('ROOT') or exit('No tienes Permitido el acceso.');



class Database
 {
  /**
   * Recurso MySQL
   * @var resource
   */
  private static $conn = null;

  /**
   * Cantidad de consultas realizadas
   * @var integer
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
   * @param string $host Url o DNS del Servidor MySQL
   * @param string $user Usuario del servidor
   * @param string &pass Contraseña del servidor
   * @param string $db Nombre de la base de datos
   * @param mixed $logger Función para el registro de datos
   * @param mixed $errors Función para el manejo de errores
   * @return nothing
   */
  public static function init()
   {
    $data = get_config('database');
    // Conectamos a la base de datos.
    self::$conn = mysqli_connect($data['host'], $data['user'], $data['pass'], $data['name']) or self::error('', 'No se pudo conectar al servidor MySQL');
   }



  /**
   * Desconectar del servidor MySQL
   * @return boolean
   */
  private static function disconect()
   {
    return (self::$conn !== null) ? mysqli_close(self::$conn) : true;
   }



  /**
   * Procesar una consulta compleja en la base de datos
   * @param string $cons Consulta SQL
   * @param miexed $values Arreglo con los valores a reemplazar por Parse_vars
   * @param boolean $ret retornar Array de datos o no.
   * @return mixed
   */
  public static function query($raw_query, $values = null, $auto_fetch = false)
   {
    $query = self::do_query(($values != null) ? self::parse_vars($raw_query, $values) : $raw_query);
    if(is_bool($query) === true)
     {
      return $query;
     }
    elseif(is_object($query) === true)
     {
      $query = new \Framework\Database_Result($query);
      return ($auto_fetch === true) ? $query->fetch() : $query;
     }
    else
     {
      return self::error();
     }
   }



  /**
   * Seleccionamos campos de una tabla
   * @param string $table Nombre de la tabla objetivo
   * @param array|string $fields
   * @param array $condition Condicionante para la selección
   * @param array|integer $order Ordenado
   * @param array|integer $limit Límite de filas
   * @return array
   */
  public static function select($table, $fields, $condition = null, $order = null, $limits = null)
   {
    $cons = 'SELECT '.(is_array($fields) ? implode(', ', $fields) : $fields).' FROM '.$table.' '.self::parse_where($condition).' '.self::parse_order($order).' '.self::parse_limits($limits);
    $query = self::do_query($cons);
    if($query !== false)
     {
      return new \Framework\Database_Result($query);
     }
    return false; 
   }



  /**
   * Insertar Datos en una tabla
   * @param string $table Nombre de la tabla
   * @param array $data Arreglo asosiativo con los datos
   * @return integer|boolean Número de filas afectadas o False.
   */
  public static function insert($table, $data)
   {
    if(is_array($data) === true)
     {
      $cons = 'INSERT INTO '.$table.' ( '.implode(', ', array_keys($data)).' ) VALUES ( '.self::parse_input($data).' )';
      $query = self::do_query($cons);
      // Seteamos el resultado,
      return ($query !== false) ? self::$conn->insert_id : self::error();
     }
    return false;
   }




  /**
   * Borrar una fila
   * @param string $table nombre de la tabla
   * @param array $cond Condicionantes
   * @return integer|boolean Número de filas afectadas o False.
   */
  public static function delete($table, $cond)
   {
    if(is_array($cond) === true)
     {
      $cons = 'DELETE FROM '.$table.' '.self::parse_where($cond);
      $query = self::do_query($cons);
      return ($query !== false) ? self::$conn->affected_rows : self::error($cons);
     }
    return false;
   }



  /**
   * Actualizar una fila
   * @param string $table nombre de la tabla
   * @param array $array Arreglo asosiativo con los datos
   * @param array $cond Condicionantes
   * @return integer|boolean Número de filas afectadas o False.
   */
  public static function update($table, $array, $cond)
   {
    if(is_array($cond) === true)
     {
      $fields = array();
      foreach($array as $field => $value)
       {
        $fields[] = $field.' = '.((is_array($value)) ? $field.' '.$value[0].' '.(int) $value[1]: self::parse_input($value));
       }
      $cons = 'UPDATE '.$table.' SET '.implode(', ', $fields).' '.self::parse_where($cond);
      $query = self::do_query($cons);
      return ($query !== false) ?self::$conn->affected_rows : self::error($cons);
     }
    return false;
   }



  /**
   * Ejecutamos una consulta
   * @param string $query Cosulta SQL
   * @return resource
   */
  private static function do_query($query)
   {
    ++self::$count;
    self::$last_query = $query;
    return mysqli_query(self::$conn, $query);
   }



  /**
   * Retornamos un error grave del servidor
   * @param string $query Consulta que origina el error
   * @return nothing
   */
  private static function error($error = null)
   {
    throw new Database_Exception(((self::$last_query !== '') ?'<span>Error en la consulta <i>'.self::$last_query.'</i></br>' : '').'</p><p><b>Error MySQL</b>: '.(($error !== null) ? $error : mysqli_error(self::$conn)).'</p>');
   }



  /**
   * Preparamos un condicionante
   * @param array $conditions Arreglo de Condiciones
   * @return string Condiciones ya preparadas
   */
  private static function parse_where($conditions)
   {
    if(is_array($conditions))
     {
      $array = array();
      foreach($conditions as $field => $value)
       {
        if(is_array($value))
         {
          $other_values = array();
          foreach($value as $other_value)
           {
            $other_values[] = $field.' = '.self::parse_input($other_value);
           }
          $array[] = '('.implode(' OR ', $other_values).')';
         }
        else
         {
          $array[] = $field.' = '.self::parse_input($value);
         }
       }
      return 'WHERE '.implode(' AND ', $array);
     }
    elseif(!empty($conditions))
     {
      return 'WHERE '.$conditions;
     }
    else 
     {
      return '';
     }
   }



  /**
   * Parseo de Límites de consulta.
   * @param integer|array $limits Número de filas o arreglo de paginado.
   */
  private static function parse_limits($limits = null)
   {
    $parsed = '';
    if($limits !== null)
     {
      if(!is_array($limits))
       {
        $parsed = 'LIMIT 0, '.$limits;
       }
      else
       {
        $parsed = 'LIMIT '.$limits[0].', '.$limits[1];
       }
     }
    return $parsed;
   }



  /**
   * Parseo de ordenado de una consulta.
   * @param integer|array $order Orden indicado.
   */
  private static function parse_order($order = null)
   {
    return ($order !== null) ? 'ORDER BY '.$order : '';
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
  private static function parse_vars($q, $params)
   {
    // Si no es un arreglo lo convertimos
    if(!is_array($params)) { $params = array($params); }
    //Validamos que tengamos igual numero de parametros que de los necesarios.
    if(count($params) != preg_match_all("/\?/", $q, $aux))
     {
      throw new Database_Exception('No coinciden la cantidad de parametros necesarios con los provistos en '.$q);
      return $q;
     }
    //Reemplazamos las etiquetas.
    foreach($params as $param)
     {
      $q = preg_replace("/\?/", self::parse_input($param), $q, 1);
     }
    return $q;
   }


  /**
   * Función que se encarga de determinar el tipo de datos para ver si debe
   * aplicar la prevención de inyecciones SQL, si debe usar comillas o si es
   * un literal ( funcion SQL ).
   * @param mixed $input Objeto a analizar.
   * @return string Cadena segura.
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
  */
  private static function parse_input($input)
   {
    if(is_bool($input) or $input === null OR empty($input) === true)
     {
      return (int) $input;
     }
    elseif(is_null($input))
     {
      return 'NULL';
     }
    elseif(is_array($input))
     {
      return implode(', ', array_map(array('self', 'parse_input'), $input));
     } 
    else
     {
      return '\''.mysqli_real_escape_string(self::$conn, $input).'\'';
     }
   }
 }



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
   * @var resource
   */
  private $resource = false;

  /**
   * Resultado de la consulta
   * @var array
   */
  private $result = array();

  /**
   * Posición
   * @var integer
   */
  private $position = 0;

  /**
   * Nro de filas
   * @var integer
   */
  public $rows = 0;



  /**
   * Inicializar los datos
   * @param string $query Consulta SQL
   * @param string $eh Nombre de la función que manipula los errores
   * @param resource $conn Recurso de conección SQL
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public function __construct($resource)
   {
    if(is_object($resource) === true)
     {
      $this->resource = $resource;
      $this->position = 0;
      $this->rows = $this->resource->num_rows;
      return $this;
     }
    else
     {
      return false;
     }
   }



  /**
   * Cuando destruimos el objeto limpiamos la consulta.
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
   * @return boolean
   */
  public function __destruct()
   {
    return $this->free();
   }



  /**
   * Limpiamos la consulta
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
   * @return boolean
   */
  private function free()
   {
    return (is_object($this->resource)) ? $this->resource->free() : true;
   }



  /**
   * Devolvemos el array con los datos de la consulta
   * @param string $field Campo objetivo.
   * @param string $default Valor a retornar si el campo no existe o está vacío.
   * @return array|string Todos los campos o sólo uno
   */
  public function fetch($field = null, $default = false)
   {
    $this->result[$this->position] = $this->resource->fetch_assoc();
    
    if($field !== null) // Pedimos un campo en especial
     {
      $result = (isset($this->result[$this->position][$field])) ? $this->result[$this->position][$field] : $default;
     }
    else
     {
      $result = $this->result[$this->position];
     }
    ++$this->position;
    return $result;
   }

 }



/**
 * Excepción exclusiva de LittleDB.
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class Database_Exception Extends \Exception { }