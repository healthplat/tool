<?php

namespace Healthplat\Tool;

use Healthplat\Tool\Providers\DatabaseProvider;
use Healthplat\Tool\Providers\LoggerProvider;
use Healthplat\Tool\Providers\RouteProvider;
use Healthplat\Tool\Providers\ConfigProvider;

/**
 * Class Application
 */
class Application extends \Phalcon\Mvc\Application
{
    private $providers = [
        ConfigProvider::class,
        RouteProvider::class,
        DatabaseProvider::class,
        LoggerProvider::class,
    ];

    /**
     * Phalcon\AbstractApplication constructor
     *
     * @param \Phalcon\Di\DiInterface $container
     */
    public function __construct(\Phalcon\Di\DiInterface $container = null)
    {
        // 初始化服务
        foreach ($this->providers as $provider) {
            (new $provider())->register($container);
        }
        // 理论上上面已经注册过config服务 应该能直接调用
        $providers = $this->config->path('app.providers');
        foreach ($providers as $provider) {
            (new $provider())->register($container);
        }
        // request请求对象初始化
        $container->setShared('request', new Request());
        $this->useImplicitView(false);
    }
}