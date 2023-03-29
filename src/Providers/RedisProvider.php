<?php
namespace Healthplat\Tool\Providers;

use Healthplat\Tool\Client;
use Healthplat\Tool\Mysql;
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;


/**
 * 注册redis链接
 * @package Healthplat\Tool\Providers
 */
class RedisProvider implements ServiceProviderInterface
{
    /**
     * 注册连接
     * @param \Phalcon\Di\DiInterface $di
     * @return void
     * @throws \ErrorException
     */
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        /**
         * 调整配置兼容
         */
        $config = $di->getConfig()->path('redis');
        // 1. Redis对象
        $optConfig = isset($config->options) && $config->options instanceof Config ? $config->options->toArray() : $config->toArray();
        $di->set('redis', function() use ($optConfig){
            if (!extension_loaded('redis')) {
                throw new \RuntimeException("Extension redis MUST be installed and loaded");
            }
            return new Client($optConfig);
        });
    }
}
