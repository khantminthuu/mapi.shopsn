<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:21
 */

namespace Common\Model;


class MemberDistributionModel extends BaseModel
{

	public static $id_d;	//ID

	public static $storeId_d;	//店铺id

	public static $userId_d;	//用户ID

	public static $cash_d;	//提成总金额

	public static $tId_d;	//分销时段表ID

	public static $status_d;	//0未打款1已打款

	public static $addTime_d;	//添加时间

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}