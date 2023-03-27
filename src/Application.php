<?php

namespace Healthplat\Tool;

use App\Providers\RouteProvider;
use Healthplat\Tool\Providers\ConfigProvider;

/**
 * Class Application
 */
class Application extends \Phalcon\Mvc\Application
{
    private $providers = [
        ConfigProvider::class,
        RouteProvider::class
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
            $this->register(new $provider());
        }
        // 理论上上面已经注册过config服务 应该能直接调用
        $providers = $this->config->path('app.providers');
        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
        $this->useImplicitView(false);
    }
}