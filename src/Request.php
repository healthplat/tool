<?php

namespace Healthplat\Tool;

use Phalcon\Http\Request\Exception;
use Phalcon\Http\Request as PhalconRequest;
use stdClass;

/**
 * 覆盖HTTP请求
 * @package Healthplat\Tool
 */
class Request extends PhalconRequest
{
    /**
     * 读取客户端IP地址
     * @param null $trustForwardedHeader
     * @return string
     */
    public function getClientAddress($trustForwardedHeader = null)
    {
        /**
         * 提取://X-REAL-IP
         */
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $realIp = trim($_SERVER['HTTP_X_REAL_IP']);
            if ($realIp !== '') {
                return $realIp;
            }
        }
        /**
         * 提取://Phalcon
         */
        return parent::getClientAddress($trustForwardedHeader);
    }

    /**
     * 读取请求入参数据
     * @param boolean $associative
     * @return array|stdClass
     */
    public function getJsonRawBody($associative = null)
    {
        $body = $this->getRawBody();
        if (!$body) {
            return [];
        }
        // 1. 转为Array/StdClass
        $data = json_decode($body, $associative);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        // 2. Array错误
        if ($associative) {
            throw new \Exception('入参格式错误，请确保是json类型', 500);
        }
        // 3. JSON错误
        $data = new stdClass();
        $data->_err = json_last_error_msg();
        $data->_raw = $body;
        return $data;
    }

    /**
     * Set request raw body
     * @param null $body
     * @return $this
     */
    public function setRawBody($body = null)
    {
        $this->_rawBody = $body;
        return $this;
    }
}
