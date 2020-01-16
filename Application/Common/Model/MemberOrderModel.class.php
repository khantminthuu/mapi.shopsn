<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:21
 */

namespace Common\Model;


class MemberOrderModel extends BaseModel
{

	public static $id_d;	//主键id

	public static $userId_d;	//用户id

	public static $storeId_d;	//店铺id

	public static $startTime_d;	//开始时间

	public static $endTime_d;	//结束时间

	public static $orderPrice_d;	//订单总价

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}