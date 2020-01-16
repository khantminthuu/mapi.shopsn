<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:21
 */

namespace Common\Model;


class MemberDetailModel extends BaseModel
{

	public static $id_d;	//ID

	public static $userId_d;	//提成用户ID

	public static $storeId_d;	//店铺id

	public static $puserId_d;	//被提成用户ID

	public static $cash_d;	//提成金额

	public static $tId_d;	//分销时间表ID

	public static $remark_d;	//备注

	public static $addTime_d;	//添加时间

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}