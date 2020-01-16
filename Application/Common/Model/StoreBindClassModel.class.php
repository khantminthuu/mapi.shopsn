<?php
declare(strict_types = 1);
namespace Common\Model;

use Common\Model\BaseModel;

/**
 * 店铺绑定分类逻辑
 * @author Administrator
 */
class StoreBindClassModel extends BaseModel
{
	private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//店铺名称【id】

	public static $commisRate_d;	//佣金比例

	public static $classOne_d;	//一级分类

	public static $classTwo_d;	//二级分类

	public static $classThree_d;	//三及分类

	public static $status_d;	//状态【0审核中1已审核 2平台自营店铺】

	
	public static function getInitnation()
	{
		$class = __CLASS__;
		return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
	}
}