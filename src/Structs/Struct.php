<?php

namespace Healthplat\Tool\Structs;

use App\Structs\Requests\TestRequest;
use Healthplat\Tool\Validators\BooleanValidator;
use Healthplat\Tool\Validators\DateValidator;
use Phalcon\Di\Exception;
use Phalcon\Filter\Validation;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;


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
     * 结构体结构
     * @var array
     */
    private $reflections;
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
    private $structName;

    /**
     * 结构体静态构造方法
     * @param null|array|object $data 入参数据类型
     * @param bool $end 将入参赋值之后是否检查必须字段
     * @return static
     */
    public static function factory($data = null)
    {
        return new static($data);
    }

    /**
     * 构造Struct结构体
     * @param null|array|object $data 入参数据类型
     */
    public function __construct($data)
    {
        $reflect = new \ReflectionClass($this);
        $this->structName = $reflect->getName();
        // 检查各字段类型是否格式正确
//        $this->checkParamFormat($reflect);
        // 初始化结构体结构
        $this->reflections = $this->initReflection($reflect);
        // 初始化数据
        $this->initAttributes($data);
        // 检查各个参数类型是否正确
        $this->checkParamType($data);
        // 将数据赋值
//        $this->setData();
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
                    throw new Exception('结构体' . $this->structName . '字段[' . $property->getName() . ']var类型错误，注意(结构体请写完整类名)');
                }
            }
        }
    }

    /**
     * 获取反射结构
     * @param \ReflectionClass $reflect
     * @return array
     * @throws \ReflectionException
     */
    private function initReflection(\ReflectionClass $reflect)
    {
        $reflections = [];
        foreach ($reflect->getProperties() as $property) {
            $comment = $property->getDocComment();
            $thisRreflection = [];
            $name = $property->getName();
            $isRequired = false;
            // 初始化结果
            if (preg_match(self::$commentRegexpValidator, $comment, $ms) > 0) {
                foreach ($ms as $value) {
                    // 查询有没有requied
                    if ($value == 'required') {
                        $isRequired = true;
                    }
                }
            }
            // 获取参数类型
            if (preg_match(self::$commentRegexpType, $comment, $m) > 0) {
                $structType = $this->toSystemType($m[1]);;
                $isArray = $m[2];
//                $object = [];
                $isStruct = false;
                if (is_a($structType, StructInterface::class, true)) {
                    $isStruct = true;
//                    $object = $this->initReflection(new \ReflectionClass($structType));
                }
                $thisRreflection = [
                    'name' => $name,
                    'type' => $structType,
                    'isStruct' => $isStruct,
                    'isArray' => $isArray == '[]' ? true : false,
//                    'object' => $object,
                    'isRequired' => $isRequired
                ];
                // 添加默认值
                $this->attributes[$name] = $this->$name ?: $this->getDefaultData($structType, $thisRreflection['isArray']);
                unset($this->$name);
//                if (is_null($this->$name)) {
//                    $this->$name = $this->getDefaultData($structType, $thisRreflection['isArray']);
//                }
            }
            $reflections[] = $thisRreflection;
        }
        return $reflections;
    }

    /**
     * 初始化数据
     * @param $data
     * @return array
     */
    private function initAttributes($data)
    {
        if (is_a($data, \stdClass::class, true)) {
            $data = json_decode(json_encode($data), true);
        }
        if (!$data) {
            return;
        }
        // 判断数据是否是model
        foreach ($this->reflections as $reflection) {
            $name = $reflection['name'];
            $reflectionType = $reflection['type'];
            if ($reflection['isArray']) {
                $this->attributes[$name] = [];
                // 假如数据正常
                $record = null;
                if (is_object($data)) {
                    $record = $data->$name;
                } else {
                    $record = $data[$name];
                }
                if (isset($record) && (is_array($record) || is_iterable($record))) {
                    foreach ($record as $datum) {
                        if ($reflection['isStruct']) {
                            $this->attributes[$name][] = $reflectionType::factory($datum);
                        } else {
                            $this->attributes[$name][] = $datum;
                        }
                    }
                } else {
                    throw new Exception('结构体' . $this->structName . '的字段[' . $reflection['name'] . ']类型错误，正确类型为array类型', 500);
                }
            } else {
                if ($reflection['isStruct']) {
                    $record = [];
                    if (is_object($data)) {
                        $record = $data->$name;
                    } else if (is_array($data)) {
                        $record = $data[$name];
                    }
                    $this->attributes[$name] = $reflectionType::factory($record);
                } else {
                    // 用对像搜索
                    if (is_object($data) && $data) {
                        $this->attributes[$name] = $data->$name ?? $this->$name;
                    } else {
                        $this->attributes[$name] = $data[$name] ?? $this->$name;
                    }
                }
            }

        }
    }

    /**
     * 检查一个对像的类型
     * @param \ReflectionClass $reflect
     * @return void
     */
    private function checkParamType($data)
    {
        if (is_a($data, \stdClass::class, true)) {
            $data = json_decode(json_encode($data), true);
        }
        if (!$data) {
            return;
        }
        foreach ($this->reflections as $reflection) {
            if ($reflection['isArray']) {
                // 判断数组结构
                if ($reflection['isStruct']) {
                    foreach ($this->attributes[$reflection['name']] as $attribute) {
                        if (!is_a($attribute, StructInterface::class, true)) {
                            throw new Exception('结构体' . $this->structName . '的字段[' . $reflection['name'] . ']类型错误，正确类型为[' . $structType . ']类型', 500);
                        }
                    }
                } else {
                    foreach ($this->attributes[$reflection['name']] as $attribute) {
                        if ($attribute) {
                            $this->checkConditionType($reflection, $attribute);
                        }
                    }
                }
            } else {
                if ($reflection['isStruct']) {
                    if (!is_a($reflection['type'], StructInterface::class, true)) {
                        throw new Exception('结构体' . $this->structName . '的字段[' . $reflection['name'] . ']类型错误，正确类型为[' . $reflection['type'] . ']类型', 500);
                    }
                } else {
                    if ($this->attributes[$reflection['name']]) {
                        $this->checkConditionType($reflection, $this->attributes[$reflection['name']]);
                    }
                }
            }
        }
    }

    /**
     * 检查字段类型
     * @param $reflection
     * @param $attribute
     * @return void
     */
    private function checkConditionType($reflection, $attribute)
    {
        $validate = new Validation();
        $structType = $reflection['type'];
        $name = $reflection['name'];
        if ($reflection['isRequired']) {
            $validate->add($name, new Validation\Validator\PresenceOf([
                'message' => '结构体' . $this->structName . '字段[' . $name . ']为必填，请传入参数'
            ]));
        }
        if ($structType) {
            $validate->add($name, new $this->validatorConfig[$structType]([
                'message' => '结构体' . $this->structName . '字段[' . $name . ']类型错误，正确类型为[' . $structType . ']类型',
            ]));
        }
        $messages = $validate->validate([
            $name => $attribute
        ]);
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
    public function setData()
    {
        foreach ($this->reflections as $reflection) {
            $name = $reflection['name'];
            $thisAttribute = isset($this->attributes[$name]) ? $this->attributes[$name] : null;
            $this->$name = $thisAttribute ?: $this->getDefaultData($thisAttribute, $reflection['type'], $reflection['isArray']);
        }
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



    /**
     * @param string $name
     * @return mixed
     * @throws \Uniondrug\Structs\Exception
     */
    public function & __get($name)
    {
        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }


    /**
     * 获取默认值
     * @param $type
     * @param $isArray
     * @return false|int|string|null
     */
    private function getDefaultData($type, $isArray)
    {
        switch ($type) {
            case 'boolean' :
                return false;
            case 'int':
            case 'integer':
            case 'double' :
                return 0;
            case 'string' :
                return '';
            default:
                if ($isArray) {
                    return [];
                }
                return null;
        }
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
}
