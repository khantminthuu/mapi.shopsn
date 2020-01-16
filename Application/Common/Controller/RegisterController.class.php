<?php
namespace Common\Controller;
/**
 * Created by PhpStorm. 生产一颗注册树
 * User: 刘嘉强
 * Date: 2018-01-25
 * Time: 10:09
 */

class RegisterController{

    protected static $objects;
    //设置对象
    public static function set($alias,$object){
        self::$objects[$alias]=$object;
    }
    // 得到对象
    public static function get($alias){
        return self::$objects[$alias];
    }
    // 销毁对象
    public static function _unset($alias){
        unset(self::$objects[$alias]);
    }
}