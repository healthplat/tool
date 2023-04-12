<?php
namespace Healthplat\Tool\Middlewares;

use Healthplat\Tool\Structs\StructInterface;
use Phalcon\Mvc\Dispatcher;

/**
 * @package Healthplat\Tool\Structs
 */
interface MiddlewareInterface
{
    /**
     * @param array|null|object $payload 入参
     *
     * @return array|StructInterface
     */
    public function run(Dispatcher $dispatcher);
}