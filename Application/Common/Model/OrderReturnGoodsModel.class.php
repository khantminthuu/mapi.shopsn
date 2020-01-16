<?php
namespace Common\Model;
use Think\Model;
/**
 * Class OrderModel
 * @package Common\Model
 */
class OrderReturnGoodsModel extends BaseModel
{

	public static $id_d;	//退货id

	public static $orderId_d;	//订单【id】

	public static $tuihuoCase_d;	//退货理由

	public static $createTime_d;	//申请时间

	public static $revocationTime_d;	//撤销时间

	public static $updateTime_d;	//审核时间

	public static $goodsId_d;	//退货的商品【id】

	public static $explain_d;	//退货(退款)说明

	public static $price_d;	//退货(退款)金额

	public static $isReceive_d;	//退款及其换货时是否收到货【0未收到1收到】

	public static $type_d;	//类型【1退货0换货3维修2退款】

	public static $status_d;	//审核状态【0审核中1审核失败2审核通过3退货中4换货中5换货完成6退货完成7已撤销】

	public static $userId_d;	//用户编号

	public static $number_d;	//申请数量

	public static $applyImg_d;	//申请图片

	public static $content_d;	//审核内容

	public static $isOwn_d;	//是否自营【0否 1是】

	public static $expressId_d;	//快递【编号】

	public static $waybillId_d;	//运单号

	public static $remark_d;	//备注

	public static $storeId_d;	//店铺【编号】

    private static $obj;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}