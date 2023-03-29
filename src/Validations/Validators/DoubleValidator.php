<?php
/**
 * 框架级Validator
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-01-05
 */

namespace Healthplat\Tool\Validations\Validators;

use Phalcon\Filter\Validation\Validator\Numericality;
use Phalcon\Validation\Message;

/**
 * 验证整型值
 * @package Pails\Validators
 */
class DoubleValidator extends Numericality
{

    /**
     * Constructor
     *
     * @param array $options = [
     *     'message' => '',
     *     'template' => '',
     *     'with' => '',
     *     'labelWith' => '',
     *     'ignoreCase' => false
     * ]
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }
}