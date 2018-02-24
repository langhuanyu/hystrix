<?php

namespace Hystrix;

class RedisConfig
{
    /**
     * Constructor
     */
    public static function getConfig()
    {
        return [
            'host' => '',
            'post' => '6379',
            'database' => '10',
            'password' => '',
        ];
    }
}
