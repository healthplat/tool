<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-12-25
 */

namespace Healthplat\Tool\Providers;
use Phalcon\Config\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Support\HelperFactory;

/**
 * 初始化系统配置
 * @package Uniondrug\Framework\Providers
 */
class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @param \Phalcon\DiInterface $di
     */
    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared('config', function () {
            $env = env('APP_ENV', 'development');
            $config = new Config([]);
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(getConfigPath()), \RecursiveIteratorIterator::SELF_FIRST);
            $helper = new HelperFactory();
            foreach ($iterator as $item) {
                if ($helper->endsWith($item, '.php', false)) {
                    $name = str_replace([
                        getConfigPath() . DIRECTORY_SEPARATOR,
                        '.php'
                    ], '', $item);
                    $data = include $item;
                    // 默认配置组
                    if (is_array($data) && isset($data['default']) && is_array($data['default'])) {
                        $config[$name] = $data['default'];
                    }
                    // 非空初始化
                    if (!isset($config[$name])) {
                        $config[$name] = [];
                    }
                    // 指定环境的配置组，覆盖默认配置
                    if (is_array($data) && isset($data[$env]) && is_array($data[$env])) {
                        $config->merge(new Config([$name => $data[$env]]));
                    }
                }
            }
            return $config;
        });
    }
}
