<?php

namespace Healthplat\Tool;


use Phalcon\Di\Di;
use Phalcon\Di\Exception;

/**
 * Class CodeData
 */
class CodeData
{

    private $timeout;
    private $options;
    private $code;
    private $codDir = "APP:CODE:";

    /**
     * 生成Code
     * @param $timeout 超时时间
     * @param $options kv数组
     * @return code
     * @throws CodeDataException
     */
    public function set($options, $timeout = 10)
    {

        $this->timeout = $timeout > 0 ? $timeout : $this->timeout;
        if (empty(is_array($options))) {
            throw new Exception("主体消息非数组", 500);
        }
        foreach ($options as $val) {
            if (empty(is_array($val)) || empty(isset($val["key"])) || empty(isset($val["value"]))) {
                throw new \Exception("value格式错误", 500);
            }
            if (count($val) != 2) {
                throw new \Exception("value格式错误", 500);

            }
        }
        $this->options = json_encode($options, JSON_UNESCAPED_UNICODE);
        $this->code = $this->uuid();
        $key = $this->codDir . $this->code;
        try {
            Di::getDefault()->get("redis")->set($key, $this->options, $this->timeout);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->code;
    }

    /**
     * 获取Code
     * @param $code
     * @return options
     * @throws CodeDataException
     */
    public function get($code)
    {
        $this->code = $code;
        $key = $this->codDir . $this->code;
        $codeVal = Di::getDefault()->get("redis")->get($key);
        if (empty($codeVal)) {
            throw new Exception("获取code值为空", 500);
        }
        $this->options = json_decode($codeVal, TRUE);
        Di::getDefault()->get("redis")->del($key);
        return $this->options;

    }

    /**
     * 生成uuid
     */
    public function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        return $prefix . $uuid;
    }

}