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

namespace Common\Tool;

/**
 * 事件监听机制
 * @author 王强
 * @version 1.0.1
 */
class Event
{
    
    private static $pluin = array();
    
    private static $classPluin = [];
    
    private static $error;
    
    /**
     * 监听构造方法
     */
    public function __construct($tag, $name)
    {
        if (isset(self::$classPluin[$tag])) {
            self::$error[$tag][] = '已存在 该插件';
        } else {
            self::$classPluin[$tag]['class'] = $name;
            self::$classPluin[$tag]['operator'] = $tag;
        }
        
    }
    /**
     * 添加行为
     * @param string $tag
     * @param string $name
     */
    public function addClassPluin($tag, $name)
    {
        if (isset(self::$classPluin[$tag])) {
            self::$error[$tag][] = '已存在 该插件';
        } else {
            self::$classPluin[$tag]['class'] = $name;
            self::$classPluin[$tag]['operator'] = $tag;
        }
    }
    
    /**
     * 插入监听机制 
     * @param 监听名称
     * @param 
     */
    static public function insetListen($name, $function)
    {
        if (isset(self::$pluin[$name])) {
            self::$error[$name][] = '已存在 该插件';
            return false;
        }
        self::$pluin[$name] = $function;
    }
    
    static public function listen($name, &$param)
    {
        if (!isset(self::$pluin[$name])) {
            return null;
        }
        $function = self::$pluin[$name];
       
        if (!is_callable($function)) {
            self::$error[$name][] = '不可调用';
            return false;
        }
        return $function($param);
    }
    /**
     * @return the $error
     */
    public static function getError()
    {
        return self::$error;
    }
    /**
     * 回调对象
     */
    public static function insertObjectCallBack ($name, $args)
    { 
        if (empty(self::$classPluin[$name])) {
            return $args;
        }
        
        $data = [];
        try {
            
            $obj = self::$classPluin[$name]['class'];
           
            $classRef = new \ReflectionObject($obj);
           
            $data = $classRef->getMethod(self::$classPluin[$name]['operator'])->invoke($obj, $args);

        } catch (\Exception $e) {
            
            throw $e;
        }
        
        return $data;
    }
    
    /**
     * 回调类
     */
    public static function insertClassCallBack ($name, $args)
    {
        if (empty(self::$classPluin[$name]) || empty($args)) {
            return [];
        }
    
        $data = [];
        try {
             
            $classRef = new \ReflectionClass(self::$classPluin[$name]['class']);
    
            $obj = $classRef->newInstance($args);
    
            $data = $classRef->getMethod(self::$classPluin[$name]['operator'])->invoke($obj);
    
        } catch (\Exception $e) {
    
            throw $e;
        }
    
        return $data;
    }
    
    /**
     * @param field_type $error
     */
    public static function setError($error)
    {
        self::$error = $error;
    }
}

