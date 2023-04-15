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
     * @param bool              $end  将入参赋值之后是否检查必须字段
     * @return static
     */
    public static function factory($data = null);

    /**
     * 转换成数组结构
     * @return array
     */
    public function toArray();
}
