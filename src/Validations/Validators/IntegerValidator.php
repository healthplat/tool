<?php
namespace Healthplat\Tool\Validations\Validators;

use Phalcon\Filter\Validation\Validator\Numericality;
use Phalcon\Validation\Message;

/**
 * 验证整型值
 * @package Pails\Validators
 */
class IntegerValidator extends Numericality
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
        $options['onlyInteger'] = true;
        $this->options = $options;
    }
}