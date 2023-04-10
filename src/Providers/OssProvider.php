<?php

namespace Healthplat\Tool\Providers;

use Healthplat\Tool\Mysql;
use Healthplat\Tool\Oss;
use Phalcon\Config\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;


/**
 * oss类
 * @package Healthplat\Tool\Providers
 */
class OssProvider implements ServiceProviderInterface
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
         * 1. reset instance
         * @var Container $di
         */
        $config = $di->getConfig()->path('oss');
        if (!($config instanceof Config)) {
            return;
        }
        $appConfig = $di->getConfig()->path('app');
        $appName = '';
        if (($config instanceof Config)) {
            $appName = $appConfig->appName;
        }
        $di->setShared('oss', function () use ($di, $config, $appName) {
            return new Oss($config->accessKeyId, $config->accessKeySecret, $config->endpoint, $appName);
        });
    }
}