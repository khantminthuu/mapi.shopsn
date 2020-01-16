<?php
namespace Common\Model;

/**
 * 优惠套餐
 * @author Administrator
 *
 */
class OrderPackageGoodsModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//主键id

	public static $orderId_d;	//订单id

	public static $packageId_d;	//套餐id

	public static $packageNum_d;	//套餐数量

	public static $packageTotal_d;	//单个套餐商品总价

	public static $packageDiscount_d;	//单个套餐优惠总价

	public static $goodsId_d;	//商品id

	public static $goodsDiscount_d;	//单个商品套餐价


	public static $status_d;	//-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间


	public static $freightId_d;	//运费模板【编号】


	public static $storeId_d;	//店铺【编号】
	
	public static $userId_d;	//用户编号
	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}