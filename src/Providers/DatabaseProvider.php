<?php

namespace Healthplat\Tool\Providers;

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
//    public function register(\Phalcon\Di\DiInterface $di): void
//    {
//        /**
//         * 1. reset instance
//         * @var Container $di
//         */
//        $config = $di->getConfig()->path('database.connection');
//        if (!($config instanceof Config)) {
//            return;
//        }
//        $adapter = $di->getConfig()->path('database.adapter');
//        $name = 'db';
//        $pdo = $this->pdo[$adapter];
//        $di->setShared($name, function () use ($di, $config, $name, $pdo) {
//            unset($config->adapter);
//            $dn = isset($config->dbname) ? $config->dbname : 'unknown';
//            $db = new $pdo($config->toArray());
//            return $db;
//        });
//    }
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        /**
         * 1. reset instance
         * @var Container $di
         */
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
            if ($dumpSql) {
                $eventsManager = new Manager();
                $profiler = $di->getProfiler();
                // 注册事件，以捕获执行的 SQL 查询
                $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
                    if ($event->getType() == 'beforeQuery') {
                        $profiler->startProfile(
                            $connection->getSQLStatement(),
                            $connection->getSQLVariables(),
                            $connection->getSQLBindTypes()
                        );
                    }
                    if ($event->getType() == 'afterQuery') {
                        $profiler->stopProfile();
                        $requestId = Di::getDefault()->get('serviceServer')->getPhalconResponse()->getHeaders()->get('X-REQUEST-Id');
                        $profiler = Di::getDefault()->getProfiler();
                        // 获取所有记录的查询
                        $profiles = $profiler->getProfiles();
                        foreach ($profiles as $profile) {
                            // 获取查询的 SQL 语句、参数和绑定类型
                            $sql = $profile->getSQLStatement();
                            $params = $profile->getSQLVariables();
                            // 获取查询的执行时间
                            $time = $profile->getTotalElapsedSeconds();
                            if ($time > 1) {
                                // 将查询写入日志文件
                                Di::getDefault()->getLogger('app')->info(sprintf('请求结束,请求链[' . $requestId . '], 请求超时- %s (%s s): %s', $sql, $time, json_encode($params)));
                            } else {
                                // 将查询写入日志文件
                                Di::getDefault()->getLogger('app')->info(sprintf('请求结束,请求链[' . $requestId . '], %s (%s s): %s', $sql, $time, json_encode($params)));
                            }
                        }
                    }
                });
                $db->setEventsManager($eventsManager);
            }
            return $db;
        });
    }
}
