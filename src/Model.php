<?php

namespace Healthplat\Tool;
/**
 * Class Model
 */
class Model extends \Phalcon\Mvc\Model
{

    /**
     * Model初始化
     */
    public function initialize()
    {
        // 1. 动态更新
        //    即没有变化的字段在update时不会出现在sql里面。
        //    否则每次都是全字段更新。
        $this->useDynamicUpdate(true);
        // 2. 全局时间
//        //    数据Insert和最后的Update时间
//        $this->addBehavior(new Timestampable([
//            'beforeCreate' => [
//                'field' => [
//                    'gmtCreated',
//                    'gmtUpdated'
//                ],
//                'format' => 'Y-m-d H:i:s',
//            ],
//            'beforeUpdate' => [
//                'field' => 'gmtUpdated',
//                'format' => 'Y-m-d H:i:s',
//            ]
//        ]));
        // Master模式
        $this->setConnectionService("db");
    }

}