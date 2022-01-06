<?php

/**
 * Implementación del tipo de cacheo por archivos
 * @package class.file.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework\Cache;

class File extends Base
{
    /**
     * Constructor de la clase.
     * @return void
     */
    public function __construct()
    {
        if (is_writable(CACHE_CONFIG['path']) !== true) {
            throw new \Framework\Cache_Exception('El cache basado en archivos no pudo inicializarse por problemas de escritura.');
        }
    }

    public function get($name, $return = true)
    {
        // Incluimos y retornamos el archivo
        if (is_file(CACHE_CONFIG['path'] . $name . '.php')) {
            $data = unserialize(require(CACHE_CONFIG['path'] . $name . '.php'));
            // Si todavía le queda 'vida' lo cargamos
            if ($data['time'] > (time() - $data['lifetime'])) {
                return $return === true ? $data['data'] : true;
            } else {
                unlink(CACHE_CONFIG['path'] . $name . '.php');
                return false;
            }
        } else {
            return false;
        }
    }

    public function set($name, $data, $lifetime)
    {
        // Ubicación del archivo
        $target = CACHE_CONFIG['path'] . $name . '.php';

        $cache = array(
            'data' => $data,
            'lifetime' => (int) $lifetime,
            'time' => time()
        );

        // Abrimos el archivo
        $file = fopen($target, 'w');

        if (!$file) {
            throw new \Framework\Cache_Exception('No se pudo trabajar con el archivo "' . $target . '"');
        } elseif (!fwrite($file, '<?php defined(\'SYSTEM_PATH\') or exit(\'No tienes Permitido el acceso.\'); return \'' . serialize($cache) . '\';')) {
            throw new \Framework\Cache_Exception('No se pudo escribir el archivo "' . $target . '"');
        } else {
            fclose($file);
            return true;
        }
    }

    public function size()
    {
        $size = 0;
        $dir = opendir(CACHE_CONFIG['path']);
        if (!$dir) {
            throw new \Framework\Cache_Exception('No se pudo abrir el directorio ' . CACHE_CONFIG['path'] . '.');
        } else {
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' and $file !== '..') {
                    $size += filesize(CACHE_CONFIG['path'] . $file);
                }
            }
            closedir($dir);
            return $size;
        }
        return 0;
    }

    public function clear($name = '')
    {
        if (!empty($name)) {
            return unlink(CACHE_CONFIG['path'] . $name . '.php');
        }
        $dir = opendir(CACHE_CONFIG['path']);
        if (!$dir) {
            throw new \Framework\Cache_Exception('No se pudo abrir el directorio ' . CACHE_CONFIG['path'] . '.');
        } else {
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' || $file !== '..') {
                    unlink(CACHE_CONFIG['path'] . $file);
                }
            }
            closedir($dir);
        }
    }
}
