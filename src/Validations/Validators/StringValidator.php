<?php
/**
 * 框架级Validator
 *
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-01-05
 */

namespace Healthplat\Tool\Validations\Validators;


use Phalcon\Filter\Validation\Validator\Callback;
use Phalcon\Filter\Validation\Validator\StringLength;

/**
 * 验证字符串
 * <code>
 * $validation = new Validation();                  // 创建Validation实例
 * $attribute = 'field';                            // 参数名称
 * $options = [                                     // 验证选项
 *     'required' => true,                          // 是否必填
 *     'empty' => false,                            // 是否允许为空
 *     'options' => [                               // 限定字符串
 *         'enable',
 *         'disable'
 *     ],
 *     'min' => 10,                                 // 最少10个字符(UTF8以一个中文3个字符)
 *     'minChar' => 3,                              // 最少 3个文字(1个中文、数字、字母都算为1个字)
 *     'max' => 30,                                 // 最多30个字符(UTF8以一个中文3个字符)
 *     'maxChar' => 10                              // 最少10个文字(1个中文、数字、字母都算为1个字)
 * ];
 * $validator = new StringValidator($options);
 * $validation->add($attribute, $validator);
 * $validation->validate();
 * </code>
 *
 * @package Pails\Validators
 */
class StringValidator extends Callback
{
    /**
     * Executes the validation
     *
     * @param \Phalcon\Filter\Validation $validation
     * @param mixed $field
     * @return bool
     */
    public function validate(\Phalcon\Filter\Validation $validation, $field): bool
    {
        return true;
    }
}
