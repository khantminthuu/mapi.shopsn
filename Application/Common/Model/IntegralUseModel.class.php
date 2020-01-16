<?php
namespace Common\Model;


/**
 * 积分使用表模型
 */
class IntegralUseModel extends BaseModel
{


	public static $id_d;	//编号

	public static $userId_d;	//用户【id】

	public static $integral_d;	//积分

	public static $orderId_d;	//订单【id】

	public static $tradingTime_d;	//交易时间

	public static $remarks_d;	//备注

	public static $type_d;	//积分类型【1收入0 支出】

	public static $status_d;	//是否有效【1.可用;2.已用;0.过期;】



    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}
