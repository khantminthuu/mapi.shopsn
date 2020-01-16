<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\TraitClass;

use Common\Tool\Extend\ArrayChildren;
use Think\Model;

/**
 * 添加属性
 * 
 * @author 王强
 * @version 1.0
 */
trait AddFieldTrait
{

    /**
     * 表字段后缀
     * @param  string
     */
    private $suffixByDbField = '_';

    /**
     * 要写的文件
     * @param string
     */
    private $filePathName;

    /**
     * 实现 类的静态属性添加【代码】【核心】
     */
    private final function autoAddProp()
    {
        $this->throwError();
        
        try {
            $obj = new \ReflectionObject($this);
            
            $staticProp = $obj->getStaticProperties();
            
            $addField = array();
            
            $dbField = $this->noCacheTableData();
            
            $this->error($dbField);
            
            $this->filePathName = $obj->getFileName();
            
            // 截取子类模型数据库属性字段 【因为 子类可能有其他的属性字段】
            
            $dbFileds = $this->getSplitUnset($staticProp);
            
            if (empty($dbFileds)) {
                return self::rewriteModel($dbField);
            }
            
            $dbFieldNumber = count($dbField);
            
            $classPropNumber = count($dbFileds);
           
            // 是否有新添加得字段
            $diff = $dbFieldNumber - $classPropNumber;
            
            if ($diff === 0) {
                return false; // 不用添加
            } else {
                // 由于索引 从 0开始
                // 筛选 要添加得字段
                $addField = $this->screenField($dbField, $classPropNumber, $dbFieldNumber);
            }
            
            $status = false;
            if (! empty($addField)) {
                $status = self::rewriteModel($addField);
            }
            return $status;
        } catch (\Exception $e) {
            $e->getTrace();
        }
    }

    /**
     * 写文件 不允许外部任何文件调用【核心】
     * @param array $addField 表字段
     * @throws \Exception
     * @return boolean;
     */
    private final function rewriteModel(array $addField)
    { 
        $line = self::getLineNum(self::$find, true); 
        $classData = array();
        $startString = "\n\tpublic static \$";
        $status = false;
        $i = - 2;
        $newString = $noString = null;
        
        $length = false;
        
        $suffix = $this->suffixByDbField;
        // 倒序排序
       
        $addField = (new ArrayChildren($addField))->rsort();
        
        foreach ($addField as $key => & $value) {
            
            if (empty($value['field'])) {
                throw new \Exception('在崩溃的边缘');
            }
            
            $length = strpos($value['field'], $suffix);
            $i ++;
            if ($length !== false) {
                $endString = ucfirst(substr($value['field'], $length + 1)) . self::SUFFIX . ";\t//" . $value['comment'] . "\n\n";
                
                $newString = $startString . strchr($value['field'], $suffix, true);
                
                $classData = self::insertContent($newString . $endString, $line + $i);
                $i --;
                $status = self::rewriteFile($classData);
            } else {
                
                $noString = $startString . $value['field'] . self::SUFFIX . ";\t//" . $value['comment'] . "\n\n";
                
                $classData = self::insertContent($noString, $line + $i);
                $i --;
                $status = self::rewriteFile($classData);
            }
        }
        
        return $status;
    }

    /**
     * 为子类中的数据库属性字段赋值【核心】
     * 
     * @param Model $model
     *            子类模型
     * @param string $suffix
     *            数据表字段后缀
     * @return
     *
     */
    private final function setDbFileds()
    {
        $this->throwError();
        
        try {
            // 反射类中的数据库属性
            $reflection = new \ReflectionObject($this);
            $staticProperties = $reflection->getStaticProperties();
            
            if (empty($staticProperties)) {
                throw new \ErrorException('该模型没有对应的字段');
            }
            
            // 截取子类模型数据库属性字段
            
            $dbFileds = $this->getSplitUnset($staticProperties);
            
            // 获取数据库的字段
            $dbData = $this->getDbFields();
            
            // 如果此数据表没有字段 ，那么抛出异常
            $this->error($dbData, $this);
            
            // 获取字段数量
            $flag = count($dbData);
            // 标记变量
            $i = 0;
            foreach ($dbFileds as $key => &$value) {
                // 利用了 可变变量的特性
                $this::$$key = $dbData[$i];
                $i ++;
                // 如果 标记变量 大于 数据表的字段数量 就结束循环
                if ($i > $flag - 1) {
                    break;
                }
            }
        } catch (\Think\Exception $e) {
            throw new \ErrorException('该模型不匹配基类模型');
        }
    }

