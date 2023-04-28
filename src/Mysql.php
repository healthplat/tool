<?php

namespace Healthplat\Tool;
/**
 * Class Mysql
 */
class Mysql extends \Phalcon\Db\Adapter\Pdo\Mysql
{
    /**
     * 读取完整SQL语句
     * 在Logger中写入SQL完整语句时, 按SQL入参替换占位符
     * 实现完整SQL渲染
     * @return string
     */
    public function getListenerSQLStatment()
    {
        // 1. PDOSqlStatment
        //    含占位符的SQL语句
        $sql = $this->getSQLStatement();
        // 2. 占位符数据集
        $vars = $this->getSqlVariables();
        if (!is_array($vars)) {
            return $sql;
        }
        // 3. 替换占位符
        foreach ($vars as $key => $value) {
            // 4. 非数字值处理
            if (!is_numeric($value)) {
                if (is_string($value)) {
                    $value = addslashes(stripslashes($value));
                } else if (is_null($value)) {
                    $value = null;
                } else {
                    $value = '{{'.gettype($value).'}}';
                }
            }
            // 5. 替换占位符
            if (is_numeric($key) && $key >= 0) {
                // 6. 问号'?'占位符
                $sql = preg_replace_callback("/([,\(]\s*)\?(\s*[,|\)])/", function($a) use ($key, $value){
                    if ($value === null) {
                        return $a[1]."NULL".$a[2];
                    }
                    return $a[1]."'{$value}'".$a[2];
                }, $sql, 1);
            } else {
                // 7. 字符占位符
                //    a): APL0
                //    b): id
                if ($value === null) {
                    $sql = str_replace(":{$key}", "NULL", $sql);
                } else {
                    $sql = str_replace(":{$key}", "'{$value}'", $sql);
                }
            }
        }
        // 8. 返回SQL结果
        return $sql;
    }
}