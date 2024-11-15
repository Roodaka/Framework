<?php

namespace Framework;

class Cache
{
    private static \Framework\Cache\Base | null $handler = null;
    private static $lifetime = 900; // 15 mins

    public static function init(): void
    {
        if ($_ENV['CACHE_DRIVER'] !== 'none') {
            require_once(SYSTEM_PATH . 'libraries/cache/handlers/class.base.php');

            if (extension_loaded('apcu') === true or (bool) ini_get('apcu.enabled') === true) {
                require_once(SYSTEM_PATH . 'libraries/cache/handlers/class.apcu.php');
                self::$handler = new \Framework\Cache\APCU();
            } else {
                require_once(SYSTEM_PATH . 'libraries/cache/handlers/class.file.php');
                self::$handler = new \Framework\Cache\File;
            }
        }
    }

    public static function set(string $name, mixed $data, int $lifetime = 0): bool
    {
        if (self::$handler !== null) {
            return self::$handler->set(strtolower($name), $data, (((int)$lifetime === 0) ? self::$lifetime : $lifetime));
        }
        return false;
    }

    public static function get(string $name): mixed
    {
        if (self::$handler !== null) {
            return self::$handler->get(strtolower($name));
        }
        return false;
    }

    public static function size(): int
    {
        if (self::$handler !== null) {
            return (int) self::$handler->size();
        }
        return 0;
    }

    public static function clear(string $name): bool
    {
        if (self::$handler !== null) {
            return self::$handler->clear($name);
        }
        return false;
    }
}

class Cache_Exception extends \Exception { }