    private function error($data)
    {
        if (empty($data)) {
            throw new \Exception('该模型【' . get_class($this) . '】对应的数据表无字段');
        }
    }

    /**
     * 抛出异常
     * 
     * @param Model $model
     *            基类模型
     * @return \Throwable
     */
    private function throwError()
    {
        if (! ($this instanceof Model)) {
            throw new \Exception('模型不匹配');
        }
    }

    /**
     * 从数组中去除字段
     * 
     * @param array $array
     *            字段数组
     * @param string $split
     *            字段后缀
     * @return array
     */
    private function getSplitUnset(array $array, $split = '_d')
    {
        if (empty($array)) {
            return array();
        }
        
        foreach ($array as $key => & $value) {
            if (false === strpos($key, $split)) {
                unset($array[$key]);
            }
        }
        
        return $array;
    }

    /**
     * 筛选出不同的字段【核心】
     * 
     * @param array $array
     *            要筛选的数据
     * @param int $classPropNum
     *            类中数据表字段的数量
     * @param int $dbFieldNumber
     *            数据表的字段数量
     * @return array
     */
    private function screenField(array $array, $classPropNum, $dbFieldNumber)
    {
        if (empty($array) || ! is_array($array) || ! is_int($classPropNum) || ! is_int($dbFieldNumber)) {
            return array();
        }
        
        $sub = $dbFieldNumber - $classPropNum;
        
        // 开始循环的地方
        $start = $dbFieldNumber - $sub;
        
        $parseDbField = array();
        $i = 0;
        for ($i = $start; $i < $dbFieldNumber; $i ++) {
            $parseDbField[] = $array[$i];
        }
       
        unset($dbFieldNumber);
        
        return $parseDbField;
    }

    /**
     * 指定行 插入代码【核心】
     * 
     * @param string $addByThis
     *            要添加得代码
     * @param int $iLine
     *            要添加到的行数
     * @param int $index
     *            为第几个字符之前，默认0
     * @return array
     */
    private function insertContent($addByThis, $iLine, $index = 0)
    {
        $source = $this->filePathName;
        if (! is_file($source)) {
            return array();
        }
        
        $file_handle = fopen($source, "r");
        $i = 0;
        $arr = array();
        while (! feof($file_handle)) {
            $line = fgets($file_handle);
            ++ $i;
            if ($i == $iLine) {
                if ($index == strlen($line) - 1) {
                    $arr[] = substr($line, 0, strlen($line) - 1) . $addByThis;
                } else {
                    $arr[] = substr($line, 0, $index) . $addByThis . substr($line, $index);
                }
            } else {
                $arr[] = $line;
            }
        }
        fclose($file_handle);
        return $arr;
    }

    /**
     * 获取某段内容的行号【核心】
     * 
     * @param string $filePath
     *            文件路径
     * @param string $target
     *            待查找字段
     * @param bool $first
     *            是否再匹配到第一个字段后退出
     * @return array
     */
    private function getLineNum($target, $first = false)
    {
        $filePath = $this->filePathName;
        self::isFile();
        
        $fp = fopen($filePath, "r");
        $lineNumArr = array();
        $lineNum = 0;
        $flag = 0;
        while (! feof($fp)) {
            $lineNum ++;
            $lineCont = fgets($fp);
            if (! strstr($lineCont, $target)) {
                continue;
            }
            
            $flag = 1;
            if ($first) {
                return $lineNum;
            } else {
                $lineNumArr[] = $lineNum;
            }
        }
        // 或者这里 抛出 找不到数据所在行 $flag 标记变量
        if (empty($lineNumArr) || $flag === 0) {
            throw new \Exception('没有找到 数据所在行');
        }
        return $lineNumArr;
    }

    /**
     * 文件是否存在
     */
    private function isFile()
    {
        if (! is_file($this->filePathName)) {
            throw new \Exception('文件不存在');
        }
    }

    /**
     * 重写文件【核心】
     * 
     * @param string $file
     *            文件路径
     * @param array $fileContent
     *            要写入的内容；
     * @return bool;
     */
    private function rewriteFile(array $fileContent)
    {
        $file = $this->filePathName;
        
        self::isFile();
        
        if (empty($fileContent) || ! is_array($fileContent)) {
            throw new \Exception('内容不能为空，且只能是数组');
        }
        // 清空
        file_put_contents($file, null);
        $status = false;
        
        foreach ($fileContent as $value) {
            $status = file_put_contents($file, $value, FILE_APPEND);
        }
        
        return $status;
    }
}