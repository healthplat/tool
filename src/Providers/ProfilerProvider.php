<?php

namespace Healthplat\Tool\Providers;

use Phalcon\Db\Profiler;
use Phalcon\Di\ServiceProviderInterface;

class ProfilerProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->getShared('profiler',function (){
            return new Profiler();
        });

    }

}