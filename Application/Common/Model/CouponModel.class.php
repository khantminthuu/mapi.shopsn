<?php
namespace Common\Model;


/**
 * 模型
 */
class CouponModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//表id

	public static $name_d;	//优惠券名字

	public static $type_d;	//发放类型 0面额模板1 按用户发放 2 注册 3 邀请 4 线下发放

	public static $money_d;	//优惠券金额

	public static $condition_d;	//使用条件

	public static $createnum_d;	//发放数量

	public static $sendNum_d;	//已领取数量

	public static $useNum_d;	//已使用数量

	public static $sendStart_time_d;	//发放开始时间

	public static $sendEnd_time_d;	//发放结束时间

	public static $useStart_time_d;	//使用开始时间

	public static $useEnd_time_d;	//使用结束时间

	public static $addTime_d;	//添加时间

	public static $updateTime_d;	//更新时间

	public static $storeId_d;	//店铺【id】

	public static $status_d;	//是否有效


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }


}