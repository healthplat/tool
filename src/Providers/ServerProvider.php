<?php

namespace Healthplat\Tool\Providers;

use Healthplat\Tool\Response;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Router;
use Phalcon\Support\HelperFactory;
use Uniondrug\Service\Server;

/**
 * @package App\Providers
 */
class ServerProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'serviceServer',
            function () {
                return new Response();
            }
        );
    }
}
