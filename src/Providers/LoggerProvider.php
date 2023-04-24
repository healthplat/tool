<?php

namespace Healthplat\Tool\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\Line;
use Phalcon\Logger\Logger;


/**
 * 日志注入Provider
 */
class LoggerProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->set(
            'logger',
            function ($logCategory = 'app') use ($di){
                $month = date('Y-m');
                $date = date('Y-m-d');
                if ($di->getConfig()->path('logger.splitDir', false)) {
                    $logPath = LOG_PATH . '/' . $logCategory . '/' . $month;
                    $logFile = $logPath . '/' . $date . '.log';
                } else {
                    $logPath = LOG_PATH . '/' . $logCategory;
                    $logFile = $logPath . '/' . $date . '.log';
                }
                try {
                    if (!file_exists($logPath)) {
                        mkdir($logPath, 0755, true);
                    }
                } catch (\Throwable $e) {
                    // skip. multi process may try to make dir at the same time. just skip errors.
                }
                $formatter = new Line();
                $formatter->setDateFormat('Y-m-d H:i:s');
                $adapter = new Stream($logFile);
                $adapter->setFormatter($formatter);
                $logger = new Logger(
                    'messages',
                    [
                        'main' => $adapter,
                    ]
                );
                return $logger;
            }
        );


    }
}