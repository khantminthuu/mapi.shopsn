<?php
namespace Common\Pay;

use Common\TraitClass\PayTrait;
use Common\Logic\BalanceLogic;
use Common\Tool\Extend\CURL;
use Think\Cache;
use Think\SessionGet;

/**
 * 开店支付费用（微信）
 * @author Administrator
 */
class OpenShopByBalance
{
	use PayTrait;
	
	private $config = [];
	
	private $orderData = [];
	
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
				'status'=>  0
			];
		}
		
		$balanceLogic = new BalanceLogic($info, '', '开店费用');
		
		$result = $balanceLogic->openShopParse();
		
		if ($result['status'] == 0) {
			return $result;
		}
		
		SessionGet::getInstance('order_data_by_open_shop', $this->orderData)->set();
		
		$ley = md5(base64_encode(time()).'_ddkf');
		

		$config = $this->config;
		
		$token = $config['token'];
		
		unset($config['token']);
		
		SessionGet::getInstance('pay_config_by_user', $config)->set();
		
		SessionGet::getInstance('ley_user', $ley)->set();
		
		$result['data'] = $config['notify_url'];
		
		$result['ley_user'] = $ley;
		
		return $result;        
	}
}