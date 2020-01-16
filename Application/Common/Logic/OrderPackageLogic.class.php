<?php
declare(strict_types = 1);
namespace Common\Logic;
use Common\Model\OrderPackageModel;
use Common\Model\OrderPackageGoodsModel;
use Common\Model\GoodsPackageCartModel;
use Common\Model\UserAddressModel;
use Common\Model\GoodsModel;
use Common\Model\GoodsImagesModel;
use Common\Model\StoreModel;
use Common\Model\SpecGoodsPriceModel;
use Common\Logic\GoodsPackageCartLogic;
use Common\SessionParse\SessionManager;
use Common\Tool\Tool;
use Think\SessionGet;


/**
 * 逻辑处理层
 */
class OrderPackageLogic extends AbstractGetDataLogic {
	
	/**
	 * 订单数据
	 * @var int
	 */
	private $orderDataNumber = 0;
	
	/**
	 * 店铺编号数据
	 * @var array
	 */
	private $storeId = [];
	
	/**
	 * 
	 * @var integer
	 */
	private $orderId = 0;
	
	/**
	 * 支付数据
	 * @var array
	 */
	private $payData = [];
	
	/**
	 * 发票信息
	 * @var array
	 */
	private $invoiceData = [];
	
	/**
	 * @return array
	 */
	public function getPayData() :array
	{
		return $this->payData;
	}
	
	/**
	 * 发票信息
	 * @return array
	 */
	public function getInvoiceData() :array
	{
		return $this->invoiceData;
	}
	
	/**
	 * 获取订单编号
	 * @return number
	 */
	public function getOrderId() {
		return $this->orderId;
	}
	
