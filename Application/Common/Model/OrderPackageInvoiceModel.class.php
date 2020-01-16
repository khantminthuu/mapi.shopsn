<?php
namespace Common\Model;


/**
 * 模型
 */
class OrderPackageInvoiceModel extends BaseModel
{


	public static $id_d;	//发票id

	public static $orderId_d;	//订单编号

	public static $raisedId_d;	//发票抬头【编号】

	public static $contentId_d;	//发票内容

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	// 修改日期

	public static $userId_d;	//用户id

	public static $remarks_d;	//备注

	public static $typeId_d;	//发票类型

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }
}