<?php
namespace Common\Model;


/**
 * 搭配套餐推荐模型
 */
class GoodsComboModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $goodsId_d;	//主商品id

	public static $subIds_d;	//最佳组合id

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//修改时间

	public static $storeId_d;	//店铺id


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}