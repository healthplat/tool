<?php

namespace Healthplat\Tool\Structs;

class PagingStruct extends Struct
{
    public $totalItems;
    public $limit;
    public $first;
    public $current;
    public $next;
    public $last;

}