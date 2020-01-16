<?php
namespace Common\Model;
use Think\Model;
use Common\Model\GoodsModel;
use Common\Model\CommonModel;

/**
 * Class OrderModel
 * @package Common\Model
 */
class OrderModel extends BaseModel
{
	// -1:取消订单,0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功, 10：代发货，11待收货
	const CancellationOfOrder = -1;
	
	const NotPaid = 0;
	
	const YesPaid = 1;
	
	const InDelivery = 2;
	
	const AlreadyShipped = 3;
	
	const ReceivedGoods = 4;
	
	const ReturnAudit = 5;
	
	const AuditFalse  = 6;
	
	const AuditSuccess = 7;
	
	const Refund = 8;
	
	const ReturnMonerySucess = 9;
	
	const ToBeShipped = 1;
	
	const ReceiptOfGoods = 3;
	
    private static $obj;
	

	public static $id_d;	//id

	public static $orderSn_id_d;	//订单标识

	public static $priceSum_d;	//总价

	public static $expressId_d;	//快递单编号

	public static $addressId_d;	//收货地址编号

	public static $userId_d;	//用户【编号】

	public static $createTime_d;	//创建时间

	public static $deliveryTime_d;	//发货时间

	public static $payTime_d;	//支付时间

	public static $overTime_d;	//完结时间

	public static $orderStatus_d;	//-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功, 

	public static $commentStatus_d;	//评价状态 0未评价 1已评价

	public static $wareId_d;	//仓库编号

	public static $payType_d;	//支付类型编号

	public static $remarks_d;	//订单备注

	public static $status_d;	//0正常1删除

	public static $translate_d;	//1需要发票，0不需要

	public static $shippingMonery_d;	//运费【这样 就不用 重复计算两遍】

	public static $expId_d;	//快递表编号

	public static $platform_d;	//平台[：0代表pc，1代表app 2 wap]

	public static $orderType_d;	//订单类型0普通订单1货到付款

	public static $storeId_d;	//店铺

	public static $couponDeductible_d;	//优惠券抵扣金额


    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }



   

    //获取订单
    public function getOrderByWhere($where,$field,$way){
        $data = $this->field($field)->where($where)->order("create_time DESC")->$way();
        return $data;
    }
}