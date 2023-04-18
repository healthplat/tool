<?php

namespace Healthplat\Tool\Structs;

class PagingStruct extends Struct
{
    /**
     * @var string
     */
    public $totalItems;

    /**
     * @var string
     */
    public $limit;

    /**
     * @var string
     */
    public $first;

    /**
     * @var string
     */
    public $current;

    /**
     * @var string
     */
    public $next;

    /**
     * @var string
     */
    public $last;

}