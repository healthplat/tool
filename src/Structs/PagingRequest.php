<?php

namespace Healthplat\Tool\Structs;

class PagingRequest extends Struct
{
    /**
     * 请求页码
     * @var int
     * @Validator(options={min:1})
     */
    public $page = 1;

    /**
     * 每页数量
     * @var int
     * @Validator(options={min:1,max:1000})
     */
    public $limit = 10;
}