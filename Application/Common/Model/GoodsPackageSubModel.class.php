<?php
namespace Common\Model;


/**
 * 模型
 */
class GoodsPackageSubModel extends BaseModel
{

    private static $obj;
    

	public static $id_d;	//编号

	public static $packageId_d;	//套餐【id】

	public static $goodsId_d;	//商品【id】

	public static $discount_d;	//商品套餐价

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

}