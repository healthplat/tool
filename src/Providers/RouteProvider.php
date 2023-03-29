<?php

namespace Healthplat\Tool\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Router;
use Phalcon\Support\HelperFactory;

/**
 * @package App\Providers
 */
class RouteProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'router',
            function () {
                // 启用注解路由，此时默认路由关闭
                $router = new Router\Annotations(false);
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(getAppPath() . DIRECTORY_SEPARATOR . 'Controllers'), \RecursiveIteratorIterator::SELF_FIRST);

                foreach ($iterator as $item) {
                    $helper = new HelperFactory();
                    if ($helper->endsWith($item, 'Controller.php', false)) {
                        $name = str_replace([getAppPath() . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR, 'Controller.php'], '', $item);
                        if ($name) {
                            $name = str_replace(DIRECTORY_SEPARATOR, '\\', $name);
                            $router->addResource('App\\Controllers\\' . $name);
                        }
                    }
                }
                $router->removeExtraSlashes(true);
                $router->setDefaultNamespace('App\\Controllers');
                $router->setDefaultController('index');
                $router->setDefaultAction('index');
                return $router;
            }
        );
    }
}
