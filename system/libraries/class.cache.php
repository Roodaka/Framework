<?php

namespace Framework;

class Cache
{
    private static $handler = null;
    private static $lifetime = 900; // 15 mins

    public static function init()
    {
        if ($_ENV['PHP_ENV'] === 'development') {
            require_once(SYSTEM_PATH . 'cache_handlers/class.base.php');

            if (extension_loaded('apcu') === true or (bool) ini_get('apcu.enabled') === true) {
                require_once(SYSTEM_PATH . 'cache_handlers/class.apcu.php');
                self::$handler = new \Framework\Cache\APCU();
            } else {
                require_once(SYSTEM_PATH . 'cache_handlers/class.file.php');
                self::$handler = new \Framework\Cache\File;
            }
        }
    }

    public static function set($name, $data, $lifetime = null)
    {
        if (self::$handler !== null) {
            return self::$handler->set(strtolower($name), $data, (((int)$lifetime === 0) ? self::$lifetime : $lifetime));
        }
        return false;
    }

    public static function get($name)
    {
        if (self::$handler !== null) {
            return self::$handler->get(strtolower($name));
        }
        return false;
    }

    public static function size()
    {
        if (self::$handler !== null) {
            return (int) self::$handler->size();
        }
        return 0;
    }

    public static function clear()
    {
        if (self::$handler !== null) {
            return (int) self::$handler->clear();
        }
        return false;
    }
}

class Cache_Exception extends \Exception { }
