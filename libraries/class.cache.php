<?php
/**
 * Implementación de cacheo de datos.
 * Ésta clase actuará de interfaz para así poder utilizar los distintos tipos de Cache
 * sin necesidad de modificar líneas de código.
 * @package class.cache.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */
namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Cache
 {
  /**
   * Handler de cacheo en uso.
   * @var object
   */
  private static $handler = null;
  /**
   * Duración por defecto del cache.
   * @var integer
   */
  private static $lifetime = 900; // 15 mins
  /**
   * Inicializador de la clase.
   */
  public static function init($return = false, $force_type = null)
   {
    $configuration = get_config('cache');
    // Cargamos la abstracción que modela los handlers.
    require_once($configuration['handlers_directory'].'class.base'.EXT);

    if($force_type !== null)
     {
      $configuration['mode'] = $force_type;
     }

    if(is_file($configuration['handlers_directory'].'class.'.$configuration['mode'].EXT))
     {
      require($configuration['handlers_directory'].'class.'.$configuration['mode'].EXT);
      $name = '\Framework\Cache\\'.$configuration['mode'];
      $temp = new $name($configuration['handlers'][strtolower($configuration['mode'])]);
      if($return === false)
       {
        self::$handler = $temp;
        unset($temp);
       }
      else
       {
        return $temp;
       }
     }
    else
     {
      return false;
     }
   }



  /**
   * 
   * @return boolean
   */
  public static function set($name, $data, $lifetime = null)
   {
    if(self::$handler !== null)
     {
      return self::$handler->set(strtolower($name), $data, (((int)$lifetime === 0) ? self::$lifetime : $lifetime));
     }
    return false;
   }



  /**
   *
   * @return
   */
  public static function get($name)
   {
    if(self::$handler !== null)
     {
      return self::$handler->get(strtolower($name));
     }
    return false;
   }



  /**
   * Solicitar el tamaño del cache actual.
   * @return integer
   */
  public static function size()
   {
    if(self::$handler !== null)
     {
      return (int) self::$handler->size();
     }
    return 0;
   }



  /**
   * Borrar una variable cacheada, de no argumentar ninguna, se borrará todo el cache.
   * @param string $name Nombre de la variable cacheada a borrar.
   * @return boolean
   */
  public static function clear($name = '')
   {
    if(self::$handler !== null)
     {
      return (int) self::$handler->clear();
     }
    return false;
   }
 }

class Cache_Exception Extends \Exception {}