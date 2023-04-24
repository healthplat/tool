<?php

namespace Healthplat\Tool;

use Healthplat\Tool\Structs\StructInterface;
use Phalcon\Http\Request\Exception;
use Phalcon\Http\Request as PhalconRequest;
use stdClass;
use Phalcon\Http\Response as PhalconResponse;

/**
 * 覆盖HTTP请求
 * @package Healthplat\Tool
 */
class Response
{
    const DATA_TYPE_OBJECT = 'OBJECT';
    const DATA_TYPE_ERROR = 'ERROR';

    private $phalconResponse;

    public function __construct()
    {
        $this->phalconResponse = new PhalconResponse();
    }

    public function getPhalconResponse()
    {
        return $this->phalconResponse;
    }

    /**
     * 返回错误Response
     * @param string $error 错误原因
     * @param int $errno 错误编号
     * @return Response
     */
    public function withError(string $error, $errno = 1)
    {
        if ((int)$errno === 0) {
            $errno = 1;
        }
        // 返回Response
        return $this->response([], static::DATA_TYPE_ERROR, $error, $errno);
    }

    /**
     * 返回成功Response
     * @param array|null $data 数据格式可选
     * @return Response
     */
    public function withSuccess(array $data = null)
    {
        return $this->response(is_array($data) ? $data : [], static::DATA_TYPE_OBJECT, "", 0);
    }

    /**
     * 以StructInterface返回Response
     * @param StructInterface $struct
     * @return Response
     */
    public function withStruct(StructInterface $struct)
    {
        $dataType = static::DATA_TYPE_OBJECT;
        return $this->response($struct->toArray(), $dataType, '', 0);
    }

    /**
     * 原样Object返回
     * @param array $data
     * @return Response
     * @example $obj->serviceServer->withData([
     *     'key' => 'value'
     *     ]);
     */
    public function withData(array $data)
    {
        return $this->response($data, self::DATA_TYPE_OBJECT, '', 0);
    }


    /**
     * 格式化输出结构
     * @param array $data
     * @param string $dataType
     * @param string $error
     * @param int $errno
     * @return Response
     */
    private function response(array $data, string $dataType, $error, $errno)
    {
        // 1. 类型转换
        if (count($data) > 0) {
            $data = $this->parseData($data);
        }
        /**
         * 2. Response
         * @var Response $response
         */
        return $this->phalconResponse->setJsonContent([
            'errno' => (string)$errno,
            'error' => (string)$error,
            'dataType' => $dataType,
            'data' => (object)$data,
        ]);
    }

    /**
     * 类型模糊化
     * @param array $data
     * @return array
     */
    private function parseData(array $data)
    {
        foreach ($data as & $value) {
            $type = strtolower(gettype($value));
            switch ($type) {
                case 'array' :
                    $value = $this->parseData($value);
                    break;
                case 'bool' :
                case 'boolean' :
                    // 2018-08-27: 结构体结果返回支持boolean原始类型
                    // $value = $value ? '1' : '0';
                    break;
                case 'integer' :
                case 'float' :
                case 'double' :
                    $value = (string)$value;
                    break;
                case
                'null' :
                    $value = '';
                    break;
            }
        }
        return $data;
    }
}