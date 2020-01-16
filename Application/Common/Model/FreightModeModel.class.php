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
 * 运送方式表 
 */
class FreightModeModel extends BaseModel
{
    /**
     * 类的实例
     * @var FreightModeModel
     */
    private static $obj;

	public static $id_d;	//ID

	public static $freightId_d;	//运费模板编号

	public static $firstThing_d;	//首件

	public static $firstWeight_d;	//首重

	public static $fristVolum_d;	//首体积

	public static $fristMoney_d;	//首运费【起步价】

	public static $continuedHeavy_d;	//续重

	public static $continuedVolum_d;	//续体积

	public static $continuedMoney_d;	//续费

	public static $carryWay_d;	//运送方式编号

	public static $continuedThing_d;	//续件
    public static $storeId_d;   //店铺id

    /**
     * 获取类的实例
     */
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
}