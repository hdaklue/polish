<?php

namespace Hdaklue\Polish\Exceptions;

use BadMethodCallException;

class UnsupportedPolisherMethodException extends BadMethodCallException
{
    public function __construct(string $class, string $method)
    {
        parent::__construct(
            "Polisher [{$class}] does not support static polish method [{$method}].",
        );
    }
}
