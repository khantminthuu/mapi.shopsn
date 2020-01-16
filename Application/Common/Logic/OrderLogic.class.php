<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Tool\Tool;
use Common\Model\CommonModel;
use Common\Model\OrderModel;
use Common\Model\OrderGoodsModel;
use Common\Model\UserAddressModel;
use Common\Model\StoreModel;
use Think\SessionGet;
use Common\SessionParse\SessionManager;

/**
 * 逻辑处理层
 */
class OrderLogic extends AbstractGetDataLogic {
	
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
	 * 订单编号（立即购买生成）
	 * @var string
	 */
	private $placeTheOrderId;
	
	/**
	 * @return array
	 */
	public function getPayData()
	{
		return $this->payData;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getPlaceTheOrderId() :string
	{
		return $this->placeTheOrderId;
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
	 * 构造方法
	 *
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new OrderModel ();
		$this->order_goods_model = new OrderGoodsModel ();
		$this->userAddressModel = new UserAddressModel ();
		$this->storeModel = new StoreModel ();
	}
	/**
	 * 返回验证数据
	 */
	public function getValidateByLogin() {
		$message = [ 
						'price_sum' => [ 
										'required' => '订单总价必填' 
						],
						'address_id' => [ 
										'required' => '收货地址必填' 
						],
						'translate' => [ 
										'required' => '是否需要发票必须',
										'number' => '是否需要发票必须是数字' 
						
						] 
		];
		return $message;
	}
	public function getValidateByGoods() :array{
		$message = [ 
			
			'invoice_id' => [
				'required' => '发票信息不能为空',
			],
			'address_id' => [ 
				'number' => '发票地址必填' 
			] 
		];
		return $message;
	}
	
	/**
	 * 
	 * @return string[][]
	 */
	public function getValidateByOrder() :array{
		$message = [ 
			'id' => [ 
			     'number' => '订单ID必须' 
			],
		];
		return $message;
	}
	
	/**
	 * 物流
	 * @return array
	 */
	public function getValidateByLogist() :array{
	    $message = [
	        'id' => [
	            'number' => '订单ID必须'
	        ],
	        'order_status' => [
	            'number' => '订单状态必须是数字',
	        ]
	    ];
	    return $message;
	}
	
	public function getValidateByReturn() {
		$message = [ 
						'id' => [ 
										'number' => '订单ID必须' 
						],
						'goods_id' => [ 
										'number' => '商品ID必须' 
						] 
		];
		return $message;
	}
	
	/**
	 * 订单生成检查
	 * @return string[][]
	 */
	public function getCartIdInfo() :array
	{
		$message = [
						'address_id' => [
										'number' => '收货地址必填'
						],
						'invoice_id' => [
							'required' => '发票必须填写'
						]
		];
		return $message;
	}
	
	/**
	 * 配件检查参数
	 */
	public function getMessageValidateByParts() :array
	{
		return [
			'address_id' => [
				'number' => '地址编号必须是数字',
			],
		];
	}
	
	/**
	 * 多商品生成订单
	 * 判断订单总金额
	 * 判断每个商品的库存
	 * 生成订单总订单
	 * 将商品信息分发到订单商品表
	 * 将购物车信息进行修改
	 */
	public function multiCommodityGeneratedOrder() :array
	{
		
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		if (empty ( $freightMoney )) {
			$this->errorMessage = '运费错误';
			return false;
		}
		
		// 获取购物车商品信息
		$this->modelObj->startTrans();
		
		$insertId = $this->addAll();
		
		if (!$this->traceStation($insertId)) {
			$this->errorMessage .= '、由于长时间没有购买，缓存时间过期，请刷新重新购买。生成订单失败';
			return [];
		}
		
		$invoice = $this->data['invoice_id'];
		
		
		$number = array();
		
		$couponMoney = 0;
		
		$payData = [];
		
		$ownMyCoupon = SessionManager::GET_COUPON_LIST();
		
		$goodsMoney = SessionManager::GET_COUPON_MONEY();
		
		$orderId = 0;
		
		$j = 0;
        $distribution_fee = empty($this->data['distribution_fee'])?0:$this->data['distribution_fee'];
		for ($i = 0; $i < $this->orderDataNumber; $i ++) {
			$number[$this->storeId[$i]] = [];
			
			$orderId = $i + $insertId;
			
			$number[$this->storeId[$i]]['order_id'] = $orderId;
			
			$number[$this->storeId[$i]]['store_id'] = $this->storeId[$i];
			
			$couponMoney = isset($ownMyCoupon [$this->storeId[$i]]['money']) ? $ownMyCoupon [$this->storeId[$i]]['money']: 0;
			
			$payData[$i] = [];
			
			$payData[$i]['order_id'] = $orderId;
			
			$payData[$i]['store_id'] = $this->storeId[$i];
			
			$payData[$i]['total_money'] = sprintf("%.2f", $goodsMoney[$this->storeId[$i]] + $freightMoney [$this->storeId[$i]] - $couponMoney+$distribution_fee);
			
			if (!empty($invoice[$this->storeId[$i]]['id'])) {
				
				$this->invoiceData[$j] = [];
				
				$this->invoiceData[$j]['id'] = $invoice[$this->storeId[$i]]['id'];
				
				$this->invoiceData[$j]['order_id'] = $orderId;
				
				$j++;
			}
		}
		
		$this->payData = $payData;
		
		return $number;
	}
	
	/**
	 * 立即购买->下单
	 */
	public function placeTheOrder() :bool
	{
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		if (empty ( $freightMoney )) {
			$this->errorMessage = '运费错误';
			return false;
		}
		
		$goods = SessionManager::GET_GOODS_DATA_SOURCE();
		
		if (empty($goods)) {
			$this->errorMessage = '商品错误';
			return false;
		}
		// 获取购物车商品信息
		
		$this->modelObj->startTrans();
		
		$insertId = $this->addData();
	
		if (!$this->traceStation($insertId)) {
			$this->errorMessage .= '、生成订单失败';
			return false;
		}
		
		$invoice = $this->data['invoice_id'];
		
		if (!empty($invoice[$goods['store_id']]['id'])) {
			
			$this->invoiceData['id'] = $invoice[$goods['store_id']]['id'];
			
			$this->invoiceData['order_id'] = $insertId;
		}
		
		$this->placeTheOrderId = $insertId;
		
		$ownMyCoupon = SessionManager::GET_COUPON_LIST();
		
		$couponMoney = isset($ownMyCoupon [$goods['store_id']]['money']) ? $ownMyCoupon [$goods['store_id']]['money']: 0;
		
		$goodsMoney = SessionManager::GET_COUPON_MONEY();
        $distribution_fee = empty($this->data['distribution_fee'])?0:$this->data['distribution_fee'];
		$totalMoney = $goodsMoney[$goods['store_id']] + $freightMoney [$goods['store_id']] - $couponMoney+$distribution_fee;
		
		$payData[0] = [
			'order_id' => $insertId,
			'store_id' => $goods['store_id'],
			'total_money' => sprintf("%.2f", $totalMoney)
		];
		
		$this->payData = $payData;
		
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$goods = SessionManager::GET_GOODS_DATA_SOURCE();
		
		$ownMyConpon = SessionManager::GET_COUPON_LIST();
		
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		$invoice = $this->data['invoice_id'];
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$orderData = [];
		$distribution_fee = empty($this->data['distribution_fee'])?0:$this->data['distribution_fee'];
		$delivery = empty($this->data['delivery'])?0:$this->data['delivery'];
		$orderData[OrderModel::$orderSn_id_d] = Tool::connect('Token')->toGUID();
		
		$orderData[OrderModel::$createTime_d] = time();
		
		$orderData[OrderModel::$userId_d] = $userId;
		
		$orderData[OrderModel::$orderStatus_d] = 0;
		
		$orderData[OrderModel::$addressId_d] = $this->data['address_id'];
		
		$orderData[OrderModel::$platform_d] = 2;
		
		$orderData[OrderModel::$priceSum_d] = round($goods['price_sum']+$freightMoney[$goods['store_id']]+$distribution_fee,2);
		
		$orderData[OrderModel::$translate_d] = $invoice[$goods['store_id']]['translate'];
		
		$orderData[OrderModel::$storeId_d] = $goods['store_id'];
		
		$orderData[OrderModel::$status_d] = 0;
		
		$orderData[OrderModel::$couponDeductible_d] = isset($ownMyConpon[$goods['store_id']]['money'] ) ? $ownMyConpon[$goods['store_id']]['money'] : 0;
		
		$orderData[OrderModel::$shippingMonery_d] = $freightMoney[$goods['store_id']];
		
		$orderData[OrderModel::$remarks_d] = isset($this->data['remarks']) ? $this->data['remarks'] : '';
        $orderData[OrderModel::$distributionFee_d] = $distribution_fee;
        $orderData[OrderModel::$delivery_d] = $delivery;
        $orderData[OrderModel::$distributor_d] = 0;
        if(isset($this->data['panic_id'])){
            $orderData[OrderModel::$orderType_d] = 2;
        }
		return $orderData;
	}
	
	/**
	 * 批量添加时处理
	 * @return []
	 */
	protected function getParseResultByAddAll() :array
	{
		$cartInfo = SessionManager::GET_GOODS_DATA_SOURCE();
		
		if (empty($cartInfo)) {
			return [];
		}
		
		$args = $this->data['goods'];
		
		$invoice = $this->data['invoice_id'];
		
		//准备生成订单
		$orderData = [];
		
		$time = time();
		
		$ownMyConpon = SessionManager::GET_COUPON_LIST();
		
		$freightMoney = SessionManager::GET_FREIGHT_MONEY();
		
		Tool::connect('Token');
		
		$userId = SessionGet::getInstance('user_id')->get();
        $distribution_fee = empty($this->data['distribution_fee'])?0:$this->data['distribution_fee'];
        $delivery = empty($this->data['delivery'])?0:$this->data['delivery'];
		$i = 0;
		foreach ($cartInfo as $key => $value) {
			
			
			$orderData[$value['store_id']][OrderModel::$addressId_d] = $this->data['address_id'];
			
			$orderData[$value['store_id']][OrderModel::$orderSn_id_d] = Tool::toGUID();
			
			$orderData[$value['store_id']][OrderModel::$createTime_d] = $time;
			
			$orderData[$value['store_id']][OrderModel::$userId_d] = $userId;
			
			$orderData[$value['store_id']][OrderModel::$orderStatus_d] = 0;
			
			$orderData[$value['store_id']][OrderModel::$platform_d] = 2;
			
			$orderData[$value['store_id']][OrderModel::$priceSum_d] += $value['price_sum'];
			$orderData[$value['store_id']][OrderModel::$translate_d] = $invoice[$value['store_id']]['translate'];
			
			$orderData[$value['store_id']][OrderModel::$storeId_d] = $value['store_id'];
			
			$orderData[$value['store_id']][OrderModel::$status_d] = 0;
			
			$orderData[$value['store_id']][OrderModel::$couponDeductible_d] = isset($ownMyConpon [$value['store_id']]['money']) ? $ownMyConpon [$value['store_id']]['money']: 0;
			
			$orderData[$value['store_id']][OrderModel::$shippingMonery_d] = $freightMoney[$value['store_id']];
			$orderData[$value['store_id']][OrderModel::$distributionFee_d] = $distribution_fee;
			$orderData[$value['store_id']][OrderModel::$delivery_d] = $delivery;
			$orderData[$value['store_id']][OrderModel::$distributor_d] = 0;

			if (!empty($args[$value['store_id']])) {
				$orderData[$value['store_id']][OrderModel::$remarks_d] = $args[$value['store_id']];
			}
		}
		foreach($orderData as $key=>$value){
            $orderData[$key][OrderModel::$priceSum_d] = $value[OrderModel::$priceSum_d]+$value[OrderModel::$shippingMonery_d]+$distribution_fee;
        }
		$this->storeId = array_keys($orderData);
		
		$addOrderData = array_values($orderData);
		
		$this->orderDataNumber = count($addOrderData);
		
		return $addOrderData;
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
		return OrderModel::class;
	}
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array {
		return [ ];
	}
	
	
	/**
	 * 确认收货
	 * @author 王强
	 */
	public function confirmGetgoods() :bool
	{
	    $this->modelObj->startTrans ();
	    
	    $status = $this->modelObj->where ( OrderModel::$id_d . '=:id and '.OrderModel::$userId_d.'=:u_id and '.OrderModel::$orderStatus_d.' = '.OrderModel::AlreadyShipped)
	       ->bind ( [
    	        ':id' => $this->data [OrderModel::$id_d],
    	        ':u_id' => SessionGet::getInstance('user_id')->get()
    	    ] )->save ( [
    	        OrderModel::$orderStatus_d => OrderModel::ReceivedGoods
    	    ] );
    	    
	    if (! $this->traceStation ( $status )) {
	        $this->errorMessage = '确认收货失败';
	        return false;
	    }
	    return true;
	}
	/**
	 * 获取商品的物流信息
	 */
	public function get_good_express() {
		$this->searchTemporary = [ 
						'id' => $this->data ['id'] 
		];
		$this->searchField = 'id,exp_id,express_id';
		$order_info = parent::getFindOne ();
		
		if (empty ( $order_info )) {
			return [ ];
		}
		
		$express = CommonModel::express ();
		$express_info = $express->getExpressInfo ( $order_info ['exp_id'] );
		
		if (empty ( $express )) {
			return [ ];
		}
		
		$result = [ 
						'OrderCode' => $order_info ['id'],
						'ShipperCode' => $express_info ['code'],
						'LogisticCode' => $order_info ['express_id'] 
		];
		return $result;
	}
	
		/**
	 * 取消订单
	 */
	public function cancelOrder() :bool
	{
		$id = $this->data ['id'];
		
		$this->modelObj->startTrans();
		
		$status = $this->saveData();
		
		if (!$this->traceStation($status)) {
			$this->errorMessage .= '、取消订单失败'; 
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultBySave() :array
	{
		$data = $this->data;
		
		$data[OrderModel::$orderStatus_d] = -1;
		
		return $data;
	}
	
	public function logicGetOrderPriceById() {
		$order_id = $this->data ['id'];
		return M ( 'order' )->where ( [ 
						'order_sn_id' => $order_id 
		] )->getField ( 'price_sum' );
	}
	// 获取我的订单
	public function getOrder($where) {
		$page = empty($this->data['page'])?0:$this->data['page'];
		$field = "id,order_sn_id,order_status,create_time,comment_status,store_id,price_sum,express_id,exp_id,user_id";
		$rest = $this->modelObj->field($field)->where($where)->page($page.",10")->order("create_time DESC")->select();
		$count = $this->modelObj->where($where)->count();
		$Page = new \Think\Page($count,10);
		$show = $Page->show();
		$page_size = $Page->totalPages;
		if (! empty ( $rest )) {
			$order_goods = $this->order_goods_model->getGoodsByOrder ( $rest );
			$res = $this->storeModel->getStoreName ( $order_goods );
			return array (
							"status" => 1,
							"data" => array("list"=>$res,"count"=>$count,"totalPages"=>$page_size,"page"=>$page),
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
	// 获取订单详情
	public function getOrderDetails() {
		$where ['id'] = $this->data ['id'];
		$field = 'id,order_sn_id,order_status,create_time,comment_status,store_id,price_sum,express_id,exp_id,remarks,pay_type,platform,shipping_monery,translate,delivery_time,pay_time,address_id,user_id,coupon_deductible';
		$way = 'find';
		$rest = $this->modelObj->getOrderByWhere ( $where, $field, $way );
		if (empty ( $rest )) {
			return array (
							"status" => 0,
							"data" => "",
							"message" => "暂无数据" 
			);
		}
		
		$rest ['pay_type'] = M ( 'PayType' )->where ( [ 
						'id' => $rest ['pay_type'] 
		] )->getField ( 'type_name' );
		
		$rest ['exp_name'] = M ( 'express' )->where ( [ 
						'id' => $rest ['exp_id'] 
		] )->getField ( 'name' );
		
		if ($rest ['translate'] == 1) {
			$type = M ( "OrderInvoice" )->where ( [ 
							'id' => $rest ['id'] 
			] )->getField ( "type_id" );
			$rest ['translate'] = M ( "InvoiceType" )->where ( [ 
							'id' => $type 
			] )->getField ( "name" );
		} else {
			$rest ['translate'] = "不需要发票";
		}
		$rest ['address'] = $this->userAddressModel->getUserAddress ( $rest ['address_id'], $rest ['user_id'] );
		
		$order_goods = $this->order_goods_model->getGoodsByOrderOne ( $rest );
		$res = $this->storeModel->getStoreNameByStoreID ( $order_goods );
		
		if ($rest ['order_status'] == 0) {
			
			SessionManager::SET_ORDER_DATA( [
					$rest ['id'] => [
							'total_money' => sprintf("%.2f", $rest ['price_sum'] + $rest ['shipping_monery'] - $rest['coupon_deductible'], 2 ),
							'order_id'	  => $rest ['id'],
							'store_id'    => $rest ['store_id']
					]
			]);
			
			SessionManager::SET_ORDER_GOODS_DATA($order_goods['goods']);
			
			// 订单类型 0 普通订单 1优惠套餐订单
			SessionGet::getInstance('order_type_by_user', 0)->set();
			
		}
		return array (
						"status" => 1,
						"data" => $res,
						"message" => "获取成功" 
		);
	}
	// 获取订单信息
	public function getOrderInfo() {
		$where ['id'] = $this->data ['id'];
		$field = "id";
		$way = 'find';
		$rest = $this->modelObj->getOrderByWhere ( $where, $field, $way );
		$order_goods = $this->order_goods_model->getGoodsByOrderOne ( $rest );
		return array (
						"status" => 1,
						"data" => $order_goods,
						"message" => "获取成功" 
		);
	}
	// 申请售后获取订单信息
	public function getOrderInfoByReturn() {
		$post = $this->data;
		$where ['id'] = $post ['id'];
		$field = "id,order_sn_id,create_time,store_id";
		$way = 'find';
		$order = $this->modelObj->getOrderByWhere ( $where, $field, $way );
		$o_where ['order_id'] = $post ['id'];
		$o_where ['goods_id'] = $post ['goods_id'];
		$o_field = "order_id,goods_id,goods_num,goods_price";
		$goods = $this->order_goods_model->getGoodsByOrderByWhere ( $o_where, $o_field );
		$goods ['order_sn_id'] = $order ['order_sn_id'];
		$goods ['create_time'] = $order ['create_time'];
		$goods ['store_id'] = $order ['store_id'];
		return array (
						"status" => 1,
						"data" => $goods,
						"message" => "获取成功" 
		);
	}
	
	/**
	 * 支付回调成功后 修改订单状态
	 */
	public function paySuccessEditStatus() :bool
	{
		if (empty ( $this->data ['id'] )) {
			return false;
		}
		$this->modelObj->startTrans ();
		
		$status = $this->modelObj->where ( OrderModel::$id_d . ' in (%s)', $this->data ['id'] )->save ( [ 
						OrderModel::$payTime_d => time (),
						OrderModel::$orderStatus_d => OrderModel::YesPaid,
						OrderModel::$payType_d => $this->data ['pay_conf'] ['pay_type_id'],
						OrderModel::$platform_d => $this->data ['pay_conf'] ['type'] 
		] );
		
		if (! $this->traceStation ( $status )) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 获取用户编号
	 *
	 * @return mixed|NULL|unknown|string[]|unknown[]|object
	 */
	public function getUserId() {
		$userId = $this->modelObj->where ( OrderModel::$id_d . '=:id' )->bind ( [ 
						':id' => $this->data ['id'] 
		] )->getField ( OrderModel::$userId_d );
		
		if (! $userId) {
			return 0;
		}
		return ( int ) $userId;
	}
	
	// 获取订单状态
	public function getOrderStatus() {
		$data = $this->modelObj->where ( OrderModel::$id_d . ' in (%s)', $this->data ['id'] )->getField ( OrderModel::$id_d . ',' . OrderModel::$orderStatus_d );
		
		return $data;
	}
	
	/**
	 * 检查订单状态
	 *
	 * @return boolean
	 */
	public function checkOrderStatus() {
		$data = $this->getOrderStatus ();
		
		if (empty ( $data )) {
			return false;
		}
		
		$message = '';
		foreach ( $data as $key => $value ) {
			if ($value == OrderModel::YesPaid) {
				continue;
			}
			$this->errorMessage = '有订单出现了问题 订单号：' . $value . '，请联系客服';
			return false;
		}
		return true;
	}
	
	/**
	 * 验证
	 */
	public function getValidateByOrderId() {
		return [ 
						OrderModel::$id_d => [ 
										'checkStringIsNumber' => true 
						] 
		];
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
	
	/**
	 * 提交事务
	 * @return bool
	 */
	public function submitTranstion() :bool
	{
		return $this->modelObj->commit();
	}
	
	/**
	 * 已签收
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	public function getOverTime() :bool
	{
	    $this->modelObj->startTrans();
	    
	    $data = [];
	    
	    $data[OrderModel::$overTime_d] = time();
	    
	    $data[OrderModel::$orderStatus_d] = 4;
	    
	    $data[OrderModel::$id_d] = $this->data['id'];
	    
	    if (!$this->traceStation($this->modelObj->save($data))) {
	        $this->errorMessage = '签收失败';
	        return false;
	    }
	    return true;
	}
}
