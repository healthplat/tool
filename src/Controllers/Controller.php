<?php

namespace Healthplat\Tool\Controllers;

use Healthplat\Tool\Services\ServiceTrait;

/**
 * 控制器
 * @package Healthplat\Tool\Controllers
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{
    use ServiceTrait;
}