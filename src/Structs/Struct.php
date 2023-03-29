<?php

namespace Healthplat\Tool\Structs;

use Healthplat\Tool\Validators\BooleanValidator;
use Healthplat\Tool\Validators\DateValidator;
use Phalcon\Di\Exception;
use Phalcon\Filter\Validation;


/**
 * @package Healthplat\Tool\Structs
 */
abstract class Struct implements StructInterface
{
    /**
     * 类型定义匹配
     * @var string
     */
    private static $commentRegexpType = "/@var\s+([_a-z0-9\\\\]+)\s*([\[\]]*)/i";
    /**
     * 验证器定义匹配
     * @var string
     */
    private static $commentRegexpValidator = "/@validator\(([^\)]*)\)/i";
    /**
     * 属性与值关系
     * 即各属性赋值后的结果
     * $attributes = {
     *     'id' => 1,
     *     'sub' => StructInterface{
     *         'id' => 0
     *     }
     * }
     * @var array
     */
    private $attributes = [];
    /**
     * 结构体完整类名
     * <code>
     * $className = '\App\Structs\Results\Module\Row'
     * </code>
     * @var string
     */
    private $className;
    private $validatorConfig = [
        'bool' => \Healthplat\Tool\Validations\Validators\BooleanValidator::class,
        'boolean' => \Healthplat\Tool\Validations\Validators\BooleanValidator::class,
        'float' => \Healthplat\Tool\Validations\Validators\DoubleValidator::class,
        'double' => \Healthplat\Tool\Validations\Validators\DoubleValidator::class,
        'int' => \Healthplat\Tool\Validations\Validators\IntegerValidator::class,
        'integer' => \Healthplat\Tool\Validations\Validators\IntegerValidator::class,
        'string' => \Healthplat\Tool\Validations\Validators\StringValidator::class,
    ];

    /**
     * 结构体静态构造方法
     * @param null|array|object $data 入参数据类型
     * @param bool $end 将入参赋值之后是否检查必须字段
     * @return static
     */
    public static function factory($data = null, $end = true)
    {
        return new static($data, $end);
    }

    /**
     * 构造Struct结构体
     * @param null|array|object $data 入参数据类型
     * @param bool $end 将入参赋值之后是否检查必须字段
     */
    public function __construct($data, $end = true)
    {
        $reflect = new \ReflectionClass($this);
        // 检查各字段类型是否格式正确
        $this->checkParamFormat($reflect);
        // 初始化数据
        $this->initAttributes($reflect, $data);
        // 检查各个参数类型是否正确
        $this->checkParamType($reflect, $this->attributes);
        // 将数据赋值
        $this->setData($reflect);
    }

    /**
     * 检查数据格式
     * @param \ReflectionClass $reflect
     * @return void
     * @throws Exception
     */
    private function checkParamFormat(\ReflectionClass $reflect)
    {
        foreach ($reflect->getProperties() as $property) {
            // 判断对像是否为结构体
            $comment = $property->getDocComment();
            if (preg_match(self::$commentRegexpType, $comment, $m) > 0) {
                $structType = $m[1];
                $isArray = $m[2];
                // 字段是否正常
                if (in_array($structType, array_keys($this->validatorConfig))) {
                    continue;
                } elseif (is_a($structType, StructInterface::class, true)) {
                    $this->checkParamFormat(new \ReflectionClass($structType));
                } else {
                    throw new Exception('字段[' . $property->getName() . ']var类型错误，注意(结构体请写完整类名)');
                }
            }
        }
    }

    /**
     * 初始化数据
     * @param $data
     * @param \ReflectionClass $reflect
     * @return void
     */
    private function initAttributes(\ReflectionClass $reflect, $data)
    {
        // 将对像转换成数组
        $data = json_decode(json_encode($data), true);
        foreach ($reflect->getProperties() as $property) {
            $name = $property->getName();
            // 循环所有对像获取数据
            $this->attributes[$name] = array_key_exists($name, $data) ? $data[$name] : null;
        }
    }