	/**
	 * 构造方法
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new OrderPackageModel();
	}
	/**
	 * 返回验证数据
	 */
	public function getValidateByLogin() {
		$message = [ 
			'address_id' => [ 
				'number' => '地址必须是数字' 
			],
// 			'translate' => [
// 				'number' => '发票必须是数字' 
// 			],		
		];
		return $message;
	}
	public function getValidateByCancel() {
	}
	/**
	 * 获取结果
	 */
	public function getResult() {
	}
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return OrderPackageModel::class;
	}
	
	/**
	 * 添加套餐订单
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAddAll() :array
	{
		$dataArray = SessionManager::GET_GOODS_DATA_SOURCE();
		
		$args = $this->data['goods'];
		
		$invoice = $this->data['invoice_id'];
		
		//准备生成订单
		$orderData = [];
		
		$time = time();
		
		$ownMyConpon = SessionManager::GET_OWN_MY_COUPON();
		
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		Tool::connect('Token');
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		foreach ($dataArray as $key => $value) {
			
			
			$orderData[$value['store_id']][OrderPackageModel::$addressId_d] = $this->data['address_id'];
			
			$orderData[$value['store_id']][OrderPackageModel::$orderSn_id_d] = Tool::toGUID();
			
			$orderData[$value['store_id']][OrderPackageModel::$createTime_d] = $time;
			
			$orderData[$value['store_id']][OrderPackageModel::$userId_d] = $userId;
			
			$orderData[$value['store_id']][OrderPackageModel::$orderStatus_d] = 0;
			
			$orderData[$value['store_id']][OrderPackageModel::$platform_d] = 2;
			
			$orderData[$value['store_id']][OrderPackageModel::$priceSum_d] += $value['price_sum'];
			$orderData[$value['store_id']][OrderPackageModel::$translate_d] = isset($invoice[$value['store_id']]['translate']) ? $invoice[$value['store_id']]['translate'] : 0;
			
			$orderData[$value['store_id']][OrderPackageModel::$storeId_d] = $value['store_id'];
			
			$orderData[$value['store_id']][OrderPackageModel::$status_d] = 0;
			
			$orderData[$value['store_id']][OrderPackageModel::$platform_d] = 2;
			
			$orderData[$value['store_id']][OrderPackageModel::$couponDeductible_d] = isset($ownMyConpon [$value['store_id']]) ? $ownMyConpon [$value['store_id']]: 0;
			
			$orderData[$value['store_id']][OrderPackageModel::$shippingMonery_d] = $freightMoney[$value['store_id']];
			
			if (!empty($args[$value['store_id']])) {
				$orderData[$value['store_id']][OrderPackageModel::$remarks_d] = $args[$value['store_id']];
			}
		}
		$this->storeId = array_keys($orderData);
		
		$addOrderData = array_values($orderData);
		
		$this->orderDataNumber = count($addOrderData);
		
		return $addOrderData;
		
	}
	
	
	/**
	 * 产生订单--立即购买
	 */
	public function orderBegin() {
		
		
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		if (empty ( $freightMoney )) {
			$this->errorMessage = '运费错误';
			return [];
		}
		
		$goods = SessionManager::GET_GOODS_DATA_SOURCE();
		
		if (empty($goods)) {
			$this->errorMessage = '商品错误';
			return [];
		}
		
		$this->modelObj->startTrans();
		
		$insertId = $this->addAll();
		
		if (!$this->traceStation($insertId)) {
			$this->errorMessage .= '生成套餐订单失败';
			return [];
		}
		
		$invoice = $this->data['invoice_id'];
		
		$number = array();
		
		$couponMoney = 0;
		
		$payData = [];
		
		$ownMyCoupon = SessionManager::GET_OWN_MY_COUPON();
		
		$goodsMoney = SessionManager::GET_COUPON_MONEY();
		
		$orderId = 0;
		for ($i = 0; $i < $this->orderDataNumber; $i ++) {
			$number[$this->storeId[$i]] = [];
			
			$orderId = $i + $insertId;
			
			$number[$this->storeId[$i]]['order_id'] = $orderId;
			
			$number[$this->storeId[$i]]['store_id'] = $this->storeId[$i];
			
			$couponMoney = isset($ownMyCoupon [$value['store_id']]) ? $ownMyCoupon [$this->storeId[$i]]: 0;
			
			$payData[$orderId] = [];
			
			$payData[$orderId]['order_id'] = $orderId;
			
			$payData[$orderId]['store_id'] = $this->storeId[$i];
			
			$payData[$orderId]['total_money'] = sprintf("%.2f", $goodsMoney[$this->storeId[$i]] + $freightMoney [$this->storeId[$i]] - $couponMoney);
			
			if (!empty($invoice[$value['store_id']]['id'])) {
				
				$this->invoiceData[$i] = [];
				
				$this->invoiceData[$i]['id'] = $invoice[$this->storeId[$i]]['id'];
				
				$this->invoiceData[$i]['order_id'] = $orderId;
				
				$i++;
			}
		}
		
		$this->payData = $payData;
		
		return $number;
	}

	/**
	 * @param $data
	 * @return array|string 得到商品信息
	 */
	public function get_good_info($data) {

		$this->cart_model = new GoodsPackageCartModel();
		$where['id'] = array("IN",$data['id']);
		
		$Cart=$this->cart_model->field("id,package_id,package_num,store_id")->where($where)->select();
		$goods_model = M ( 'goods' );
		$total_money = 0;
		$storeid = 0;
		$weight = 0;
		$good_number = 0;

		foreach ( $Cart as $key => $value ) {
			$pack = M("goods_package")->field("total,discount")->where(['id'=>$value['package_id']])->find();
			$pack_sub = M("goods_package_sub")->field("goods_id,discount")->where(['package_id'=>$value['package_id']])->select();
			foreach ($pack_sub as $kk => $vv) {
				$goods_data = $goods_model->where (['id'=>$vv['goods_id']])->field ( 'id as goods_id,stock,price_member,p_id,title,weight,express_id,store_id' )->find ();
				// if ($value ['package_num'] > $goods_data ['stock']) {
				// 	return "商品" . $goods_data ['title'] . "库存不足";
				// }
				
				$weight += $goods_data ['weight']*$value ['package_num'];
				
				$good_number += $value ['package_num'];
				$pack_sub[$kk] ['price_member'] = $goods_data ['price_member'];
			}
			$storeid = $value ['store_id'];
			$total_money += $value ['package_num'] * $pack['discount'];
			$Cart[$key]['total'] = $pack['total'];
			$Cart[$key]['discount'] = $pack['discount'];
			$Cart[$key]['discount'] = $pack['discount'];
			$Cart[$key]['goods'] = $pack_sub;
		}
		
		$allData = [ ];
		$allData ['store_info'] ['totai_money'] = $total_money;
		$allData ['store_info'] ['store_id'] = $storeid;
		$allData ['store_info'] ['weight'] = $weight;
		$allData ['store_info'] ['goods_num'] = $good_number;
		$allData ['good_info'] = $Cart;
		$allData ['remarks'] = $data['remarks'];
		$allData ['translate'] = $data['translate'];
		$allData ['invoice_id'] = $data['invoice_id'];
		return $allData;
	}

		
	/**
	
	/**
	 * 配送方式选择
	 */
	protected function _getShipping() {
		$Shipping = M ( 'express' )->where ( [ 
						'status' => 1,
						'order' => 1 
		] )->field ( 'name,id' )->order ( 'id desc' )->limit ( 2 )->select ();
		return $Shipping;
	}
	
	/**
	 * 取消订单
	 * 
	 *
	 */
	public function cancel_order() {
		$user_id = session ( "user_id" );
		$where['id'] = $this->data ['id'];
		$data['order_status'] = "-1";
		$result = $this->modelObj->where($where)->save($data);
		if ($result === false) {
			return array ("status" => 0,"data" =>"","message" => "取消失败");
		}
		return array ("status" => 1,"data" =>""  ,"message" => "取消成功");
	}
	
	// 获取我的订单
	public function getOrder($where) {
		$this->goods_model = new GoodsModel();
		$this->images_model = new GoodsImagesModel();
		$this->orderGoods = new OrderPackageGoodsModel();
		$this->store = new StoreModel();
		$spec = new SpecGoodsPriceModel();
		$page = empty($this->data['page'])?0:$this->data['page'];
		$field = "id,order_sn_id,order_status,create_time,comment_status,store_id,price_sum,express_id,exp_id,user_id";
		$rest = $this->modelObj->field($field)->where($where)->order("create_time DESC")->page($page.",10")->select();
		$count = $this->modelObj->where($where)->count();
		$Page = new \Think\Page($count,10);
		$page = $Page->show();
		$page_size = $Page->totalPages;
		if (!empty($rest)) {
			foreach ($rest as $key => $value) {
				$goods = $this->orderGoods->field("id,order_id,package_num,package_total,package_discount,goods_id,goods_discount")->where(['order_id'=>$value['id']])->select();
				foreach ($goods as $k => $v) {
					$Goods = $this->goods_model->field("title,p_id")->where(['id'=>$v['goods_id']])->find(); 
					$space = $spec->getGoodSpe($v['goods_id']); 
					$img = $this->images_model->where(['goods_id'=>$Goods['p_id']])->getField("pic_url");
					$goods[$k]['title']=$Goods['title'];
					$goods[$k]['space']=$space;
					$goods[$k]['pic_url']=$img;
				}
				$rest[$key]['goods'] = $goods;
				$store  = $this->store->field('shop_name,store_logo')->where(['id'=>$value['store_id']])->find();
				$rest[$key]['shop_name'] = $store['shop_name'];
				$rest[$key]['store_logo'] = $store['store_logo'];
			}
			return array (
				"status" => 1,
				"data" => array("list"=>$rest,"count"=>$count,"totalPages"=>$page_size),
				"message" => "获取成功" 
			);
		} else {
			return array (
				"status" => 0,
				"data" => "",
				"message" => "暂无数据" 
			);
		}
	}
	public function getValidateByOrder() {
		$message = [ 
			'id' => [ 
				'required' => '订单ID必须' 
			] 
		];
		return $message;
	}
	// 获取订单详情
	public function getOrderDetails() { 
		$this->orderGoods = new OrderPackageGoodsModel();
		$this->userAddressModel = new UserAddressModel();
		$this->goods_model = new GoodsModel();
		$this->images_model = new GoodsImagesModel();
		$this->store = new StoreModel();
		$spec = new SpecGoodsPriceModel();
		$where ['id'] = $this->data ['id'];
		$field = 'id,order_sn_id,order_status,create_time,comment_status,store_id,price_sum,express_id,exp_id,remarks,pay_type,platform,shipping_monery,delivery_time,pay_time,address_id,user_id,coupon_deductible,translate';
		$way = 'find';
		$rest = $this->modelObj->field($field)->where($where)->find();
		if (empty ( $rest )) {
			return array (
				"status" => 0,
				"data" => "",
				"message" => "暂无数据" 
			);
		}
		if ($rest ['translate'] == 1) {
			$type = M ( "OrderPackageInvoice" )->where ( [ 
							'order_id' => $rest ['id'] 
			] )->getField ( "type_id" );
			$rest ['translate'] = M ("InvoiceType")->where([ 
							'id' => $type 
			] )->getField ("name");
		} else {
			$rest ['translate'] = "不需要发票";
		}
		$rest ['pay_type'] = M ( 'PayType' )->where ( [ 
						'id' => $rest ['pay_type'] 
		] )->getField ( 'type_name' );
		
		$rest ['exp_name'] = M ( 'express' )->where ( [ 
						'id' => $rest ['exp_id'] 
		] )->getField ( 'name' );
		
		$rest ['address'] = $this->userAddressModel->getUserAddress ( $rest ['address_id'], $rest ['user_id'] );
		
		$goods = $this->orderGoods->field("id,order_id,package_num,package_total,package_discount,goods_id,goods_discount")->where(['order_id'=>$rest['id']])->select();
		foreach ($goods as $k => $v) {
			$Goods = $this->goods_model->field("title,p_id")->where(['id'=>$v['goods_id']])->find(); 
			$space = $spec->getGoodSpe($v['goods_id']); 
			$img = $this->images_model->where(['goods_id'=>$Goods['p_id']])->getField("pic_url");
			$goods[$k]['title']=$Goods['title'];
			$goods[$k]['space']=$space;
			$goods[$k]['pic_url']=$img;
		}
		$rest['goods'] = $goods;
		$rest['store']  = $this->store->field('shop_name,store_logo')->where(['id'=>$rest['store_id']])->find();
		
		if ($rest['order_status'] == 0) {
			
			// 订单类型 0 普通订单 1优惠套餐订单
			SessionGet::getInstance('order_type_by_user', 1)->set();
			
			SessionGet::getInstance('order_data', [
				$rest['id'] => [
					'total_money' => bcadd($rest ['price_sum'], $rest ['shipping_monery'], 2),
					'order_id' => $rest['id'],
					'store_id' => $rest['store_id']
				]
			])->set();
		}
		
		return array (
			"status" => 1,
			"data" => $rest,
			"message" => "获取成功" 
		);
	}
	// 获取订单信息
	public function getOrderInfo() {
		$this->orderGoods = new OrderPackageGoodsModel();
		$this->goods_model = new GoodsModel();
		$this->images_model = new GoodsImagesModel();
		$where ['order_id'] = $this->data ['id'];
		$goods = $this->orderGoods->field("order_id,goods_id")->where($where)->select();
		foreach ($goods as $k => $v) {
			$Goods = $this->goods_model->field("title,p_id")->where(['id'=>$v['goods_id']])->find(); 	
			$img = $this->images_model->where(['goods_id'=>$Goods['p_id']])->getField("pic_url");
			$goods[$k]['title']=$Goods['title'];
			$goods[$k]['pic_url']=$img;
		}
		return array (
			"status" => 1,
			"data" => $goods,
			"message" => "获取成功" 
		);
	}
	// 申请售后获取订单信息
	public function getOrderInfoByReturn() {
		$orderGoods = new OrderPackageGoodsModel();
		$post = $this->data;
		$where['id'] = $post['id'];
		$field = "id,order_sn_id,create_time,store_id";
		$order = $this->modelObj->field($field)->where($where)->find();
		$o_where['order_id'] = $post['id'];
		$o_where['goods_id'] = $post['goods_id'];
		$o_field = "order_id,goods_id,package_num,goods_discount";
		$goods = $orderGoods->field($o_field)->where($o_where)->find();
		$goods ['order_sn_id'] = $order ['order_sn_id'];
		$goods ['create_time'] = $order ['create_time'];
		$goods ['store_id'] = $order ['store_id'];
		return array ("status" => 1,"data" => $goods,"message" => "获取成功");
	}
	
	/**
	 * 支付回调成功后 修改订单状态
	 */
	public function paySuccessEditStatus() {
		if (empty ( $this->data ['id'] )) {
			return false;
		}
		$this->modelObj->startTrans ();
		
		$status = $this->modelObj->where ( OrderPackageModel::$id_d . ' in (%s)', $this->data ['id'] )->save ( [ 
			OrderPackageModel::$payTime_d => time (),
			OrderPackageModel::$orderStatus_d => '1',
			OrderPackageModel::$payType_d => $this->data ['pay_conf'] ['pay_type_id'],
			OrderPackageModel::$platform_d => $this->data ['pay_conf'] ['type'] 
		] );
		
		if (! $this->traceStation ( $status )) {
			return false;
		}
		
		return $status;
	}
	
	// 获取订单状态
	public function getOrderStatus() {
		$data = $this->modelObj->where ( OrderPackageModel::$id_d . ' in (%s)', $this->data ['id'] )->getField ( OrderPackageModel::$id_d . ',' . OrderPackageModel::$orderStatus_d );
		
		return $data;
	}
	
	// 删除订单
	public function getOrderDel() {
		$where ['id'] = $this->data ['id'];
		$data ['status'] = 1;
		$res = $this->modelObj->where ( $where )->save ( $data );
		if (! $res) {
			return array (
				"status" => 0,
				"data" => "",
				"message" => "删除失败" 
			);
		}
		return array (
			"status" => 1,
			"data" => "",
			"message" => "删除成功" 
		);
	}
	//订单再次购买
	public function getBuyAgain(){
    	$orderGoods = new OrderPackageGoodsModel();
		$post = $this->data;
		$goods = $orderGoods->field("package_id")->where(['order_id'=>$post['id']])->select();
		$orderGoods = array_column($goods,"package_id");
		$order['id'] = implode(",",$orderGoods);
		$this->goodsCartLogic = new GoodsPackageCartLogic($order);
		$result = $this->goodsCartLogic->addPackageToCart();
		return $result;
	}
	
	/**
	 * 提交事务
	 * @return bool
	 */
	public function submitTranstion() :bool
	{
		return $this->modelObj->commit();
	}
}
