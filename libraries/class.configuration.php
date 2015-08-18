<?php
/**
 * Manejo de configuraciones del sistema, tanto del framework como del sitio
 * @package class.configuration.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

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
  public function init()
   {
    self::$configuration = require_once(CONFIGURATIONS_DIR.'configuration'.EXT);
   }



  /**
   * Obtenemos un arreglo de configuraciones
   * @param string $name Nombre de la configuración a cargar
   * @param string $field Campo específico a cargar de esa configuración.
   * @return boolean
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public static function get($name, $field = null)
   {
    if(array_key_exists($name, self::$variables) === true)
     {
      if(!empty($field))
       {
        if(array_key_exists($field, self::$variables[$name]) === true)
         {
          return self::$variables[$name][$field];
         }
        else
         {
          throw new Configuration_Exception('Se intenta cargar un campo de configuraci&oacute;n inv&aacute;lido ('.$name.'['.$field.']).');
         } 
       }
     }
    elseif(is_file(CONFIGURATIONS_DIR.$name.EXT) === true)
     {
      self::$variables[$name] = require_once(CONFIGURATIONS_DIR.strtolower($name).EXT);
     }
    else
     {
      throw new Configuration_Exception('Se intenta cargar un conjunto de configuraciones inexistente ('.$name.').');
     }
    return self::$variables[$name];
   } // public static function get();
 } // final class Configuration();


/**
 * Excepción exclusiva del componente Configuration
 * @access private
 */
class Configuration_Exception Extends \Exception { } // class Context_Exception();