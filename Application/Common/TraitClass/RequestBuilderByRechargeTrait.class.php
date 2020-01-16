<?php
namespace Common\TraitClass;

use Extend\Alipay\Wappay\BuilderModel\AlipayTradeWapPayContentBuilder;
use Think\SessionGet;

/**
 * 请求构造参数S h o p s N
 * @author Administrator
 */
trait RequestBuilderByRechargeTrait
{
	private $orderData = [];
	
	private $config = [];
	
	private $param = [];
	
	/**
	 * 获取支付请求数据
	 * @param string $body
	 * @param string $subject
	 * @return AlipayTradeWapPayContentBuilder
	 */
	private function getRequstBuilder()
	{
		
		$token = $this->config['token'];
		
		$config = $this->config;
		
		unset($config['token']);
		
		SessionGet::getInstance('pay_config_by_user', $config)->set();
		
		$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
		$payRequestBuilder->setBody($this->param['body']);
		$payRequestBuilder->setSubject($this->param['subject']);
		$payRequestBuilder->setOutTradeNo($this->orderData['order_sn']);
		$payRequestBuilder->setTotalAmount($this->orderData['money']);
		$payRequestBuilder->setPassbackParamst(['token' => $token]);
		$payRequestBuilder->setTimeExpress('30m');
		
		return $payRequestBuilder;
	}
	
	/**
	 * 检查金额
	 */
	private function checkMoney()
	{
		if (bccomp($this->orderData['money'], 0.00, 2) === -1 ) {
			return [
				'data'=> '',
				'message'=>  '价格异常',
				'status'=>  0
			];
		}
		return [];
	}
	
}