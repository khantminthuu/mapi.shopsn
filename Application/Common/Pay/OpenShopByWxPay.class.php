<?php
namespace Common\Pay;

use Common\Logic\OrderWxpayLogic;
use Common\TraitClass\PayTrait;
use Common\Pay\Component\PayGenialPublicTrait;
use Think\SessionGet;

/**
 * 开店支付费用（微信）
 * @author Administrator
 */
class OpenShopByWxPay
{
	use PayTrait;
	
	use PayGenialPublicTrait;
	
	
	/**
	 * 开店支付
	 * @var integer
	 */
	const OPEN_SHOP_PAY = 2;
	
	/**
	 * 架构方法
	 * @param array $config
	 * @param array $orderData
	 */
	public function __construct(array $config = [], array $orderData = [])
	{
		$this->config = $config;
		
		$this->orderData = $orderData;
		
		$this->description = '开店支付';
	}
	
	/**
	 * 微信支付
	 */
	public function pay()
	{
		$storeData = SessionGet::getInstance('store_data_by_person')->get();
		
		if (empty($storeData)) {
			return [
					'data'=> '',
					'message'=>  '充值金额异常 ',
					'status'=>  0
			];
		}
		$info = $this->orderData ;
		
		$priceSum = $info['money'];
		
		if (bccomp($priceSum, 0.00, 2) === -1) {
			return [
				'data'=> '',
				'message'=>  '充值金额异常 ',
				'status'=>  '0'
			];
		}
		
		$orderWxPay = new OrderWxpayLogic($this->orderData, '', static::OPEN_SHOP_PAY);
		
		$status = $orderWxPay->getResultByPay();
		
		if ($status == false) {
			return [
				'data'=> '',
				'message'=> '微信订单号生成异常',
				'status'=>  '0'
			];
		}
		
		$payConfig = $this->getPayConfig($this->config);
		
		$this->priceSum = $priceSum;
		
		SessionGet::getInstance('order_data_by_open_shop', $info)->set();
		
		$this->orderSnId = $orderWxPay->getWxOrderId();
		
		$result = $this->component();
		
		return $result;
	}
}