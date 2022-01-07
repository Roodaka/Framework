<?php

/**
 * Abstracción de Controladores
 * @package class.controller.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.3
 * @access public
 */

namespace Framework;

abstract class Controller
{
    /**
     * Variable aún sin utilizar
     * @var boolean
     */
    protected $permisson_required = null;
    /**
     * Indica si existe una petición POSTs
     * @var boolean
     */
    protected $is_post = false;
    /**
     * Almacena las variables POST como objetos Post_Value
     * @var array
     */
    protected $post = array();
    /**
     * Indica la cantidad de variables POST
     * @var integer
     */
    protected $post_count = 0;
    /**
     * Indica si se han adjuntado archivos vía POST
     * @var boolean
     */
    protected $has_files = false;
    /**
     * Alias a _FILES
     * @var array
     */
    protected $files = array();
    /**
     * Conteo de variables en $this->files
     * @var integer
     */
    protected $files_count = 0;
    /**
     * Constructor del controlador.
     * Asigna las variables POST y FILE.
     * @param boolean $ignore_post Ignorar o no las peticiones POST (por redireccionamiento interno)
     * @return nothing
     */
    public function __construct($ignore_post = false)
    {
        if ($ignore_post === false and $_SERVER['REQUEST_METHOD'] === 'POST') {
            require(APP_PATH . 'libraries/utils/class.post_value.php');
            $this->is_post = true;

            foreach ($_POST as $key => $value) {
                $this->post[$key] = new Utils\Post_Value($value);
            }
            $this->post_count = count($this->post);

            if (isset($_FILES) === true) {
                $this->has_files = true;
                $this->files = (array) $_FILES;
                $this->files_count = count($_FILES);
            }
        }
    }

    /**
     * Convertimos la clase una cadena
     * @return string
     */
    public function __toString()
    {
        return 'Controlador ' . get_called_class();
    }

    /**
     * Invocamos al controlador
     * @return boolean
     */
    public function __invoke()
    {
        return $this->main();
    }

    /**
     * Evitamos la clonación de controladores.
     * @return nothing
     */
    public function __clone()
    {
        throw new Controller_Exception('No se permite clonar este objeto ' . $this . '.');
    }



    /**
     * Procesar los datos hasta ahora obtenidos, preparando el contexto del
     * controlador. Debe llamarse antes del método Main();
     * @return mixed
     */
    abstract public function init();



    /**
     * Método predeterminado, es llamado cuando no hay una función especificada.
     * @return mixed
     */
    abstract protected function main();
}

/**
 * Excepción única de la clase Controller
 * @access private
 */
class Controller_Exception extends \Exception { }
