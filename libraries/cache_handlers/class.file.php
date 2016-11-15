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

class File Extends \Framework\Cache\Base
 {
  /**
   * Constructor de la clase.
   * @return void
   */
  public function __construct($configuration)
   {
    $this->configuration = $configuration;

    if(is_writable($this->configuration['cache_dir']) !== true)
     {
      throw new \Framework\Cache_Exception('El cache basado en archivos no pudo inicializarse por problemas de escritura.');
     }
   }



  public function get($name, $return = true)
   {
    // Incluimos y retornamos el archivo
    if(is_file($this->configuration['cache_dir'].$name.EXT))
     {
      $data = unserialize(require($this->configuration['cache_dir'].$name.EXT));
      // Si todavía le queda 'vida' lo cargamos
      if($data['time'] > (time() - $data['lifetime']))
       {
        return $return === true ? $data['data'] : true;
       }
      else
       {
        unlink($this->configuration['cache_dir'].$name.EXT);
        return false;
       }
     }
    else
     {
      return false;
     }
   }



  public function set($name, $data, $lifetime)
   {
    // Ubicación del archivo
    $target = $this->configuration['cache_dir'].$name.EXT;

    $cache = array(
     'data' => $data,
     'lifetime' => (int) $lifetime,
     'time' => time());

    // Abrimos el archivo
    $file = fopen($target, 'w');

    if(!$file)
     {
      throw new Cache_Exception('No se pudo trabajar con el archivo "'.$target.'"');
     }
    elseif(!fwrite($file, '<?php defined(\'ROOT\') or exit(\'No tienes Permitido el acceso.\'); return \''.serialize($cache).'\';'))
     {
      throw new Cache_Exception('No se pudo escribir el archivo "'.$target.'"');
     }
    else
     {
      fclose($file);
      return true;
     }
   }



  public function size()
   {
    $size = 0;
    $dir = opendir($this->configuration['cache_dir']);
    if(!$dir)
     {
      throw new Cache_Exception('No se pudo abrir el directorio '.$this->configuration['cache_dir'].'.');
     }
    else
     {
      while(($file = readdir($dir)) !== false)
       {
        if($file !== '.' AND $file !== '..')
         {
          $size += filesize($this->configuration['cache_dir'].$file);
         }
       }
      closedir($dir);
      return $size;
     }
    return 0;
   }



  public function clear()
   {
    $dir = opendir($this->configuration['cache_dir']);
    if(!$dir)
     {
      throw new Cache_Exception('No se pudo abrir el directorio '.$this->configuration['cache_dir'].'.');
     }
    else
     {
      while(($file = readdir($dir)) !== false)
       {
        if($file !== '.' || $file !== '..')
         {
          unlink($this->configuration['cache_dir'].$file);
         }
       }
      closedir($dir);
     }
   }
 }