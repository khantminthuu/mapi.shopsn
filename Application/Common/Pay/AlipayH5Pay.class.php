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
use Common\TraitClass\PayTrait;
use Extend\Alipay\Wappay\Service\AlipayTradeService;
use Extend\Alipay\Wappay\BuilderModel\AlipayTradeWapPayContentBuilder;
use Common\Tool\Tool;
use Think\SessionGet;

/**
 * 支付宝支付
 * @author Administrator
 *
 */
class AlipayH5Pay
{
	use PayTrait;
	
	private $config = [];
	
	private $orderData = [];
	
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
		$info = $this->orderData ;
		$priceSum = $this->totaMoney();
		
		if (bccomp($priceSum, 0.00, 2) === -1 || $this->isPass === false) {
			return [
				'data'=> '',
				'message'=>  '价格异常 或者 运费计算错误',
				'status'=>  0
			];
		}
		
		$payConfig = $this->config;
		
		$token = $payConfig['token'];
		
		unset($payConfig['token']);
		
		SessionGet::getInstance('pay_config_by_user', $payConfig)->set();
		
		$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
		$payRequestBuilder->setBody('OrderPay');
		$payRequestBuilder->setSubject('多商户商品支付');
		$payRequestBuilder->setOutTradeNo(Tool::connect('Token')->toGUID());
		$payRequestBuilder->setTotalAmount($priceSum);
		$payRequestBuilder->setPassbackParamst([
			'token' => $token, 
		]);
		$payRequestBuilder->setTimeExpress('10m');
		
		$config = [
			'app_id' => $this->config['pay_account'],
			'merchant_private_key'=> $this->config['private_pem'],
			'alipay_public_key' => $this->config['public_pem'],
			'return_url'=> $this->config['return_url'],
			'notify_url'=> $this->config['notify_url']
		];
		$payResponse = new AlipayTradeService($config);
		
		$result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
		
		return [
			'data'=> $result,
			'message'=> empty($result) ? '失败  ' : '成功 ',
			'status'=> empty($result) ? 0 : 1,
			'tds' => 'S h o p s N'
		];
	}
}