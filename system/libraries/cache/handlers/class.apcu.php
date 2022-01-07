<?php

namespace Framework\Cache;

class APCU extends \Framework\Cache\Base
{
    public function __construct()
    {
        if (\extension_loaded('apcu') === false or (bool) \ini_get('apc.enabled') === false) {
            throw new \Framework\Cache_Exception('La extensi&oacute;n APC no se encuentra activa.');
        }
    }

    public function get(string $name): mixed
    {
        $result = false;
        $data = \apcu_fetch($name, $result);
        return $result === true ? $data : false;
    }

    public function set(string $name, mixed $data, int $lifetime): bool
    {
        return (bool) \apcu_store($name, $data, $lifetime);
    }

    public function size(): int
    {
        $size = 0;
        $info = \apcu_cache_info();
        foreach ($info['cache_list'] as $entry) {
            $size += $entry['mem_size'];
        }

        return $size;
    }

    public function clear(string $name): bool
    {
        if (!empty($name)) {
            return \apcu_delete($name);
        } else {
            return \apcu_clear_cache();
        }
    }
}
