<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-12-25
 */
namespace Healthplat\Tool\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;

/**
 * 初始化系统配置
 * @package Uniondrug\Framework\Providers
 */
class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @param \Phalcon\DiInterface $di
     */
    public function register(\Phalcon\Di\DiInterface $di) : void
    {
    }
}
