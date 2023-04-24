<?php

namespace Healthplat\Tool\Middlewares\Middleware;

use Healthplat\Tool\Middlewares\Middleware;
use Phalcon\Di\Exception;
use Phalcon\Mvc\Dispatcher;

class LogMiddleware extends Middleware
{
    /**
     * 生成requestId
     * @return string
     */
    public function requestId()
    {
        $tm = explode(' ', microtime(false));
        return sprintf("%s%s%s%d%d", 'a', $tm[1], (int)($tm[0] * 1000000), mt_rand(10000000, 99999999), mt_rand(1000000, 9999999));
    }

    /**
     * @param Dispatcher $dispatcher
     * @return false
     */
    public function run(Dispatcher $dispatcher)
    {
        $requestId = $this->request->getHeader('X-REQUEST-Id') ?: $this->requestId();
        $this->serviceServer->getPhalconResponse()->setHeader('X-REQUEST-Id', $requestId);
        $host = $this->request->getHttpHost() . $this->request->getURI();
        $condition = (array)$this->request->getJsonRawBody();
        $this->logger->info('开始请求接口[' . $host . '],请求链[' . $requestId . '],请求入参[' . json_encode($condition) . ']');
        return true;
    }
}