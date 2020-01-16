<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
namespace Common\Tool\Extend;

use Common\Tool\Tool;

/**
 * 数组操作类
 */
class ArrayParse implements \ArrayAccess, \Reflector
{

    private $array = array();

    protected $children = array();

    protected $parseChildren = array();

    public static $childrenClass = null;

    /**
     *
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetExists()
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function __set($name, $value)
    {
        if (! isset($this->array[$name])) {
            $this->array[$name] = $value;
        }
    }

    public function __get($name = null)
    {
        return isset($this->array[$name]) ? $this->array[$name] : $this->array;
    }

    public function offsetExists($offset)
    {
        // TODO Auto-generated method stub
        return isset($this->array[$offset]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        // TODO Auto-generated method stub
        return $this->array[$offset];
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        // TODO Auto-generated method stub
        $this->array[$offset] = $value;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        // TODO Auto-generated method stub
        unset($this->array[$index]);
    }

    /**
     * 组合数据
     */
    public function buildData(array $data = null)
    {
        $data = empty($data) ? $this->array : $data;
        
        // 阵列变量
        extract($data);
        
        foreach ($children as $key => &$value) {
            
            foreach ($configValue as $config => $nameValue) {
                if (array_key_exists($value['type_name'], $nameValue)) {
                    $value['value'] = $nameValue[$value['type_name']];
                }
            }
        }
        
        // 转换序号
        
        $obj = new ArrayChildren($pData);
        
        $pData = $obj->convertIdByData('id');
        
        $classId = '';
        
        foreach ($configValue as $config => $nameValue) {
            $classId = $nameValue['class_id'];
            if (! array_key_exists($classId, $pData) || ($pData[$classId]['p_id'] != 0)) {
                continue;
            }
            
            $pData[$classId]['parent_key'] = $nameValue['parent_key'];
        }
        
        $obj->setData($children);
        
        $children = $obj->convertIdByData('config_class_id');
        
        foreach ($pData as $type => & $name) {
            
            if (! array_key_exists($type, $children)) {
                continue;
            }
            $name['type_name'] = $children[$type]['type_name'];
            $name['show_type'] = $children[$type]['show_type'];
            $name['type'] = $children[$type]['type'];
            $name['value'] = $children[$type]['value'];
        }
        return $pData;
    }

    /**
     * 分析系统配置
     */
    public function buildConfig(array $data = null)
    {
        $data = empty($data) ? $this->array : $data;
        // 阵列变量
        extract($data);
        foreach ($children as $key => &$value) {
            foreach ($configValue as $config => $nameValue) {
                if (array_key_exists($value['type_name'], $nameValue)) {
                    $value['value'] = $nameValue[$value['type_name']];
                }
            }
        }
        $this->children = $children;
        return $this;
    }

    public function parseConfig()
    {
        if (empty($this->children)) {
            return $this;
        }
        $data = $this->children;
        foreach ($data as $key => &$value) {
            if (! isset($value['type_name']) || ! isset($value['value'])) {
                continue;
            }
            
            $value[$value['type_name']] = $value['value'];
            
            unset($data[$key]['value'], $data[$key]['type_name']);
        }
        $this->parseChildren = $data;
        
        return $this;
    }

    /**
     * 转换为一维数组
     * 
     * @param array $receive            
     * @param array $data            
     * @return array
     */
    public function oneArray(array &$receive, array $data = null)
    {
        $data = (empty($data)) ? $this->parseChildren : $data;
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->oneArray($receive, $value);
            } else {
                $receive[$key] = $value;
            }
        }
        return $receive;
    }

    /**
     * 组合数据
     */
    public function lineArrayData(array $data, array $lineArray, $isExits = 'username', $secondExits = 'goods_title', $isXD = 'user_id')
    {
        if (empty($data)) {
            return array();
        }
        foreach ($data as $value) {
            if (! is_array($value) || empty($value)) {
                return false;
            }
        }
        $lineArray = array_merge($lineArray, $data);
        
        $parseData = self::recursionData($lineArray, $isExits, $secondExits, $isXD);
        return $parseData;
    }

    /**
     * 处理数据
     */
    private static function recursionData(array $data, $isExits = 'username', $secondExits = 'goods_title', $isXD = 'user_id', $id = 10000000)
    {
        $flag = array();
        foreach ($data as $key => $value) {
            $flag = array_key_exists($isExits, $value) ? $value : null;
            ! array_key_exists($secondExits, $value) ? array_shift($data) : false;
            foreach ($data as $sKey => $sValue) {
                if (! empty($flag) && $flag[$isXD] == $sValue[$isXD]) {
                    $data[$sKey][$isExits] = $flag[$isExits];
                    unset($data[$sKey][$isXD]);
                }
            }
        }
        return $data;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Reflector::export()
     */
    public static function export()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Reflector::__toString()
     */
    public function __toString()
    {
        // TODO Auto-generated method stub
    }

    public function __call($methods, $args = null)
    {
        $obj = new self::$childrenClass();
        
        return method_exists($obj, $methods) ? call_user_func_array(array(
            $obj,
            $methods
        ), $args) : E('该类【' . get_class($obj) . '】，没有该方法【' . $methods . '】');
    }
}