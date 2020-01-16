<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Common\Model;

use Common\Model\BaseModel;

/**
 * 发货模地址型
 * @author Administrator
 * @version 1.0.0
 */
class FreightSendModel extends BaseModel
{
    /**
     * 类的实例
     * @var FreightSendModel
     */
    private static $obj;

	public static $freightId_d;	//模板编号

	public static $mailArea_d;	//地区编号

    /**
     * 获取类的实例
     * @return \Admin\Model\FreightSendModel
     */
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
}