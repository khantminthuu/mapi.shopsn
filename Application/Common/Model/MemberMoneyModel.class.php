<?php
/**
 * Created by PhpStorm.
 * User: 王波
 * Date: 2019/1/16
 * Time: 14:21
 */

namespace Common\Model;


class MemberMoneyModel extends BaseModel
{

	public static $id_d;	//主键id

	public static $storeId_d;	//店铺id

	public static $userId_d;	//用户id

	public static $money_d;	//金额

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间

	public static $status_d;	//0未提现1已提现

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}