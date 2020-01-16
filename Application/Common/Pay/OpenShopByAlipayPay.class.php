<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Pay;

use Common\TraitClass\AlipayTradeServiceTrait;
use Common\TraitClass\RequestBuilderByRechargeTrait;
use Think\SessionGet;

/**
 * 支付宝开店支付
 * @author 王强
 */
class OpenShopByAlipayPay
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
	 * 微信支付
	 */
	public function pay()
	{
		$data = $this->checkMoney();
		if (!empty($data)) {
			return $data;
		}
		
		$this->param = [
			'body' => 'OpenShop',
			'subject' => '开店支付'
		];
		
		SessionGet::getInstance('order_data_by_open_shop', $this->orderData)->set();
		
		$payRequst = $this->getRequstBuilder();
		
		$this->alipayConfig = $this->config;
		
		$result = $this->getAlipayTrade($payRequst);
		
		return $result;
	}
	
	/**
	 * 获取自定义参数
	 */
	protected function getPassbackParamst(array $map)
	{
		$data = SessionGet::getInstance('store_data_by_person')->get();
		
		$map['store_id'] = $data['id'];
		
		$map['store_type'] = $this->orderData['store_type'];
		
		return $map;
	}
}