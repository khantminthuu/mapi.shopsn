<?php
namespace Common\Pay;

use Common\TraitClass\PayTrait;
use Common\Logic\OrderWxpayLogic;
use Common\Pay\Component\PayGenialPublicTrait;
use Think\SessionGet;

/**
 * 余额充值
 * @author Administrator
 *
 */
class BalanceRechargeByWxPay 
{
	use PayTrait;
	
	use PayGenialPublicTrait;
	
	/**
	 * 余额充值
	 * @var integer
	 */
	const BALANCE_RECHARGE = 1;
	
	/**
	 * 架构方法
	 * @param array $config
	 * @param array $orderData
	 */
	public function __construct(array $config = [], array $orderData = [])
	{
		$this->config = $config;
		
		$this->description = '余额充值';
		
		$this->orderData = $orderData;
	}
	
	/**
	 * 微信支付
	 */
	public function pay()
	{
		$info = $this->orderData ;
		
		$priceSum = $info['money'];
		
		if (bccomp($priceSum, 0.00, 2) === -1) {
			return [
				'data'=> '',
				'message'=> '充值金额异常 ',
				'status'=> '0'
			];
		}
		
		$orderWxPay = new OrderWxpayLogic($this->orderData, '', static::BALANCE_RECHARGE);
		
		$status = $orderWxPay->getResultByPay();
		
		if ($status == false) {
			return [
				'data'=> '',
				'message'=>  '微信订单号生成异常',
				'status'=>  '0'
			];
		}
		
		$payConfig = $this->getPayConfig($this->config);
		
		$this->priceSum = $priceSum;
		
		SessionGet::getInstance('order_data_by_balance', $info)->set();
		
		$this->orderSnId = $orderWxPay->getWxOrderId();
		
		$result = $this->component();
		
		return $result;
	}
}