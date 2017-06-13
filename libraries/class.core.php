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
  private static $avaiable_controllers = array();

  /**
   * Configuración del sistema
   * @var array
   */
  public static $config = array();

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
  private static $new_routing = array(
   'controller' => null,
   'method' => null,
   'value' => null,
   'page' => 1);
  /**
   * URL de ruta hacia el sistema.
   * @var string
   */
  public static $url_fullpath = null;
  /**
   * Código de error actual
   * @var integer
   */
  public static $error_code = null;

  // Constantes que definen los errores en la carga del controlador
  const ERROR_CONTEXT = 1; // El contexto no permite el uso de este controlador (sin utilizar aún)
  const ERROR_FILE = 2; // No se encuentra el archivo del controlador
  const ERROR_MISSING_ROUTE = 3; // Controlador o método fuera del listado de rutas.
  const ERROR_INVALID_ROUTE = 4; // Controlador o método indefinido.
  const ERROR_LOOP = 6; // Se ha alcanzado el número máximo de redirecciones

  // Para hacer más dinámico el sistema, estas constantes son quienes definen
  // las claves en el arreglo $_GET que decidirán el trayecto del mismo.
  const KEY_CONTROLLER = 'a';
  const KEY_METHOD = 'f';
  const KEY_VALUE = 'v';
  const KEY_PAGE = 'p';



   /**
    * Iniciamos el Núcleo del sistema
    * @param array $initial_data Arreglo con los datos (tiempo y RAM) iniciales
    * @return nothing
    */
  public static function init()
   {
    self::$config = get_config('core');

    self::$avaiable_controllers = get_config('routes');

    $path = explode('/', $_SERVER['SCRIPT_NAME']);
    array_shift($path);
    array_pop($path);
    self::$url_fullpath = ((isset($_SERVER['REQUEST_SCHEME'])) ? $_SERVER['REQUEST_SCHEME'] : 'http').'://'.$_SERVER['HTTP_HOST'].'/'.implode('/', $path).'/';

    // Cargamos configuraciones del sitio y las preferencias del usuario
    self::route();
   } // public static function init();



   /**
    * Cargador dinámico de controladores
    * @return nothing
    */
  private static function route()
   {
    // Controlador
    if(isset($_GET[self::KEY_CONTROLLER]))
     {
      self::$target_routing['controller'] = $_GET[self::KEY_CONTROLLER];
     } else { self::$target_routing['controller'] = self::$config['default_route']['controller']; }

    // Método
    if(isset($_GET[self::KEY_METHOD]))
     {
      self::$target_routing['method'] = $_GET[self::KEY_METHOD];
     } else { self::$target_routing['method'] = self::$config['default_route']['method']; }

    // Índice
    if(isset($_GET[self::KEY_VALUE]))
     {
      self::$target_routing['value'] = $_GET[self::KEY_VALUE];
     }

    // Página
    if(isset($_GET[self::KEY_PAGE]))
     {
      self::$target_routing['page'] = (int) $_GET[self::KEY_PAGE];
     }
    if(self::is_valid_route(self::$target_routing['controller'], self::$target_routing['method']) === false)
     {
      self::$target_routing = self::$config['error_route'];
     }

    self::call_controller();

    // Renderizado final.
    View::show();
   } // protected static function route();



  /**
   * Cargamos el controlador objetivo del router.
   * @param string $controller Controlador Objetivo
   * @param string $method Método a ejecutar
   * @param string $value Valor o ID
   * @param integer $page Número de página
   * @param boolean $redirected Indica si la llamada es parte de una redirección
   * @return nothing
   */
  private static function call_controller($controller = null, $method = null, $value = null, $page = 1, $redirected = 0)
   {
    if($controller !== null)
     {
      $method = $method !== null ? $method : self::$default_routing['method'];
      self::$target_routing = array('controller' => $controller, 'method' => $method, 'value' => $value, 'page' => $page);
     }

    require_once(CONTROLLERS_DIR.'class.'.strtolower(self::$target_routing['controller']).EXT);

    // Hacemos la última validación.
    $class = '\Framework\Controllers\\'.self::$target_routing['controller'];
    $controller = new $class($redirected);
    if(get_parent_class($controller) === 'Framework\Controller')
     {
      call_user_func_array(array($controller, 'build_header'), array());
      call_user_func_array(array($controller, self::$target_routing['method']), array());
     }
    else
     {
      self::handle_error(self::ERROR_INVALID_ROUTE, 'El controlador cargado ('.self::$target_routing['controller'].') es inv&aacute;lido.', self::$error);
     }

    // Esta porción de código sólo es llamada cuando un controlador pide, desde
    // sí mismo, una redirección. Se reiniciarán las vistas y el controlador
    // actual
    if(self::$new_routing['controller'] !== null && $redirected > 0 && $redirected < self::$config['max_redirections'])
     {
      // Removemos el controlador anterior.
      unset($controller);

      // Redireccionamos a la nueva ruta.
      self::$target_routing = self::$new_routing;

      // Reiniciamos las vistas.
      View::clear();

      // Reiniciamos los Modelos.
      Factory::clear();

      // Llamamos a esta misma función para continuar el proceso.
      self::call_controller(self::$target_routing['controller'], self::$target_routing['method'], self::$target_routing['value'], self::$target_routing['page'], true);
     }
    elseif($redirected === self::$config['max_redirections'])
     {
      self::handle_error(self::ERROR_LOOP);
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
    if(self::is_valid_route($controller, $method) === false)
     {
      $new_route = self::$config['error_route'];
     }
    else
     {
      $new_route = array(
       'controller' => $controller,
       'method' => $method,
       'value' => $value,
       'page' => $page);
     }
    

    if($http_redirection === false)
     {
      self::$new_routing = $new_route;
     }
    else
     {
      header('Location: '.self::$url_fullpath.url($new_route['controller'], $new_route['method'], $new_route['value'], $new_route['page'], null));
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
    $method = ($method === null) ? self::$config['default_route']['method'] : $method;

    if(isset(self::$avaiable_controllers[$controller]) === false)
     {
      self::handle_error('El controlador '.$controller.' no se encuentra en la lista de rutas.', self::ERROR_INVALID_ROUTE);
     }
    elseif(isset(self::$avaiable_controllers[$controller][$method]) === false)
     {
      self::handle_error('El m&eacute;todo indicado ('.$controller.'->'.$method.') no existe en la lista de rutas.', self::ERROR_INVALID_ROUTE);
     }
    elseif(is_file(CONTROLLERS_DIR.'class.'.$controller.EXT) === false)
     {
      self::handle_error('El archivo de controlador '.$controller.' no existe.', self::ERROR_FILE);
     }
    return (bool) (self::$error_code === null);
   } // private static function is_valid_route();



  /**
   * Método para el manejo interno de errores.
   * @param integer $error_code Código de error
   * @param integer $force_close Forzar fin de ejecución
   * @param string $message Mensaje opcional
   * @return void
   */
  public static function handle_error($message = '', $error_code = self::ROUTING_ERROR_CONTEXT)
   {
    self::$error_code = $error_code;

    if(DEVELOPER_MODE === true)
     {
      throw new Core_Exception($message);
     }
    else
     {
      echo $message;
      // TODO: ERROR LOG
     }
   }
 } // final class Core();


/**
 * Excepción única de la clase Core
 */
class Core_Exception Extends \Exception { } // class Core_Exception();