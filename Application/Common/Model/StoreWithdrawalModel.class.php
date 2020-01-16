<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 15:58
 */

namespace Common\Model;

/**
 * 店铺提现模型
 */
class StoreWithdrawalModel extends BaseModel
{


	public static $id_d;	//主键id

	public static $userId_d;	//用户id

	public static $storeId_d;	//店铺id

	public static $account_d;	//账号

	public static $type_d;	//类型

	public static $money_d;	//提现金额

	public static $status_d;	//0待审核1已打款2审核不通过

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间

	public static $remarks_d;	//备注

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !(self::$obj instanceof $class) ? new self() : self::$obj;

    }
}