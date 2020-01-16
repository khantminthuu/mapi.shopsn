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
namespace Common\TraitClass;

use Extend\Alipay\Wappay\Service\AlipayTradeService;

trait AlipayTradeServiceTrait
{
	private $alipayConfig = [];
	
	/**
	 * 支付
	 * @param unknown $payRequestBuilder
	 * @return string[]|number[]|\Extend\Alipay\Wappay\Service\$response[]
	 */
	private function getAlipayTrade($payRequestBuilder)
	{
		$config = [
			'app_id' => $this->alipayConfig['pay_account'],
			'merchant_private_key'=> $this->alipayConfig['private_pem'],
			'alipay_public_key' => $this->alipayConfig['public_pem'],
			'return_url'=> $this->alipayConfig['return_url'],
			'notify_url'=> $this->alipayConfig['notify_url']
		];
		$payResponse = new AlipayTradeService($config);
		
		$result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
		
		return [
			'data'=> $result,
			'message'=> empty($result) ? '失败' : '成功',
			'status'=> empty($result) ? 0 : 1
		];
	}
}