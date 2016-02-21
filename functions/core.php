<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
/**
 * libs/functions.php
 * @author Cody Roodaka
 * Creado el 03/04/2011 01:17 a.m.
 */


/**
 * Construir una URL
 * @param string $mod Módulo objetivo
 * @param string $val Valor
 * @param string $sec Submódulo
 * @param int $page Número de página
 * @param string $title Título (mero SEO)
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function url($controller, $method = null, $value = null, $page = null, $title = null)
 {
  return 'index.php?'.Framework\Core::ROUTING_CONTROLLER_VARIABLE.'='.$controller
  .(($value !== null) ? '&'.Framework\Core::ROUTING_VALUE_VARIABLE.'='.$value : '')
  .(($title !== null) ? '-'.$title : '')
  .(($method !== null) ? '&'.Framework\Core::ROUTING_METHOD_VARIABLE.'='.$method : '')
  .(((int) $page >= 1) ? '&'.Framework\Core::ROUTING_PAGENUMBER_VARIABLE.'='.$page : '');
 } // function url();



/**
 * Cargar un componente interno del Framework.
 * @param string $target Nombre del componente
 * @return nothing
 */
function load_component($target)
 {
  if(!class_exists($target))
   {
    if(is_file(LIBRARIES_DIR.'class.'.strtolower($target).EXT) === true)
     {
      require_once(LIBRARIES_DIR.'class.'.strtolower($target).EXT);
     }
    else
     {
      throw new \Framework\Core_Exception('No se encuentra el componente '.$target);
     }
   }
 }



/**
 * Cargar una librería de usuario
 * @param string $target Nombre de la librería
 * @return nothing
 */
function load_util($target)
 {
  if(!class_exists($target))
   {
    if(is_file(LIBRARIES_DIR.'utils'.DS.'class.'.strtolower($target).EXT) === true)
     {
      require_once(LIBRARIES_DIR.'utils'.DS.'class.'.strtolower($target).EXT);
     }
    else
     {
      throw new \Framework\Core_Exception('No se encuentra la clase utilitaria '.$target);
     }
   }
 }



/**
 * Cargar una librería de terceros
 * @param string $target Nombre de la librería
 * @return nothing
 */
function load_third_party($target)
 {
  if(!class_exists($target))
   {
    if(is_file(LIBRARIES_DIR.'third_party'.DS.'class.'.strtolower($target).EXT) === true)
     {
      require_once(LIBRARIES_DIR.'third_party'.DS.'class.'.strtolower($target).EXT);
     }
    else
     {
      throw new \Framework\Core_Exception('No se encuentra la clase de terceros'.$target);
     }
   }
 }



/**
 * Crear un modelo.
 * @param string $name Nombre del modelo
 * @param integer $id Identificador del modelo (opcional)
 * @param array|string $specified_fields Campos específicos a cargar (opcional)
 * @param boolean $autoload Auto cargar los datos del modelo
 * @param boolean $protected Proteger el modelo indicado a la limpieza de modelos.
 * @return object Referencia al objeto creado.
 */
function load_model($model, $id = null, $specified_fields = null, $autoload = true, $protected = false)
 {
  return \Framework\Factory::create($model, $id, $specified_fields, $autoload, $protected);
 }



/**
 * Calcular el paginado para las consultas MySQL
 * @param int $page Número de página
 * @param int $limit Límite de resultados por página
 * @return array
 */
function paginate($page, $limit)
 {
  if($page === 1) { $return = array(0, $limit); }
  else
   {
    $start = (($page - 1) * $limit);
    $return = array($start, ($start + $limit));
   }
  return $return; 
 } // function paginate();



/**
 * Agregamos el manejo personalizado de las excepciones
 * @param object $exception Excepción entregada por el sistema
 * @return nothing
 */
function exception_handler($exception)
 {
  echo '<div>
   <h4>'
    .str_replace('Framework\\', '', str_replace('_Exception', '', get_class($exception)))
    .' Error: '.$exception->getMessage()
   .'</h4>
   <p><b>File</b>: '.str_replace(ROOT, 'HOME_DIR'.DS, $exception->getFile()).'</p>
   <p><b>Trace</b>: '.str_replace(ROOT, 'HOME_DIR'.DS, $exception->getTraceAsString()).'</p>
  </div>';
 } // function exception_handler();



/**
 * Solicitar un arreglo (o un campo) de configuración.
 * @param string $target Nombre del arreglo de configuraciones.
 * @param string $field Campo específico a cargar del arreglo anteriormente indicado
 * @return array|mixed
 */
function get_config($target, $field = null)
 {
  return \Framework\Configuration::get($target, $field);
 }



/**
 * Chequeamos si estamos en una ruta específica
 * @return boolean
 */
function is_routing($controller, $method = null)
 {
  if($method !== null)
   {
    return ($controller === \Framework\Core::$target_routing['controller'] && $method === \Framework\Core::$target_routing['method']);
   }
  else
   {
    return ($controller === \Framework\Core::$target_routing['controller']);
   }
 }



/**
 * Obtener el nombre del controlador actual.
 * @return string
 */
function get_routing_controller()
 {
  return \Framework\Core::$target_routing['controller'];
 }



/**
 * Obtener el nombre del método actual.
 * @return string
 */
function get_routing_method()
 {
  return \Framework\Core::$target_routing['method'];
 }



/**
 * Obtener el nombre del controlador actual.
 * @param boolean $return_int Exigir el retorno de un número o de una cadena
 * @return string|integer
 */
function get_routing_value($return_int = true)
 {
  if($return_int === true)
   {
    return (int) \Framework\Core::$target_routing['value'];
   }
  else
   {
    return \Framework\Core::$target_routing['value'];
   }
 }



/**
 * Obtener el número de página actual
 * @return integer
 */
function get_routing_page()
 {
  return (int) \Framework\Core::$target_routing['page'];
 }