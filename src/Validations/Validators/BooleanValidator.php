<?php
/**
 * 框架级Validator
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-08-27
 */
namespace Healthplat\Tool\Validations\Validators;


use Phalcon\Filter\Validation\Validator\Confirmation;

/**
 * Boolean类型
 * @package Pails\Validators
 */
class BooleanValidator extends Confirmation
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
