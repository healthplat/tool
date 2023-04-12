<?php

namespace Healthplat\Tool\Middlewares;

use Healthplat\Tool\Services\ServiceTrait;
use Phalcon\Mvc\Dispatcher;
use stdClass;
use Healthplat\Tool\Middlewares\MiddlewareInterface as HxMiddlewareInterface;

/**
 * 业务逻辑抽像
 * @package Healthplat\Tool\Logics
 */
abstract class Middleware extends \Phalcon\Di\Injectable implements HxMiddlewareInterface
{
    use ServiceTrait;

    /**
     * 结构体工厂
     * @param array|stdClass|null $payload
     * @return mixed
     */
    public static function factory(Dispatcher $dispatcher)
    {
        // 1. new实例
        $logic = new static();
        $result = $logic->run($dispatcher);
        // 4. 最后返回结果
        return $result;
    }
}
