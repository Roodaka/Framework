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



class LDB
 {
  /**
   * Recurso MySQL
   * @var resource
   */
  private static $conn = null;

  /**
   * Arreglo con los datos de conexión al servidor MySQL
   * @var array
   */
  private static $data = array(
   'host' => null,
   'port' => null,
   'user' => null,
   'pass' => null,
   'name' => null,
   'prefix' => null
   );

  /**
   * Cantidad de consultas realizadas
   * @var integer
   */
  public static $count = 0;


  /**
   * Constantes utilizadas en la actualización de columnas
   */
  const ALL = '*';
  const ADD = '+';
  const REST = '-';
  const FIELDS = 0;
  const VALUES = 1;



  /**
   * Inicializador de LDB
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
    self::$data = get_config('database');
    // Conectamos a la base de datos.
    self::connect();
   }



  /**
   * Conectar al servidor MySQL
   * @return nothing
   */
  private static function connect()
   {
    self::$conn = mysqli_connect(self::$data['host'], self::$data['user'], self::$data['pass'], self::$data['name']) or self::error('', 'No se pudo conectar al servidor MySQL');
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
  public static function query($cons, $values = null, $ret = false)
   {
    $query = ($values != null) ? self::parse_vars($cons, $values) : $cons;
    if($ret == true)
     {
      $res = self::do_query($query);
      if($res !== false)
       {
        if($res === true)
         {
          $return = true;
         }
        else
         {
          $return = $res->fetch_assoc();
          $res->free();
         }
       }
      else
       {
        $return = false;
        self::error($query);
       }
     }
    else
     {
      $return = new \Framework\Query($query, self::$conn);
      ++self::$count;
     }
    return $return;
   }



  /**
   * Seleccionamos campos de una tabla
   * @param string $table Nombre de la tabla objetivo
   * @param array|string $fields
   * @param array $condition Condicionante para la selección
   * @param array|integer $limit Límite de filas
   * @return array
   */
  public static function select($table, $fields, $condition = null, $order = null, $limits = null)
   {
    $cons = 'SELECT '.(is_array($fields) ? implode(', ', $fields) : $fields).' FROM '.self::$data['name'].'.'.self::$data['prefix'].$table.' '.self::parse_where($condition).' '.self::parse_order($order).' '.self::parse_limits($limits);
    $query = self::do_query($cons);
    if(!$query || $query == false)
     {
     return false;
     }
    else
     {
      if((int) $limits > 1 || is_array($limits))
       {
        return new \Framework\Query($cons, self::$conn);
        ++self::$count;
       }
      else
       {
        return $query->fetch_assoc();
       }
     }
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
      // Tenemos una inserción de múltiples filas
      if(isset($data[self::FIELDS]) && isset($data[self::VALUES]))
       {
        $fields = implode(', ', $data[self::FIELDS]);
        $values = array();
        foreach($data[self::VALUES] as $row)
         {
          $values[] = '( '.self::parse_input($row).' )';
         }
        $values = implode(', ', $values).';';
       }
      else
       {
        $fields = implode(', ', array_keys($data));
        $values = self::parse_input($data);
       }
      $cons = 'INSERT INTO '.self::$data['name'].'.'.self::$data['prefix'].$table.' ( '.$fields.' ) VALUES ( '.$values.' )';
      $query = self::do_query($cons);
      // Seteamos el resultado,
      return (!$query || $query == false) ? self::error($cons) : self::$conn->insert_id;
     }
    else { return false; }
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
      $cons = 'DELETE FROM '.self::$data['name'].'.'.self::$data['prefix'].$table.' '.self::parse_where($cond);
      $query = self::do_query($cons);
      return (!$query || $query == false) ? self::error($cons) : self::$conn->affected_rows;
     } else { return false; }
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
      $cons = 'UPDATE '.self::$data['name'].'.'.self::$data['prefix'].$table.' SET '.implode(', ', $fields).' '.self::parse_where($cond);
      $query = self::do_query($cons);
      return (!$query || $query == false) ? self::error($cons) : self::$conn->affected_rows;
     } else { return false; }
   }



  /**
   * Ejecutamos una consulta
   * @param string $query Cosulta SQL
   * @return resource
   */
  private static function do_query($query)
   {
    ++self::$count;
    return mysqli_query(self::$conn, $query);
   }



  /**
   * Retornamos un error grave del servidor
   * @param string $query Consulta que origina el error
   * @return nothing
   */
  private static function error($query, $error = null)
   {
    throw new LDB_Exception((($query !== '') ?'<span>Error en la consulta <i>'.$query.'</i></br>' : '').'</p><p><b>Error MySQL</b>: '.(($error !== null) ? $error : mysqli_error(self::$conn)).'</p>');
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
    return ($order !== null) ? $order : '';
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
      throw new LDB_Exception('No coinciden la cantidad de parametros necesarios con los provistos en '.$q);
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
    if(is_bool($input))
     {
      return (int) $input;
     }
    elseif($input === null OR empty($input) === true)
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
class Query
 {
  /**
   * Recurso MySQL
   * @var resource
   */
  private $data = false;

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
  public function __construct($raw_query, $connection)
   {
    $query = mysqli_query($connection, $raw_query);
    if(is_object($query))
     {
      $this->data = $query;
      $this->position = 0;
      $this->rows = $this->data->num_rows;
      return true;
     }
    else
     {
      throw new LDB_Exception((($query !== '') ? '<span>Error en la consulta <i>'.$query.'</i></br>' : '').'</p>');
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
    return (is_resource($this->data)) ? $this->data->free() : true;
   }



  /**
   * Devolvemos el array con los datos de la consulta
   * @param string $field Campo objetivo.
   * @param string $default Valor a retornar si el campo no existe o está vacío.
   * @return array|string Todos los campos o sólo uno
   */
  public function fetch($field = null, $default = null)
   {
    $this->result = $this->data->fetch_assoc();
    if($field !== null) // Pedimos un campo en especial
     {
      return (isset($this->result[$field])) ? $this->result[$field] : $default;
     }
    else
     {
      return $this->result;
     }
   }

 }



/**
 * Excepción exclusiva de LittleDB.
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class LDB_Exception Extends \Exception { }