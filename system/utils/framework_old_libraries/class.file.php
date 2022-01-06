<?php

namespace Framework\Utils;

/**
 * Clase para subida de archivos al servidor.
 * Parte de Little Forum Script
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
class File
{
    /**
     * Arreglo de configuraciones
     * @var array
     */
    private $configuration = array();
    /**
     * Código de error actual
     * @var integer
     */
    private $error = null;

    const ERROR_COPY = 0;
    const ERROR_IMAGESIZE = 1;
    const ERROR_FILESIZE = 2;
    const ERROR_MIME_TYPE = 3;
    const ERROR_EXTENSION = 4;
    const ERROR_EMPTY = 5;
    const ERROR_SHELL = 6;


    // Directorio en donde se copiará el archivo
    protected $path = '/';
    // Tamaño máximo, en bytes
    protected $max_size = 1048576;
    // Ancho máximo, en píxeles
    protected $max_width = 768;
    // Alto máximo, en píxeles
    protected $max_height = 512;
    // Tipos de archivo permitidos{"image\\/png":".png","image\\/jpg":".jpg","image\\/jpeg":".jpeg"}
    protected $allow_filetypes = array(
        'image/png' => '.png',
        'image/jpg' => '.jpg',
        'image/jpeg' => '.jpeg'
    );
    // Nombre del archivo ya copiado
    protected $futurename = '';
    // Ubicacion completa del archivo subido
    public $result = '';
    // Archivo por defecto
    public $default_file = '';



    /**
     * Inicializamos la clase Uploader
     * @param string $target_path Directorio donde se copiará la imagen.
     * @param string $name Nuevo nombre del archivo copiado
     * @param int $max_size Tamaño máximo del archivo en bytes
     * @param int $max_width Ancho máximo del archivo en pixeles
     * @param int $max_height Alto máximo del archivo en pixeles
     * @param array $allow_filetypes Filtro de tipos de archivo a subir
     * @author Cody Roodaka <roodakazo@hotmail.com>
     */
    public function __construct($target_path, $futurename, $default = null)
    {
        $this->path = $target_path;
        $this->futurename = $futurename;
    } // public functiom __construct


    /**
     * Subimos la imagen
     * @param array $file Arreglo con los datos del archivo temporal objetivo
     * @return boolean
     * @author Cody Roodaka <roodakazo@hotmail.com> & Alexander Eberle
     */
    public function upload_file($file = array())
    {
        // Filtro por contenido vacío
        if (!empty($file['tmp_name']) && is_uploaded_file($file['tmp_name']) == true) {
            // Filtro de encabezado
            if (isset($this->allow_filetypes[$file['type']])) {
                $imaginfo = getimagesize($file['tmp_name']);
                // Filtro por estructura
                if (isset($this->allow_filetypes[$imaginfo['mime']])) {
                    // Chequeamos que tenga el tamaño requerido
                    if (filesize($file['tmp_name']) <= $this->max_size) {
                        // Comprobamos que tenga el tamaño indicado
                        if ($imaginfo[0] <= $this->max_width && $imaginfo[1] <= $this->max_height) {
                            // Copiamos el archivo final
                            //TODO: Cambiar a "move_uploaded_file"
                            $copy = copy($file['tmp_name'], $this->path . $this->futurename . $this->allow_filetypes[$imaginfo['mime']]);
                            if ($copy == true) {
                                $this->error = false;
                                $this->result = $this->path . $this->futurename . $this->allow_filetypes[$imaginfo['mime']];
                                return true;
                            } else {
                                $this->error = 'copy';
                            }
                        } else {
                            $this->error = 'imagesize';
                        }
                    } else {
                        $this->error = 'filesize';
                    }
                } else {
                    $this->error = 'mime';
                }
            } else {
                $this->error = 'exthead';
            }
        } else {
            $this->error = 'empty';
        }
        return false;
    } // public function upload_file



    /**
     * Cargamos la imagen desde una URL externa
     * @param string $mail Mail objetivo
     * @return boolean
     * @author Cody Roodaka <roodakazo@hotmail.com>
     */
    public function use_url($url)
    {
        // Nro de caracteres de la url
        $long = strlen($url);

        // Guardamos solo la extension del archivo
        $ext = substr($url, ($long - 4), $long);

        $imaginfo = getimagesize($url);
        if (isset($this->allow_filetypes[$imaginfo['mime']])) {
            if ($imaginfo[0] <= $this->max_width && $imaginfo[1] <= $this->max_height) {
                $content = file_get_contents($url);
                $filename = $this->path . $this->futurename . $this->allow_filetypes[$imaginfo['mime']];
                file_put_contents($filename, $content);
                $this->result = $filename;
                return true;
            } else {
                $this->error = 'imagesize';
                return false;
            }
        } else {
            $this->error = 'mime';
            return false;
        }
    } // public function use_url();



    /**
     * Usamos una URL de gravatar.
     * @param string $mail Mail objetivo
     * @return boolean
     * @author Cody Roodaka <roodakazo@hotmail.com>
     */
    public function use_gravatar($mail)
    {
        $this->result = 'http://www.gravatar.com/avatar/' . md5(strtolower($mail)) . '?s=120';
        return true;
    } // public function use_gravatar();



    /**
     * Seleccionamos el valor por defecto para los avatares.
     * @return boolean
     * @author Cody Roodaka <roodakazo@hotmail.com>
     */
    public function use_default()
    {
        $this->result = $this->default_file;
        return true;
    }
} // class Uploader();