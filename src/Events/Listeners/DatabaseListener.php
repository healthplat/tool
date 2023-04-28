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
     * @param int|null $error
     * @param int|null $warning
     */
    public function __construct(int $error = null, int $warning = null)
    {
        $this->profiler = new Profiler();
        $error === null || $this->durationError = (double)($error / 1000);
        $warning === null || $this->durationWarning = (double)($warning / 1000);
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
            $sql = $connection->getSQLStatement().','.json_encode($connection->getSqlVariables());
        } else {
            $sql = $connection->getSQLStatement();
        }
        if (strpos($sql, 'FROM `INFORMATION_SCHEMA`') !== false || strpos($sql, 'SHOW FULL COLUMNS FROM') !== false) {
            return false; // 返回false表示不执行该SQL语句
        } else {
            $this->logger->info("SQL完成-SQL执行时间[".$duration."]," . $sql);
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
