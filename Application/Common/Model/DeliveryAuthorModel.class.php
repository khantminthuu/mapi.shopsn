<?php
namespace Common\Model;


/**
 * 配送设置模型
 */
class DeliveryAuthorModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键id

	public static $isOpen_d;	//是否开启配送0关闭1开启

	public static $transport_d;	//运送方式设置0平台设置1商家设置

	public static $transportMode_d;	//运送方式0平台派单1商家配送

	public static $freightCalculation_d;	//运费计算方式设置0平台设置1商家设置

	public static $freightMode_d;	//运费计算方式0按距离1固定值2免费

	public static $distance_d;	//每公里价格

	public static $freightMoney_d;	//固定价格

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}