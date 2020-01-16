<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/24 0024
 * Time: 14:04
 */

namespace Common\Model;


class ServiceModel extends BaseModel
{

	public static $id_d;	//

	public static $status_d;	//是否显示  1为显示  0不显示

	public static $sort_d;	//排序

	public static $storeId_d;	//店铺id

	public static $userId_d;	//管理员user_id

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;

    }
}