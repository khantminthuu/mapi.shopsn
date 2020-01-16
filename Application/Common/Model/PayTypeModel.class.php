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

namespace Common\Model;

use Common\Model\BaseModel;

/**
 * 支付 类型 
 */
class PayTypeModel extends BaseModel
{
    private static $obj;

	public static $id_d;

	public static $typeName_d;

	public static $createTime_d;

	public static $updateTime_d;

	public static $status_d;

	public static $isDefault_d;

	public static $isSpecial_d;	//特殊支付方式 0 不是 1 是

	public static $logo_d;	//支付logo

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    
}