<?php
namespace Common\Model;


/**
 * 配送运费设置表模型
 */
class DeliveryMoneyConfigModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键id

	public static $isOpen_d;	//0关闭1开启

	public static $storeId_d;	//店铺id

	public static $transportMode_d;	//配送方式0平台配送1店铺配送

	public static $freightMode_d;	//运费计算方式0按距离1固定2免费

	public static $distance_d;	//每公里价格

	public static $freightMoney_d;	//固定运费

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

}