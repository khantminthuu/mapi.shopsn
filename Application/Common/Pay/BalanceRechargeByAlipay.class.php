<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
namespace Common\Pay;

use Common\TraitClass\RequestBuilderByRechargeTrait;
use Common\TraitClass\AlipayTradeServiceTrait;
use Think\SessionGet;

/**
 * 余额充值
 * @author Administrator
 */
class BalanceRechargeByAlipay
{
	use RequestBuilderByRechargeTrait;
	use AlipayTradeServiceTrait;
	
	
	/**
	 * 架构方法
	 * @param array $config
	 * @param array $orderData
	 */
	public function __construct(array $config = [], array $orderData = [])
	{
		$this->config = $config;
	
		$this->orderData = $orderData;
	}
	
	
	/**
	 * 支付宝支付
	 */
	public function pay()
	{
		$data = $this->checkMoney();
		if (!empty($data)) {
			return $data;
		}
		
		$this->param = [
			'body' => 'RechargePay',
			'subject' => '余额充值'
		];
		
		SessionGet::getInstance('order_data_by_balance', $this->orderData)->set();
		
		$payRequst = $this->getRequstBuilder();
		
		$this->alipayConfig = $this->config;
		
		$result = $this->getAlipayTrade($payRequst);
		
		return $result;
	}
}