<?php
namespace Common\Model;


/**
 * 店铺模型
 */
class StoreAdvModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//广告自增标识编号

	public static $apId_d;	//广告位id

	public static $advTitle_d;	//广告内容描述

	public static $advContent_d;	//广告内容

	public static $adKey_d;	//广告键

	public static $advStart_date_d;	//广告开始时间

	public static $advEnd_date_d;	//广告结束时间

	public static $slideSort_d;	//幻灯片排序

	public static $storeId_d;	//店铺【ID】

	public static $clickNum_d;	//广告点击率

	public static $isAllow_d;	//会员购买的广告是否通过审核【0未审核1审核已通过2审核未通过】

	public static $buyStyle_d;	//购买方式

	public static $goldpay_d;	//购买所支付的金币

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间

	public static $adUrl_d;	//广告链接


	public static $status_d;	//是否显示0显示1不显示


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;

    }
}