<?php

namespace Framework\Cache;

class File extends Base
{
    private static string $path;

    public function __construct()
    {
        if (is_writable(APP_PATH . 'cached/data') !== true) {
            self::$path = APP_PATH . 'cached/data';
        } else {
            throw new \Framework\Cache_Exception('El cache basado en archivos no pudo inicializarse por problemas de escritura.');
        }
    }

    public function get(string $name): mixed
    {
        if (is_file(self::$path . $name . '.php')) {
            $data = unserialize(require(self::$path . $name . '.php'));
            if ($data['time'] > (time() - $data['lifetime'])) {
                return $return === true ? $data['data'] : true;
            } else {
                unlink(self::$path . $name . '.php');
                return false;
            }
        } else {
            return false;
        }
    }

    public function set(string $name, mixed $data, int $lifetime): bool
    {
        $target = self::$path . $name . '.php';

        $cache = array(
            'data' => $data,
            'lifetime' => (int) $lifetime,
            'time' => time()
        );

        $file = fopen($target, 'w');

        if (!$file) {
            throw new \Framework\Cache_Exception('No se pudo trabajar con el archivo "' . $target . '"');
        } elseif (!fwrite($file, '<?php return \'' . serialize($cache) . '\';')) {
            throw new \Framework\Cache_Exception('No se pudo escribir el archivo "' . $target . '"');
        } else {
            fclose($file);
            return true;
        }
    }

    public function size(): int
    {
        $size = 0;
        $dir = opendir(self::$path);
        if (!$dir) {
            throw new \Framework\Cache_Exception('No se pudo abrir el directorio ' . self::$path . '.');
        } else {
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' and $file !== '..') {
                    $size += filesize(self::$path . $file);
                }
            }
            closedir($dir);
            return $size;
        }
        return 0;
    }

    public function clear(string $name): bool
    {
        if (!empty($name)) {
            return unlink(self::$path . $name . '.php');
        }
        $dir = opendir(self::$path);
        if (!$dir) {
            throw new \Framework\Cache_Exception('No se pudo abrir el directorio ' . self::$path . '.');
        } else {
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' || $file !== '..') {
                    unlink(self::$path . $file);
                }
            }
            closedir($dir);
        }
    }
}
