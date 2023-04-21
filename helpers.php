<?php
use Phalcon\Di\Di;

if (!function_exists('app')) {
    /**
     * @return \Phalcon\Di\DiInterface
     */
    function app()
    {
        return Di::getDefault();
    }
}


if (!function_exists('config')) {
    /**
     * @return \Phalcon\Config
     */
    function config()
    {
        return \app()->getConfig();
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}