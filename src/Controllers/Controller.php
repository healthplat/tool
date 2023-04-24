<?php

namespace Healthplat\Tool\Controllers;

use Healthplat\Tool\Services\ServiceTrait;
use Phalcon\Mvc\Dispatcher;

/**
 * 控制器
 * @package Healthplat\Tool\Controllers
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{
    use ServiceTrait;


    /**
     * 控制器前
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $globals = $this->di->get('config')->path('middleware.global');
        foreach ($globals as $class) {
            $flag = $class::factory($dispatcher);
            if (!$flag) {
                return false;
            }
        }
        return true;
        // 在控制器操作执行之前注入逻辑
    }

    /**
     * 控制器后
     * @param Dispatcher $dispatcher
     * @return void
     */
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        $globalAfters = $this->di->get('config')->path('middleware.globalAfter');
        foreach ($globalAfters as $class) {
            $class::factory($dispatcher);
        }
    }
}