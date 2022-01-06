<?php
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
  return 'index.php?'.Framework\Core::KEY_CONTROLLER.'='.$controller
  .(($value !== null) ? '&'.Framework\Core::KEY_VALUE.'='.$value : '')
  .(($title !== null) ? '-'.$title : '')
  .(($method !== null) ? '&'.Framework\Core::KEY_METHOD.'='.$method : '')
  .(((int) $page >= 1) ? '&'.Framework\Core::KEY_PAGE.'='.$page : '');
 } // function url();



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
    $return = array((($page - 1) * $limit), $limit);
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
   <h3>'
    .str_replace('Framework\\', '', str_replace('_Exception', '', get_class($exception)))
    .' Error: '.$exception->getMessage()
   .'</h3>
   <p><strong>Source</strong>: '.str_replace(ROOT_PATH, 'HOME_DIR/', $exception->getFile()).' at <strong>line '.$exception->getLine().'</strong></p>
   <p><h4>Trace:</h4>';

   $last_file = 'HOME_DIR/';
   $last_line = 0;
   foreach($exception->getTrace() as $trace)
    {
     $last_file = isset($trace['file']) ? $trace['file'] : $last_file;
     $last_line = isset($trace['line']) ? $trace['line'] : $last_line;
     echo '<span>'.str_replace(ROOT_PATH, 'HOME_DIR/', $last_file)
     .((isset($trace['class']) && isset($trace['type'])) ? $trace['class'].$trace['type'] : '').$trace['function']
     .'('.((DEVELOPER_MODE === true) ? '<i>'.json_encode($trace['args']).'</i>' : '').')</span> on <strong>line '.$last_line.'</strong><br />';
    }
   echo '</p></div>';
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