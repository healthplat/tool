<?php

namespace Healthplat\Tool\Logics;


use Healthplat\Tool\Structs\StructInterface;

/**
 * @package Healthplat\Tool\Logics
 */
interface LogicInterface
{
    /**
     * @param array|null|object $payload 入参
     *
     * @return array|StructInterface
     */
    public function run($payload);
}
