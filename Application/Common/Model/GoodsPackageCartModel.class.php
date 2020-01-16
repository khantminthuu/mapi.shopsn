<?php
namespace Common\Model;


/**
 * 模型
 */
class GoodsPackageCartModel extends BaseModel
{


	public static $id_d;	//套餐购物车【编号】

	public static $packageId_d;	//套餐【编号】

	public static $packageNum_d;	//套餐数量

	public static $storeId_d;	//商户编号

	public static $userId_d;	//用户

	public static $createTime_d;	//创建日期

	public static $updateTime_d;	//修改日期

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

}