<?php

/**
 * Manejo de Vistas
 * @package class.view.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;


enum Return_Types
{
    case HTML;
    case JSON;
}

final class View
{
    /**
     * Definir el tipo de salida (JSON, HTML)
     * @var Return_Types
     */
    public static Return_Types $return_type = Return_Types::HTML;

    /**
     * Variables internas del componente
     * @var array
     */
    protected static array $variables = array();

    /**
     * Arreglo con plantillas
     * @var array
     */
    protected static array $templates = array();

    /**
     * Archivos Extra
     * @var array
     */
    // TODO: check this
    protected static array $files = array(
        'js' => array(),
        'css' => array(),
        'lang' => array()
    );

    protected static string $lang;


    public static function init(): void
    {
        if (is_file(APP_PATH . 'views/templates/framework_header.html') === true) {
            self::$templates[] = 'framework_header';
        }
    }

    /**
     * Agregar una clave con su respectivo valor al arreglo de claves
     * @param string | array $key Clave a asignar
     * @param mixed $value Valor de la clave
     * @return void
     */
    public static function add_key(string|array $key, $value = null): void
    {
        if (is_array($key) and $value === null) {
            self::$variables += $key;
        } else {
            self::$variables[$key] = $value;
        }
    }

    /**
     * Agregamos un archivo para que sea cargado en el header
     * @param string $type Tipo de archivo a agregar.
     * @param string $name Ruta del archivo
     * @return void
     */
    public static function add_file($type, $name): void
    {
        self::$files[$type][] = $name;
    }

    public static function set_lang($lang): void
    {
        self::$lang = $lang;
    }

    /**
     * Agregar una nueva plantilla para mostrar
     * @param string $template Plantilla a asignar
     * @return void
     */
    public static function add_template($template): void
    {
        self::$templates[] = $template;
    }

    /**
     * Cargamos todos los archivos de idioma solicitados
     * @param string $lang Idioma forzado
     * @return array
     */
    private static function load_language($lang = 'spanish'): array
    {
        $directory = APP_PATH . 'views/languages/';
        if (!is_dir($directory) === true) {
            throw new View_Exception('El directorio de idiomas ' . $directory . ' no existe.');
        }

        if (!is_file($directory . strtolower($lang) . '.json')) {
            throw new View_Exception('El archivo principal de idiomas ' . $directory . $lang . '.json no existe.');
        }

        $result = json_decode(file_get_contents($directory . strtolower($lang) . '.json'), true);
        if (count(self::$files['lang']) >= 1) {
            foreach (self::$files['lang'] as $file) {
                if (!is_file($directory . $file . '.json')) {
                    throw new View_Exception('El archivo de idiomas ' . $directory . $file . '.json no existe.');
                }

                $result += json_decode(file_get_contents($directory . $file . '.json'), true);
            }
        }
        return $result;
    }

    /**
     * Reiniciamos la clase borrando las variables y plantillas asignadas
     * @return void
     */
    public static function clear(): void
    {
        self::$variables = array();
        self::$templates = array();
        self::$files = array('js' => array(), 'css' => array(), 'lang' => array());
    }

    /**
     * Mostramos todas las plantillas cargadas
     * @param boolean $return Indica si retornar o no el HTML generado
     * @return void
     */
    public static function show(): void
    {
        if (self::$return_type === Return_Types::HTML && count(self::$templates) >= 1) {
            self::add_key(['system' => require_once(APP_PATH . 'configurations/global.php')]);
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
        } elseif (self::$return_type === Return_Types::JSON && count(self::$variables) >= 1) {
            echo json_encode(self::$variables);
        }
    }
}

/**
 * Excepción exclusiva del componente View
 * @access private
 */
class View_Exception extends \Framework\Standard_Exception
{
}
