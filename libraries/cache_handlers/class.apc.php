<?php
/**
 * Implementación del tipo de cacheo por archivos
 * @package class.file.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework\Cache;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class APC Extends \Framework\Cache\Base
 {
  /**
   * Constructor de la clase.
   * @return void
   */
  public function __construct($configuration)
   {
    $this->configuration = $configuration;

    if(extension_loaded('apc') === false OR (bool) ini_get('apc.enabled') === false)
     {
      throw new \Framework\Cache_Exception('La extensi&oacute;n APC no se encuentra activa.');
     }
   }



  public function get($name, $return = true)
   {
    $name = strtolower($name);
    $data = apc_fetch($name, $result);
    if($return === true && $result === true)
     {
      return $data;
     }
    else
     {
      return $result;
     }
   }



  public function set($name, $data, $lifetime)
   {
    // Ubicación del archivo
    return (bool) apc_store($name, $data, $lifetime);
   }



  public function size()
   {
    return apc_cache_info('', true)['mem_size'];
   }



  public function clear()
   {
    return apc_clear_cache('user') && apc_clear_cache();
   }
 }