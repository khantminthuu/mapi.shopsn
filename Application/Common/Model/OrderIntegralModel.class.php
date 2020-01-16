<?php
namespace Common\Model;
use Common\Model\CommonModel;
use Common\Model\StoreModel;

/**
 * 模型
 */
class OrderIntegralModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//编号

	public static $orderSn_d;	//订单标志

	public static $interaglTotal_d;	//使用积分总数

	public static $priceSum_d;	//花费金额

	public static $userId_d;	//用户

	public static $expressId_d;	//快递单编号

	public static $addressId_d;	//收货地址【编号】

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

	public static $platform_d;	//平台【0代表pc，1代表app，2Wap】

	public static $orderType_d;	//订单类型【0普通订单1货到付款】

	public static $storeId_d;	//店铺

	
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    

    /**
     * 获取用户的积分兑换列表
     *
     */
    public function getUserConfirm($userId){
        $storeModel = new StoreModel();
        $page = empty($this->data['page'])?0:$this->data['page']; 
        $where['a.user_id'] = $userId;
        $where['a.status'] = 0;
        $field = 'a.id,a.order_status,FROM_UNIXTIME(a.create_time,\'%Y-%m-%d %H:%i:%s\') as create_time,a.price_sum,b.goods_num,b.integral,b.goods_id,a.store_id';
        $ret = $this->alias("a")
            ->field($field)
            ->join('db_order_integral_goods as b on b.order_id = a.id', 'LEFT')
            ->where($where)
            ->page($page.',10')
            ->order("a.id DESC")
            ->select();
        $count =  $this->alias("a")->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($ret)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $goodModel = new GoodsModel();
        foreach ($ret as $key =>$value){
            //获取商品的图片和标题信息
            $goodInfo = $goodModel->getGoodTitle($value['goods_id']);
            $ret[$key]['image'] = $goodInfo['image'];
            $ret[$key]['title'] = $goodInfo['title'];
        }
        $result = [];
        foreach ($ret as $k => $info) {
            $store = $storeModel->field("shop_name,store_logo")->where(['id'=>$info['store_id']])->find();
            $result[$info['store_id']]['cart'][] = $info;
            $result[$info['store_id']]['store_name'] = $store['shop_name'];
            $result[$info['store_id']]['store_logo']=$store['store_logo'];

        }
        $cart = array_values($result);
        $data['goods'] = $cart;
        $data['count'] = $count;
        $data['totalPages'] = $totalPages;
        $data['page_size'] = 10;

        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }

    /**
     * 获取积分兑换订单详情
     *
     */
    public function getConfirmInfo($uid,$data){
        $where = [
            'a.user_id'      => $uid,
            'a.id'           => $data['id'],
            'a.status'      => 0,
        ];

        $field = 'a.id,a.store_id,a.shipping_monery,a.order_status,a.interagl_total,a.pay_type,a.order_sn as order_sn_id,a.address_id,FROM_UNIXTIME(a.create_time,\'%Y-%m-%d %H:%i:%s\') as create_time,FROM_UNIXTIME(a.delivery_time,\'%Y-%m-%d  %H:%i:%s\') as delivery_time,a.exp_id,a.price_sum,b.goods_num,b.money,b.goods_id,a.remarks';
        $ret = $this->alias("a")
            ->field($field)
            ->join('db_order_integral_goods as b on b.order_id = a.id', 'LEFT')
            ->where($where)
            ->find();
        // 获取运送快递
        $express = CommonModel::getExpressModel();
        $ret['express'] = $express->getExpress($ret['exp_id']);
        // 获取收货地址
        $userAddress = CommonModel::getUserAddressModel();

        $userInfo = $userAddress->getUserAddress($ret['address_id'],$uid);
        $ret['user_name'] = $userInfo['realname'];
        $ret['user_mobile'] = $userInfo['mobile'];
        $ret['user_address'] = $userInfo['addressInfo'];
        $ret['store_name'] = M("pay_type")->where(["id"=>$ret['pay_type']])->getField("type_name");
        $goodModel = new GoodsModel();
        //获取商品的图片和标题信息
        $goodInfo = $goodModel->getGoodTitle($ret['goods_id']);
        $ret['image'] = $goodInfo['image'];
        $ret['title'] = $goodInfo['title'];
        $ret['integral_id'] = M("integral_goods")->where(['goods_id'=>$ret['goods_id']])->getField("id");
        $ret['store_name'] = M("Store")->where(['id'=>$goodInfo['store_id']])->getField("shop_name");
        return $ret;
    }

    /**
     * 积分兑换商品 确认收货
     *
     */
    public function confirmGetgoods($userId,$orderId){
        $where['user_id'] = $userId;
        $where['id']       = $orderId;
        $data['order_status'] = 4;
        $result = $this->where($where)->save($data);
        return $result;
    }

    /**
     * 积分兑换商品取消订单
     *
     */
    public function cancel_order($uid,$id){
        $where = [
            'id' => $id,
            'user_id' =>$uid,
        ];
        $data['order_status'] = '-1';
        M()->startTrans();
        $reslt =  $this->where($where)->save($data);
        if (!$reslt) {
             M()->rollback();
            return array("status"=>0,"message"=>"取消失败","data"=>"");
        }
        //操作订单商品表里的状态
        $where_order = [
            "order_id" => $id,
         ];
        $date['status'] = "-1";
        $order_goods = M("order_integral_goods")->where($where_order)->save($date);
        if (!$order_goods) {
            M()->rollback();
            return array("status"=>0,"message"=>"取消失败","data"=>"");
        }
        M()->commit();
        return array("status"=>1,"message"=>"取消成功","data"=>"");
    }
    //积分订单再次购买
    public function getNewBuyOrderById($id){
        $order = $this->where(['id'=>$id])->find();

        $order['order_sn'] = toGUID();
        $order['express_id'] = 0;
        $order['create_time'] = time();
        $order['delivery_time'] = 0;
        $order['pay_time'] = 0;
        $order['over_time'] = 0;
        $order['order_status'] = 0;
        $order['comment_status'] = 0;
        $order['status'] = 0;
        unset($order['id']);
        $order_ret = $this->add($order);
        if (!$order_ret) {
             return array("status"=>0,"message"=>"创建订单失败","data"=>"");
        } 
        $order_goods =  M("order_integral_goods")->where(["order_id"=>$id])->find();
        $order_goods['order_id'] = $order_ret;
        $order_goods['status'] = 0;
        $order_goods['comment'] = 0;
        unset($order_goods['id']);
        $ret = M("order_integral_goods")->add($order_goods);
        if (!$ret) {
            return array("status"=>0,"message"=>"创建订单失败","data"=>"");
        }
        return array("status"=>1,"message"=>"创建订单成功","data"=>array("orderId"=>$order_ret));
    }

}