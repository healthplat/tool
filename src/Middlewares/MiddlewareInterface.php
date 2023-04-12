<?php
namespace Healthplat\Tool\Middlewares;

use Phalcon\Mvc\Dispatcher;

/**
 * @package Healthplat\Tool\Structs
 */
interface MiddlewareInterface
{
    /**
     * 结构体静态构造方法
     * @param Dispatcher $dispatcher
     * @return mixed
     */
    public static function factory(Dispatcher $dispatcher);
}