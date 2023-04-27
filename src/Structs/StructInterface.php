<?php

namespace Healthplat\Tool\Structs;

/**
 * @package Healthplat\Tool\Structs
 */
interface StructInterface
{
    /**
     * 结构体静态构造方法
     * @param null|array|object $data 入参数据类型
     * @return static
     */
    public static function factory($data = null);

    /**
     * 处理之后的数据
     * @return mixed
     */
    public function afterFactory();


    /**
     * 转换成数组结构
     * @return array
     */
    public function toArray();
}
