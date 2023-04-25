<?php

namespace Healthplat\Tool\Providers;

use Phalcon\Di\ServiceProviderInterface;

class ProfilerProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared('profiler', function () {
            return new \Phalcon\Db\Profiler();
        });
    }

}