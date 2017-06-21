<?php

namespace Lib\Traits;

trait Singleton
{
    protected static $instance;

    public static function instance()
    {
        if (!isset(static::$instance)) {
            static::init();
        }

        return static::$instance;
    }

    private function __construct()
    {}

    private function __wakeup()
    {}

    private function __clone()
    {}
}
