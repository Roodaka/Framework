<?php

/**
 * Manejo de Vistas
 * @package class.view.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

final class View
{
    /**
     * Variables internas del componente
     * @var Array
     */
    protected static $variables = array();

    /**
     * Arreglo con plantillas
     * @var array
     */
    protected static $templates = array();

    /**
     * Archivos Extra
     * @var Array
     */
    // TODO: check this
    protected static $files = array(
        'js' => array(),
        'css' => array(),
        'lang' => array()
    );

    protected static $lang;


    public static function init() {
        if (is_file(APP_PATH . 'views/templates/framework_header.html') === true) {
            self::$templates[] = 'framework_header';
        }
    }

    /**
     * Agregar una clave con su respectivo valor al arreglo de claves
     * @param string $key Clave a asignar
     * @param mixed $value Valor de la clave
     * @return nothing
     */
    public static function add_key($key, $value = null)
    {
        if (is_array($key) and $value === null) {
            self::$variables += $key;
        } else {
            self::$variables[$key] = $value;
        }
    } // public static function add_template();



    /**
     * Agregamos un archivo para que sea cargado en el header
     * @param string $type Tipo de archivo a agregar.
     * @param string $name Ruta del archivo
     * @return nothing
     */
    public static function add_file($type, $name)
    {
        self::$files[$type][] = $name;
    } // public static function add_file();



    public static function set_lang($lang)
    {
        self::$lang = $lang;
    }


    /**
     * Agregar una nueva plantilla para mostrar
     * @param string $template Plantilla a asignar
     * @return nothing
     */
    public static function add_template($template)
    {
        self::$templates[] = $template;
    } // public static function add_template();



    /**
     * Cargamos todos los archivos de idioma solicitados
     * @param string $lang Idioma forzado
     * @return array
     */
    private static function load_language($lang = 'spanish')
    {
        $directory = APP_PATH . 'views/languages/';
        if (is_dir($directory) === true) {
            if (is_file($directory . strtolower($lang) . '.json')) {
                $result = json_decode(file_get_contents($directory . strtolower($lang) . '.json'), true);
                if (count(self::$files['lang']) >= 1) {
                    foreach (self::$files['lang'] as $file) {
                        if (is_file($directory . $file . '.json')) {
                            $result += json_decode(file_get_contents($directory . $file . '.json'), true);
                        } else {
                            throw new View_Exception('El archivo de idiomas ' . $directory . $file . '.json no existe.');
                        }
                    }
                }
                return $result;
            } else {
                throw new View_Exception('El archivo principal de idiomas ' . $directory . $lang . '.json no existe.');
            }
        } else {
            throw new View_Exception('El directorio de idiomas ' . $directory . ' no existe.');
        }
    }



    /**
     * Reiniciamos la clase borrando las variables y plantillas asignadas
     * @return nothing
     */
    public static function clear()
    {
        self::$variables = array();
        self::$templates = array();
        self::$files = array('js' => array(), 'css' => array(), 'lang' => array());
    } // public static function clear();



    /**
     * Mostramos todas las plantillas cargadas
     * @param boolean $return Indica si retornar o no el HTML generado
     * @return nothing
     */
    public static function show()
    {
        if (count(self::$templates) >= 1) {
            self::add_key('system', require_once(APP_PATH . 'configurations/global.php'));
            self::add_key('core_files', self::$files);

            $latte = new \Latte\Engine;

            $latte->addFunction('url', function () {
                return url(...func_get_args());
            });

            $latte->setTempDirectory(APP_PATH . 'cached/templates/');
            $latte->setAutoRefresh($_ENV['PHP_ENV'] === 'development');
            //$rain->assign('lang', self::load_language(self::$lang));

            if (is_file(APP_PATH . 'views/templates/framework_footer.html') === true) {
                self::$templates[] = 'framework_footer';
            }

            foreach (self::$templates as $template) {
                $latte->render(APP_PATH . 'views/templates/' . $template . '.html', self::$variables);
            }
        } else // Consideramos una respuesta AJAX.
        {
            //TODO: Considerar idiomas
            if (count(self::$variables) >= 1) {
                echo json_encode(self::$variables);
            }
        }
    } // public function show();
} // class View();


/**
 * Excepción exclusiva del componente View
 * @access private
 */
class View_Exception extends \Exception
{
} // class View_Exception();