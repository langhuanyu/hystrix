<?php

namespace Hystrix;

class Storage
{
    private static $_instance;

    private $config;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new \Predis\Client(RedisConfig::getConfig());
        }

        return self::$_instance;
    }
}
