<?php
namespace Common\Model;


/**
 * 模型
 */
class IntegralGoodsModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//id

	public static $goodsId_d;	//商品ID

	public static $integral_d;	//需要的积分

	public static $delayed_d;	//积分最少被领取时间,最少0,最大999

	public static $status_d;	//是可兑换

	public static $createTime_d;	//创建时间

	public static $money_d;	//换取商品需要另外添加的钱

	public static $updateTime_d;	//修改时间

	public static $storeId_d;	//店铺【id】

	public static $isShow_d;	//是否显示【1显示 0不显示】

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }



}