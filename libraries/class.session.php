<?php
/**
 * Control de sesiones y cookies
 * @package class.session.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class Session
 {
  /**
   * Configuración del componente
   * @var Array
   */
  protected static $configuration = array();

  /**
   * Hash identificador de la sesión
   * @var string
   */
  private static $hash = null;

  /**
   * Objeto de usuario a gestionar
   * var Object
   */
  public static $user = null;

  /**
   * Iniciamos la sesión
   * @return nothing
   */
  public static function init()
   {
    // Configuramos...
    self::$configuration = get_config('session');
    // Obtenemos una instancia de LDB para utilizar...
    if(!isset($_SESSION) OR session_id() == '')
     {
      session_start();
     }

    $_SESSION['datetime'] = time();

    if(isset($_COOKIE[self::$configuration['cookie_name']]))
     {
      self::$hash = $_COOKIE[self::$configuration['cookie_name']];
     }

    if(isset($_SESSION['hash']))
     {
      self::$hash = $_SESSION['hash'];
     }

    if(self::$hash !== null && !empty(self::$configuration['mysql']['table']))
     {
      $query = LDB::query('SELECT '
       .self::$configuration['mysql']['field_user'].', '.self::$configuration['mysql']['field_cookies']
       .' FROM '.self::$configuration['mysql']['table']
       .' WHERE '.self::$configuration['mysql']['field_hash'].' = ? AND '.self::$configuration['mysql']['field_time'].' > ? LIMIT 0, 1'
       , array(self::$hash, (time() - self::$configuration['duration'])), true);
      if($query !== false && !empty($query))
       {
        self::set_id($query[self::$configuration['mysql']['field_user']], $query[self::$configuration['mysql']['field_cookies']]);
       }
     }
   } // public static function init();



  /**
   * Asignar un ID a la sesión.
   * @param Integer $id Identificador de usuario a setear.
   * @param Boolean $cookies Indica el uso de cookies
   * @return boolean
   */
  public static function set_id($id, $cookies = false)
   {
    if(!empty(self::$configuration['mysql']['table']))
     {
      $_SESSION['hash'] = hash(self::$configuration['algorithm'], $id);
      self::$hash = $_SESSION['hash'];

      if($cookies === true)
       {
        setcookie(self::$configuration['cookie_name'], self::$hash, (time() + self::$configuration['duration']), self::$configuration['cookie_path'], self::$configuration['cookie_domain']);
       }

      LDB::query('INSERT INTO '.self::$configuration['mysql']['table'].' ('.self::$configuration['mysql']['field_hash'].', '.self::$configuration['mysql']['field_user'].', '.self::$configuration['mysql']['field_time'].', '.self::$configuration['mysql']['field_cookies'].')
       VALUES (\''.self::$hash.'\', '.$id.', '.$_SESSION['datetime'].', '.(int) $cookies
       .') ON DUPLICATE KEY UPDATE '.self::$configuration['mysql']['field_time'].' = '.$_SESSION['datetime'].', '.self::$configuration['mysql']['field_cookies'].' = '.(int) $cookies, null, true);

      return self::set_user_object($id);
     }
    else
     {
      throw new Session_Exception('No se ha configurado la sesi&oacute;n. revise el archivo configurations/session.php');
     }
   } // public static function set_id();




   /**
    * Seteamos el modelo de usuario
    * @return nothing
    */
  private static function set_user_object($id)
   {
    if(self::$configuration['user_object'] !== null)
     {
      self::$user = Factory::create(self::$configuration['user_object'], $id, self::$configuration['user_fields'], true, true);
      return true;
     }
    else
     {
      throw new Session_Exception('No se ha asignado un modelo para el Usuario en Sesi&oacute;n.');
     }
   } // private static function set_user_object();



   /**
    * Chequeamos si la sesión es de un usuario válido
    * @return bool
    */
  public static function is_session()
   {
    return !empty(self::$user);
   } // private static function is_user_id()



  /**
   * Terminamos la sesión.
   * @return Nothing
   */
  public static function end()
   {
    self::$user = null;
    // Borramos la sesión por el lado de la base de datos.
    LDB::delete(self::$configuration['mysql']['table'], array(self::$configuration['mysql']['field_hash'] => $_SESSION['hash']), false);
    // Si existe una cookie, la destruímos
    if(isset($_COOKIE))
     {
      setcookie(self::$configuration['cookie_name'], $_SESSION['hash'], (time() - self::$configuration['duration']), self::$configuration['cookie_path'], self::$configuration['cookie_domain']);
      unset($_COOKIE);
     }
    // Destruímos la sesión por el lado del compilador.
    unset($_SESSION);
    session_regenerate_id(true);
   } // public static function end();
 } // final class Session();


/**
 * Excepción exclusiva del componente Session
 * @access private
 */
class Session_Exception Extends \Exception {} // class Session_Exception();