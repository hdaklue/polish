<?php

namespace Hdaklue\Polish;

use Hdaklue\Polish\Exceptions\UnsupportedPolisherMethodException;

abstract class BasePolisher
{
    public static function __callStatic($method, $args): mixed
    {
        $reflection = new \ReflectionClass(static::class);
        
        if (!$reflection->hasMethod($method) || 
            $method === '__callStatic' || 
            !$reflection->getMethod($method)->isStatic() ||
            !$reflection->getMethod($method)->isPublic()) {
            throw new UnsupportedPolisherMethodException(static::class, $method);
        }

        return static::{$method}(...$args);
    }
}
