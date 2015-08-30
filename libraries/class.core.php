<?php
/**
 * Funcionamiento general del sistema
 * @package class.core.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class Core
 {
  /**
   * Arreglo de Controladores disponibles.
   * @var array
   */
  protected static $avaiable_controllers = array();

  /**
   * Configuración del sistema
   * @var array
   */
  public static $config = array();

  /**
   * Identificador del error.
   * @var integer
   */
  public static $error = null;

  /**
   * Ruta de ruteo en caso de errores.
   * @var array
   */
  protected static $error_routes = array(
   'controller' => 'error',
   'method' => 'main',
   'value' => null,
   'page' => 1);

  /**
   * Ruta por defecto
   * @var array
   */
  protected static $default_routing = array(
   'controller' => null,
   'method' => null,
   'value' => null,
   'page' => 1);

  /**
   * Ruta actual
   * @var array
   */
  public static $target_routing = array(
   'controller' => null,
   'method' => null,
   'value' => null,
   'page' => 1);

  /**
   * Ruta solicitada para redireccionamiento.
   * @var array
   */
  protected static $new_routing = array(
   'controller' => null,
   'method' => null,
   'value' => null,
   'page' => 1);

  // Constantes que definen los errores en la carga del controlador
  const ROUTING_ERROR_CONTEXT = 'routing_error_context';
  const ROUTING_ERROR_FILE = 'routing_error_file';
  const ROUTING_ERROR_CONTROLLER = 'routing_error_controller';
  const ROUTING_ERROR_METHOD = 'routing_error_method';

  // Para hacer más dinámico el sistema, estas constantes son quienes definen
  // las claves en el arreglo $_GET que decidirán el trayecto del mismo.
  const ROUTING_CONTROLLER_VARIABLE = 'a';
  const ROUTING_METHOD_VARIABLE = 'f';
  const ROUTING_VALUE_VARIABLE = 'v';
  const ROUTING_PAGENUMBER_VARIABLE = 'p';



   /**
    * Iniciamos el Núcleo del sistema
    * @param array $initial_data Arreglo con los datos (tiempo y RAM) iniciales
    * @return nothing
    */
  public static function init()
   {
    self::load_components();

    self::$config = get_config('core');

    self::$avaiable_controllers = get_config('routes');

    self::$default_routing = array('controller' => self::$config['default_controller'], 'method' => self::$config['default_method']);

    // Cargamos configuraciones del sitio y las preferencias del usuario
    //TODO: Crear el modelo de preferencias de usuario
    self::route();
   } // public static function init();



   /**
    * Cargador dinámico de controladores
    * @return nothing
    */
  private static function route()
   {
    // Controlador
    if(isset($_GET[self::ROUTING_CONTROLLER_VARIABLE]))
     {
      self::$target_routing['controller'] = $_GET[self::ROUTING_CONTROLLER_VARIABLE];
     } else { self::$target_routing['controller'] = self::$default_routing['controller']; }

    // Método
    if(isset($_GET[self::ROUTING_METHOD_VARIABLE]))
     {
      self::$target_routing['method'] = $_GET[self::ROUTING_METHOD_VARIABLE];
     } else { self::$target_routing['method'] = self::$default_routing['method']; }

    // Índice
    if(isset($_GET[self::ROUTING_VALUE_VARIABLE]))
     {
      self::$target_routing['value'] = $_GET[self::ROUTING_VALUE_VARIABLE];
     }

    // Página
    if(isset($_GET[self::ROUTING_PAGENUMBER_VARIABLE]))
     {
      self::$target_routing['page'] = (int) $_GET[self::ROUTING_PAGENUMBER_VARIABLE];
     }

    if(self::is_valid_route(self::$target_routing['controller'], self::$target_routing['method']) === false)
     {
      // El problema es sólo el método.
      if(self::$error === self::ROUTING_ERROR_METHOD)
       {
        // redireccionamos al controlador que tenía ese método
        self::$target_routing['method'] = self::$default_routing['method'];
       }
      else
       {
        self::$target_routing = self::$error_routes;
       }
     }

    self::call_controller();

    // Renderizado final.
    View::show();
   } // protected static function route();



  /**
   * Cargamos el controlador objetivo del router.
   * @return nothing
   */
  private static function call_controller($controller = null, $method = null, $value = null, $page = 1, $ignore_post = false)
   {
    // En caso de error, llamamos al controlador correspondiente
    if(self::$error !== null)
     {
      self::$target_routing = self::$error_routes;
     }
    elseif($controller !== null)
     {
      $method = $method !== null ? $method : self::$default_routing['method'];
      self::$target_routing = array('controller' => $controller, 'method' => $method, 'value' => $value, 'page' => $page);
     }

    // DESPUÉS DE TODO ESTO, CARGAMOS EL CONTROLADOR!!!
    require_once(CONTROLLERS_DIR.'class.'.strtolower(self::$target_routing['controller']).EXT);

    // Hacemos la última validación.
    $class = '\Framework\Controllers\\'.self::$target_routing['controller'];
    $controller = new $class($ignore_post);
    if(get_parent_class($controller) === 'Framework\Controller')
     {
      call_user_func_array(array($controller, self::$target_routing['method']), array());
     }
    else
     {
      throw new Core_Exception('El controlador cargado ('.self::$target_routing['controller'].') es inv&aacute;lido.', self::$error);
     }

    // Esta porción de código sólo es llamada cuando un controlador pide, desde
    // sí mismo, una redirección, lo cual reiniciará las vistas y el controlador
    // que había sido cargado previamente
    if(self::$new_routing['controller'] !== null AND self::$new_routing['method'] !== null)
     {
      // Removemos el controlador anterior.
      unset($controller);

      // Redireccionamos a la nueva ruta.
      self::$target_routing = self::$new_routing;

      // anulamos la redirección
      self::$new_routing = array('controller' => null, 'method' => null, 'value' => null, 'page' => 1);

      // Reiniciamos las vistas.
      View::clear();

      // Reiniciamos los Modelos.
      Factory::clear();

      // Llamamos a esta misma función para continuar el proceso.
      self::call_controller(self::$target_routing['controller'], self::$target_routing['method'], self::$target_routing['value'], self::$target_routing['page'], true);
     }
   } // private static function call_controller();



  /**
   * Solicitar un cambio de controlador
   * @param string $controller Controlador objetivo
   * @param string $method Método objetivo
   * @param string|integer $value ID solicitado
   * @param integer $page Nro de página
   * @param boolean $http_redirection Solicitar una redirección HTTP o no
   * @return nothing
   */
  public static function redirect($controller, $method = null, $value = null, $page = 1, $http_redirection = true)
   {
    if(self::is_valid_route($controller, $method) === true)
     {
      $new_route = array(
       'controller' => $controller,
       'method' => (($method !== null) ? $method : self::$default_routing['method']),
       'value' => $value,
       'page' => (int) $page);
     }
    else
     {
      $new_route = self::$error_routes;
     }

    if($http_redirection === false)
     {
      self::$new_routing = $new_route;
     }
    else
     {
      header('Location: '.url($new_route['controller'], $new_route['method'], $new_route['value'], $new_route['page'], null));
     }
   } // public static function redirect();



  /**
   * Verificamos la ruta objetivo sea válida
   * @param string $controller Controlador objetivo
   * @param string $method Método objetivo
   * @return boolean
   */
  private static function is_valid_route($controller, $method = null)
   {
    $controller = strtolower($controller);
    $method = ($method === null) ? self::$default_routing['method'] : $method;

    if(isset(self::$avaiable_controllers[$controller]) === false)
     {
      throw new Core_Exception('El controlador '.$controller.' no se encuentra en la lista de rutas.');
     }

    if(isset(self::$avaiable_controllers[$controller][$method]) === false)
     {
      throw new Core_Exception('El m&eacute;todo indicado ('.$controller.'->'.$method.') no existe en la lista de rutas.');
     }

    if(is_file(CONTROLLERS_DIR.'class.'.$controller.EXT) === false)
     {
      throw new Core_Exception('El archivo de controlador '.$controller.' no existe.');
     }

    return true;
   } // private static function is_valid_route();



  /**
   * Autocarga de componentes en base al archivo de configuración core.php
   * @return nothing
   */
  private static function load_components()
   {
    // Precarga de LittleDB para su próximo uso por los modelos.
    load_component('Configuration');
    Configuration::init();
    load_component('LDB');
    LDB::init();
    Configuration::load_from_db();
    load_component('Controller');
    load_component('Factory');
    load_component('Model');
    load_component('Session');
    Session::init();
    load_component('View');
   } // private static function load_components();
 } // final class Core();


/**
 * Excepción única de la clase Core
 */
class Core_Exception Extends \Exception { } // class Core_Exception();