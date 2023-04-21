<?php

namespace Healthplat\Tool\Commands;

use Healthplat\Tool\Application as MainApplication;
use Phalcon\Mvc\Dispatcher;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * 命令
 * @package Healthplat\Tool\Commands
 */
class Command extends SymfonyApplication
{
    private $app;

    public function __construct(MainApplication $app)
    {
        $this->app = $app;
        $this->registerCommands();
        parent::__construct('hgic');
    }

    /**
     * 注册命令
     * @return void
     */
    public function registerCommands()
    {
        // 通过配置文件定义的命令，可以将其他模块提供的命令添加进来
        $commands = $this->app->di->get('config')->path('app.commands');
        foreach ($commands as $command) {
            $this->add(new $command());
        }
    }
}