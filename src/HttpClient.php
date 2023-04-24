<?php

namespace Healthplat\Tool;

use Exception;
use Phalcon\Di\Di;
use Redis;
use RedisException;
use RuntimeException;

/**
 * Class HttpClient
 * @package Healthplat\Tool
 */
class HttpClient extends \GuzzleHttp\Client
{
    private $container;

    private $options;

    const SLOW_SECONDS = 1;

    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function request($method, $uri = '', array $options = [])
    {
        $this->container = Di::getDefault();
        $begin = microtime(true);
        $method = strtoupper($method);
        // 初始化别的值
        $this->initOptions($options);
        $this->container->get('logger')->info(sprintf("HttpClient以{%s}请求{%s}开始 - %s", $method, $uri, json_encode($this->options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        // 3. Request Progress
        $error = null;
        try {
            $response = parent::request($method, $uri, $this->options);
            return $response;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            throw $e;
        } finally {
            $duration = (double)(microtime(true) - $begin);
            if ($error !== null) {
                $this->container->get('logger')->error(sprintf("[d=%.06f]HttpClient以{%s}请求{%s}出错 - %s", $duration, $method, $uri, $error));
            } else if ($duration >= self::SLOW_SECONDS) {
                $this->container->get('logger')->warning(sprintf("[d=%.06f]HttpClient以{%s}请求{%s}较慢, 超过{%s}秒阀值", $duration, $method, $uri, self::SLOW_SECONDS));
            }
            if (isset($response) && $response) {
                $this->container->get('logger')->info(sprintf("[d=%.06f]HttpClient以{%s}请求{%s}完成,返回结果：{%s}", $duration, $method, $uri, $response->getBody()->getContents()));
            }
        }
    }

    /**
     * 初始化三方值
     * @param $options
     * @return void
     */
    private function initOptions($options)
    {
        $this->options = is_array($options) ? $options : [];
        $this->options['headers'] = isset($options['headers']) && is_array($options['headers']) ? $options['headers'] : [];
        // 添加requestId
        $requestId = $this->container->get('serviceServer')->getPhalconResponse()->getHeaders()->get('X-REQUEST-Id');
        $this->options['headers']['X-REQUEST-Id'] = $requestId;
    }
}