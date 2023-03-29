<?php

namespace Healthplat\Tool\Providers;

use Healthplat\Tool\HttpClient;
use Healthplat\Tool\Response;
use Phalcon\Di\ServiceProviderInterface;

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
        $di->setShared(
            'httpClient',
            function () {
                return new HttpClient();
            }
        );
    }
}
