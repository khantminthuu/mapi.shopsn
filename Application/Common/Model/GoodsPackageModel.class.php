<?php
namespace Common\Model;

/**
 * 商品套餐
 * @author Administrator
 */
class GoodsPackageModel extends BaseModel
{
	private static $obj;


	public static $id_d;	//id

	public static $total_d;	//商品总价

	public static $discount_d;	//优惠总价

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//修改时间

	public static $storeId_d;	//店铺编号

	public static $status_d;	//审核状态【0拒绝， 1通过，2审核中】

	public static $title_d;	//套餐名称

	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}