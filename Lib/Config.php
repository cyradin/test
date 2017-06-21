<?php

namespace Lib;

use Noodlehaus\Config as BaseConfig;

class Config
{
    use Traits\Singleton;

    private static function init()
    {
        $configFiles = scandir(__DIR__ . '/../config');
        // removing . and ..
        array_shift($configFiles);
        array_shift($configFiles);

        // moving 'local.yml' to the end of array,
        // so it will overwrite other parameters
        usort($configFiles, function($a, $b) {
            return ($b == 'local.yml') ? -1 : 1;
        });

        foreach ($configFiles as &$file) {
            $file = __DIR__ . '/../config/' . $file;
        }

        static::$instance = new BaseConfig($configFiles);
    }
}