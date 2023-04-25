<?php

namespace Healthplat\Tool\Middlewares\Middleware;

use Healthplat\Tool\Middlewares\Middleware;
use Phalcon\Di\Exception;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;

class SqlMiddleware extends Middleware
{

    /**
     * @param Dispatcher $dispatcher
     * @return false
     */
    public function run(Dispatcher $dispatcher)
    {
        $profiler = $this->di->getProfiler();
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
                $this->di->getLogger('sql-out')->info(sprintf('%s (%s s): %s', $sql, $time, json_encode($params)));
            } else {
                // 将查询写入日志文件
                $this->di->getLogger('sql')->info(sprintf('%s (%s s): %s', $sql, $time, json_encode($params)));
            }
        }
        return true;
    }
}