    /**
     * 检查一个对像的类型
     * @param \ReflectionClass $reflect
     * @return void
     */
    private function checkParamType(\ReflectionClass $reflect, $attributes)
    {
        foreach ($reflect->getProperties() as $property) {
            $comment = $property->getDocComment();
            // 获取参数类型
            if (preg_match(self::$commentRegexpType, $comment, $m) > 0) {
                // 配置的类型
                $structType = $m[1];
                $isArray = $m[2];
                // 假如没有配置类型 直接跳过
                if (!$structType) {
                    continue;
                }
                $structType = $this->toSystemType($structType);
                $attrData = array_key_exists($property->getName(), $attributes) ? $attributes[$property->getName()] : null;
                // 假如是结构体就递归判断结构体类型
                if ($isArray == '[]' && $attrData) {
                    $attrData = $attrData ? $attrData : [];
                    foreach ($attrData as $attrDatum) {
                        // 假如是结构体就递归判断结构体类型
                        if (is_a($structType, StructInterface::class, true)) {
                            // 递归判断结构体的字段
                            $this->checkParamType(new \ReflectionClass($structType), $attrDatum);
                        } else {
                            $this->checkConditionType($property, $attrDatum);
                        }
                    }
                } elseif (is_a($structType, StructInterface::class, true)) {
                    // 假如是结构体递归判断
                    $this->checkParamType(new \ReflectionClass($structType), $attributes[$property->getName()]);
                } else {
                    $this->checkConditionType($property, $attributes);
                }
            }
        }
    }

    /**
     * 检查字段类型
     * @param \ReflectionProperty $property
     * @param $attribute
     * @return void
     */
    private function checkConditionType(\ReflectionProperty $property, $attribute)
    {
        $comment = $property->getDocComment();
        // 获取参数类型
        $isRequired = false;
        $structType = null;
        $name = $property->getName();
        if (preg_match(self::$commentRegexpValidator, $comment, $ms) > 0) {
            foreach ($ms as $value) {
                // 查询有没有requied
                if ($value == 'required') {
                    $isRequired = true;
                }
            }
        }
        if (preg_match(self::$commentRegexpType, $comment, $m) > 0) {
            // 配置的类型
            $structType = $m[1];
        }
        $validate = new Validation();
        if ($isRequired) {
            $validate->add($name, new Validation\Validator\PresenceOf([
                'message' => '字段[' . $name . ']为必填，请传入参数'
            ]));
        }
        if ($structType) {
            $validate->add($name, new $this->validatorConfig[$structType]([
                'message' => '字段[' . $name . ']类型错误，正确类型为[' . $structType . ']类型',
            ]));
        }
        $messages = $validate->validate($attribute);
        // 3. 验证过程有错误
        foreach ($messages as $message) {
            throw new Exception($message->getMessage(), 500);
        }
    }

    /**
     * 给对象赋值
     * @param \ReflectionClass $reflect
     * @return void
     */
    public function setData(\ReflectionClass $reflect)
    {
        foreach ($reflect->getProperties() as $property) {
            $name = $property->getName();
            $comment = $property->getDocComment();
            if (preg_match(self::$commentRegexpType, $comment, $m) > 0) {
                // 配置的类型
                $structType = $m[1];
                $isArray = $m[2];
                if (is_a($structType, StructInterface::class, true)) {
                    // 如果是结构体就扶植结构体
                    if ($isArray == '[]') {
                        foreach ($this->attributes[$name] as $attribute) {
                            $this->$name[] = $structType::factory($attribute);
                        }
                    } else {
                        $this->$name = $structType::factory($this->attributes[$name]);
                    }
                } else {
                    $this->$name = $this->attributes[$name];
                }
            }
        }
    }


    /**
     * 入参是否传值
     * @param string $name
     * @return bool
     */
    public function isInput($name)
    {
        return isset($this->requirements[$name]);
    }


    /**
     * 转为数组输出
     * @return array
     */
    public function toArray()
    {
        $data = $this->attributes;
        return $this->parseArray($data);
    }

    public function toJson($options = 0, $depth = 512)
    {
        // TODO: Implement toJson() method.
    }


    /**
     * 简写类型转标准类型名称
     * @param string $type
     * @return string
     * @example $this->toSystemType('int'); // integer
     */
    private function toSystemType(string $type)
    {
        switch ($type) {
            case 'bool' :
                $type = 'boolean';
                break;
            case 'float' :
                $type = 'double';
                break;
            case 'int' :
                $type = 'integer';
                break;
            case 'str' :
                $type = 'string';
                break;
        }
        return $type;
    }


    /**
     * 以递归模式将结构转为数组
     * @param array $data
     * @return array
     */
    private function parseArray($data)
    {
        foreach ($data as $name => & $value) {
            if (is_array($value)) {
                $value = $this->parseArray($value);
            } else if (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }
        }
        return $data;
    }
}
