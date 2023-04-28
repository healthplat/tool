<?php

namespace Healthplat\Tool\Providers;

use Healthplat\Tool\Events\Listeners\DatabaseListener;
use Healthplat\Tool\Mysql;
use Phalcon\Config\Config;
use Phalcon\Di\Di;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager;


/**
 * 注册DB连接
 * 本类可用于多连接注册
 * @package Healthplat\Tool\Providers
 */
class DatabaseProvider implements ServiceProviderInterface
{
    private $pdo = [
        'mysql' => Mysql::class,
    ];

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $config = $di->getConfig()->path('database.connection');
        $dumpSql = $di->getConfig()->path('database.dumpSql') ?? false;
        if (!($config instanceof Config)) {
            return;
        }
        $adapter = $di->getConfig()->path('database.adapter');
        $name = 'db';
        $pdo = $this->pdo[$adapter];
        $di->setShared($name, function () use ($di, $config, $name, $pdo, $dumpSql) {
            unset($config->adapter);
            $dn = isset($config->dbname) ? $config->dbname : 'unknown';
            $db = new $pdo($config->toArray());
            $db->setEventsManager($di->getEventsManager());
            return $db;
        });
        if ($dumpSql) {
            $di->getEventsManager()->attach($name, new DatabaseListener());
        }
    }
}
