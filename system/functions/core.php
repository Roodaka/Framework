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
    return 'index.php?' . Framework\Router::KEY_CONTROLLER . '=' . $controller
        . (($value !== null) ? '&' . Framework\Router::KEY_VALUE . '=' . $value : '')
        . (($title !== null) ? '-' . $title : '')
        . (($method !== null) ? '&' . Framework\Router::KEY_METHOD . '=' . $method : '')
        . (((int) $page >= 1) ? '&' . Framework\Router::KEY_PAGE . '=' . $page : '');
} // function url();



/**
 * Crear un modelo.
 * @param string $name Nombre del modelo
 * @param int $id Identificador del modelo (opcional)
 * @param array|string $specified_fields Campos específicos a cargar (opcional)
 * @param boolean $autoload Auto cargar los datos del modelo
 * @param boolean $protected Proteger el modelo indicado a la limpieza de modelos.
 * @return object Referencia al objeto creado.
 */
function load_model(string $model, int $id = null, array $specified_fields = [], bool $autoload = true, bool $protected = false): \Framework\Model
{
    return \Framework\Factory::create($model, $id, $specified_fields, $autoload, $protected);
}



/**
 * Calcular el paginado para las consultas MySQL
 * @param int $page Número de página
 * @param int $limit Límite de resultados por página
 * @return array
 */
function paginate($page, $limit): array
{
    if ($page === 1) {
        $return = [0, $limit];
    } else {
        $return = [(($page - 1) * $limit), $limit];
    }
    return $return;
} // function paginate();



/**
 * Agregamos el manejo personalizado de las excepciones
 * @param object $exception Excepción entregada por el sistema
 * @return void
 */
function exception_handler($exception): void
{
    echo '<div>
   <h3>'
        . str_replace('Framework\\', '', str_replace('_Exception', '', get_class($exception)))
        . ' Error: ' . $exception->getMessage()
        . '</h3>
   <p><strong>Source</strong>: ' . str_replace(ROOT_PATH, 'HOME_DIR/', $exception->getFile()) . ' at <strong>line ' . $exception->getLine() . '</strong></p>
   <p><h4>Trace:</h4>';

    $last_file = 'HOME_DIR/';
    $last_line = 0;
    foreach ($exception->getTrace() as $trace) {
        $last_file = isset($trace['file']) ? $trace['file'] : $last_file;
        $last_line = isset($trace['line']) ? $trace['line'] : $last_line;
        echo '<span>' . str_replace(ROOT_PATH, 'HOME_DIR/', $last_file)
            . ' ' . ((isset($trace['class']) && isset($trace['type'])) ? $trace['class'] . $trace['type'] : '') . $trace['function']
            . '(' . (($_ENV['PHP_ENV'] === 'development') ? '<i>' . json_encode($trace['args']) . '</i>' : '') . ')</span> on <strong>line ' . $last_line . '</strong><br />';
    }
    echo '</p></div>';
}

/**
 * Chequeamos si estamos en una ruta específica
 * @return boolean
 */
function is_routing($controller, $method = null): bool
{
    if ($method !== null) {
        return ($controller === \Framework\Router::$target_routing['controller'] && $method === \Framework\Router::$target_routing['method']);
    } else {
        return ($controller === \Framework\Router::$target_routing['controller']);
    }
}

/**
 * Obtener el nombre del controlador actual.
 * @return string
 */
function get_routing_controller(): string
{
    return \Framework\Router::$target_routing['controller'];
}

/**
 * Obtener el nombre del método actual.
 * @return string
 */
function get_routing_method(): string
{
    return \Framework\Router::$target_routing['method'];
}

/**
 * Obtener el nombre del controlador actual.
 * @param boolean $return_int Exigir el retorno de un número o de una cadena
 * @return string|int
 */
function get_routing_value($return_int = true): string|int
{
    if ($return_int === true) {
        return (int) \Framework\Router::$target_routing['value'];
    } else {
        return \Framework\Router::$target_routing['value'];
    }
}

/**
 * Obtener el número de página actual
 * @return int
 */
function get_routing_page(): int
{
    return (int) \Framework\Router::$target_routing['page'];
}
