<?php
declare(strict_types = 1);

namespace Common\Logic;
use Common\Model\CommonModel;
use Common\Model\OrderIntegralModel;
use Common\Tool\Tool;
use Common\SessionParse\SessionManager;
use Think\SessionGet;
/**
 * 逻辑处理层
 * 
 */
class OrderIntegralLogic extends AbstractGetDataLogic
{
	/**
	 * 积分订单
	 * @var integer
	 */
	private $orderIntegralInsertId = 0;
	
	/**
	 *
	 * @return number
	 */
	public function getOrderIntegralInsertId()
	{
		return $this->orderIntegralInsertId;
	}
	
	/**
	 * 支付数据
	 * @var array
	 */
	private $payData = [];
	
	
	
	/**
	 * @return array
	 */
	public function getPayData() :array
	{
		return $this->payData;
	}
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;

        $this->splitKey = $split;

        $this->modelObj = new OrderIntegralModel();
      
    }
    
    /**
     * 返回验证数据
     */
    public function getValidateByBuildOrder() :array
    {
    	$message = [
    		'remarks' => [
    			'specialCharFilter' => '积分备注',
    		],
    		'address_id' => [
    			'number' => '必须输入收货地址',
    		],
    	];
    	return $message;
    }
    

    /**
     * 积分兑换-立即兑换  参数验证
     * 
     */
    public function getValidateByOrder()
    {
        $message = [
            'orderId' => [
                'required' => '订单ID必填',
                'number'   => '订单ID必须是数字',
            ],
        ];
        return $message;
    }

    public function getValidateByOrderId(){
        $message = [
            'id' => [
                'required' => '订单ID必须',
            ],
        ];
        return $message;
    }
    /**
     * 获取结果
     */
    public function getResult()
    {

    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName() :string
     */
    public function getModelClassName() :string
    {
        return OrderIntegralModel::class;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment() :array
    {
        return [

        ];
    }

    /**
     * 积分兑换处理
     */
    public function commitConfirm(){
    	
    	$goods = [];
    	
    	$freight = [];
    	
    	//  验证是否会出现异常
    	$freight = SessionManager::GET_FREIGHT_MONEY();
    	
    	$goods= SessionManager::GET_GOODS_DATA_SOURCE();
    	
    	
    	$this->modelObj->startTrans();
    	
    	$insertId = $this->addData();
    	
    	if (!$this->traceStation($insertId)) {
    		return false;
    	}
    	
    	$totalMoney = $goods['price_sum'] + $freight[$goods['store_id']];
    	
    	$payData = [];
    	
    	$payData[0] = [
    			'order_id' => $insertId,
    			'store_id' => $goods['store_id'],
    			'integral' => $goods['integral'],
    			'total_money' => sprintf("%.2f", $totalMoney)
    	];
    	
    	
    	$this->payData = $payData;
    	
    	$this->orderIntegralInsertId = $insertId;
    	
    	return true;
    }
    
    /**
     * 获取我的积分兑换订单详情
     *
     */
    public function getConfirmInfo(){
    	$userId = session('user_id');
    	$params  = $this->data;
    	$data = $this->modelObj->getConfirmInfo($userId,$params);
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	if ($data['order_status'] == 0) {
    		
	    	
	    	$payData = [];
	    	
	    	$totalMoney = $data['price_sum'] + $data['shipping_monery'];
	    	
	    	$payData[$data['id']] = [
	    		'order_id' => $data['id'],
	    		'store_id' => $data['store_id'],
	    		'integral' => $data['interagl_total'],
	    		'total_money' => sprintf("%.2f", $totalMoney)
	    	];
	    	
	    	$orderGoodsData = [];
	    	
	    	$orderGoodsData = [
	    		'goods_num' => $data['goods_num'],
	    		'goods_price' => $data['money'],
	    		'goods_id' => $data['goods_id']
	    	];
    		
	    	SessionManager::SET_ORDER_DATA($payData);
	    	
	    	SessionManager::SET_ORDER_GOODS_DATA([$orderGoodsData]);
    	}
    	
    	return $data;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
     */
    protected function getParseResultByAdd() :array
    {
    	$goods = SessionManager::GET_GOODS_DATA_SOURCE();
    	
    	$freightMoney = SessionManager::GET_FREIGHT_MONEY();
    	
    	$time = time();
    	
    	//积分兑换  生成订单
    	$orderSn = Tool::connect('Token')->toGUID();
    	
    	$orderData = [];
    	
    	$orderData[OrderIntegralModel::$orderSn_d] = $orderSn;
    	//总积分
    	$orderData[OrderIntegralModel::$interaglTotal_d] = $goods['integral'];
    	$orderData[OrderIntegralModel::$addressId_d] = $this->data['address_id'];
    	$orderData[OrderIntegralModel::$userId_d] = SessionGet::getInstance('user_id')->get();
    	$orderData[OrderIntegralModel::$createTime_d] = $time;
    	$orderData[OrderIntegralModel::$payTime_d] = 0;
    	$orderData[OrderIntegralModel::$overTime_d] = 0;
    	$orderData[OrderIntegralModel::$orderStatus_d] = 0;
    	$orderData[OrderIntegralModel::$commentStatus_d] = 0;
    	$orderData[OrderIntegralModel::$payType_d] = 0;
    	$orderData[OrderIntegralModel::$remarks_d] = isset($this->data['remarks']) ? $this->data['remarks'] : '';
    	$orderData[OrderIntegralModel::$status_d] = 0;
    	$orderData[OrderIntegralModel::$translate_d] = 0;
    	$orderData[OrderIntegralModel::$platform_d] = 2 ;
    	$orderData[OrderIntegralModel::$orderType_d] = 0 ;
    	$orderData[OrderIntegralModel::$storeId_d] = $goods['store_id'];
    	$orderData[OrderIntegralModel::$priceSum_d] = $goods['price_sum'];
    	
    	$orderData[OrderIntegralModel::$shippingMonery_d] =  $freightMoney[$goods['store_id']];
    	
    	return $orderData;
    }
    
    /**
     * 支付回调成功后 修改订单状态
     */
    public function paySuccessEditStatus() {
    	if (empty ( $this->data ['id'] )) {
    		return false;
    	}
    	$this->modelObj->startTrans ();
    	
    	$status = $this->modelObj->where ( OrderIntegralModel::$id_d . ' in (%s)', $this->data ['id'] )
    		->save ( [
    			OrderIntegralModel::$payTime_d => time (),
    			OrderIntegralModel::$orderStatus_d => 1,
    			OrderIntegralModel::$payType_d => $this->data ['pay_conf'] ['pay_type_id'],
    			OrderIntegralModel::$platform_d => $this->data ['pay_conf'] ['type']
    		] );
    	
    	if (! $this->traceStation ( $status )) {
    		return false;
    	}
    	
    	return true;
    }
    
    
    /**
     * 我的积分兑换列表
     */
    public function getMyConfirm(){
        $userId = session("user_id");
        $data = $this->modelObj->getUserConfirm($userId);
        return $data;
    }

  

    /**
     * 积分兑换商品 - 确认收货
     *
     */
    public function confirmGetgoods(){
        $userId = session('user_id');
        $orserId = $this->data['id'];
        $result = $this->modelObj->confirmGetgoods($userId,$orserId);
        if ($result){
            return true;
        }
        return false;
    }

    /**
     *  取消订单
     *
     */
    public function cancel_order(){
        $user_id  = session("user_id");
        $id = $this->data['id'];
        $result = CommonModel::get_modle("OrderIntegral")->cancel_order($user_id,$id);
        return $result;

    }
    //删除订单
    public function getDelOrder(){
        $where['id'] = $this->data['id'];
        $data['status'] = 1;
        $result = $this->modelObj->where($where)->save($data);
        if(!$result){
            return array("status"=>0,"message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>"");

    }
    //再次购买订单
    public function getNewBuyOrder(){
        $id = $this->data['id'];
        $data = $this->modelObj->getNewBuyOrderById($id);
        return $data;
    }
}
