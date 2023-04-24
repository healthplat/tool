<?php

namespace Healthplat\Tool\Middlewares\Middleware;

use Healthplat\Tool\Middlewares\Middleware;
use Phalcon\Di\Exception;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;

class LogAfterMiddleware extends Middleware
{

    /**
     * @param Dispatcher $dispatcher
     * @return false
     */
    public function run(Dispatcher $dispatcher)
    {
        $requestId = $this->serviceServer->getPhalconResponse()->getHeaders()->get('X-REQUEST-Id');
        $returnResponse = $dispatcher->getReturnedValue();
        $returnValue = '';
        if (is_a($returnResponse, Response::class, true)) {
            $returnValue = $returnResponse->getContent();
        }
        $this->logger->info('请求结束,请求链[' . $requestId . '],请求出参[' . $returnValue . ']');
        return true;
    }
}