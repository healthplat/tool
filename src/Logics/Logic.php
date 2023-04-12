<?php

namespace Healthplat\Tool\Logics;

use Healthplat\Tool\Services\ServiceTrait;
use stdClass;

/**
 * 业务逻辑抽像
 * @package Healthplat\Tool\Logics
 */
abstract class Logic extends \Phalcon\Di\Injectable implements LogicInterface
{
    use ServiceTrait;

    public $userId;

    /**
     * 结构体工厂
     * @param array|stdClass|null $payload
     * @return mixed
     */
    public static function factory($payload)
    {
        // 1. new实例
        $logic = new static();
        // 2. run过程
        $logic->beforeRun();
        $result = $logic->run($payload);
        $logic->afterRun($result);
        // 4. 最后返回结果
        return $result;
    }


    /**
     * run()运行之后
     * @param mixed $result 值为run()方法的出参结果
     */
    public function afterRun($result)
    {
    }

    /**
     * 运行run()方法之前
     */
    public function beforeRun()
    {
        if (!empty($_SERVER['HTTP_X_USERID'])) {
            $this->userId = $_SERVER['HTTP_X_USERID'];
        }
    }
}
