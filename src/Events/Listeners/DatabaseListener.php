<?php
namespace Healthplat\Tool\Events\Listeners;

use Healthplat\Tool\Mysql;
use Phalcon\Db\Profiler;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;


/**
 * DB查询过程
 * @package Healthplat\Tool\Events\Listeners
 */
class DatabaseListener extends Injectable
{
    /**
     * @var Profiler
     */
    protected $profiler;

    /**
     * DatabaseListener constructor.
     */
    public function __construct()
    {
        $this->profiler = new Profiler();
    }

    /**
     * SQL完成执行后
     * @param Event $event
     * @param Connection $connection
     */
    public function afterQuery(Event $event, $connection)
    {
        /**
         * @var Profiler\Item $profile
         */
        $this->profiler->stopProfile();
        $profile = $this->profiler->getLastProfile();
        $duration = (double)$profile->getTotalElapsedSeconds();
        // 1. logger内容
        if ($connection instanceof Mysql) {
            $sql = $connection->getListenerSQLStatment();
        } else {
            $sql = $connection->getSQLStatement();
        }
        if (strpos($sql, 'FROM `INFORMATION_SCHEMA`') !== false || strpos($sql, 'SHOW FULL COLUMNS FROM') !== false) {
        } else {
            $requestId = $this->request->getHeader('X-REQUEST-Id');
            $this->logger->info("请求链[$requestId],SQL执行时间[".$duration."],SQL完成 - " . $sql);
        }
    }

    /**
     * SQL开始执行前
     * @param Event $event
     * @param Connection $connection
     */
    public function beforeQuery(Event $event, $connection)
    {
        $this->profiler->startProfile($connection->getSQLStatement());
    }
}
