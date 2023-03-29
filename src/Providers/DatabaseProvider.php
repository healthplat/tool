<?php

namespace Healthplat\Tool\Providers;

use Healthplat\Tool\Mysql;
use Phalcon\Config\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;


/**
 * 注册DB连接
 * 本类可用于多连接注册
 * @package Healthplat\Tool\Providers
 */
class DatabaseProvider implements ServiceProviderInterface
{
    private $beforeSharedInitialized = false;
    /**
     * @var bool
     */
    private $listenerEnabled = false;
    private $pdo = [
        'mysql' => Mysql::class,
    ];

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
        $config = $di->getConfig()->path('database.connection');
        if (!($config instanceof Config)) {
            return;
        }
        $adapter = $di->getConfig()->path('database.adapter');
        $name = 'db';
        $pdo = $this->pdo[$adapter];
        $di->setShared($name, function () use ($di, $config, $name, $pdo) {
            unset($config->adapter);
            $dn = isset($config->dbname) ? $config->dbname : 'unknown';
            $db = new $pdo($config->toArray());
            return $db;
        });
    }
}
