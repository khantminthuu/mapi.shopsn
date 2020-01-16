<?php
namespace Common\Pay;

use Common\TraitClass\PayTrait;
use Extend\Alipay\Aop\Request\AlipayFundTransToaccountTransferRequest;
use Extend\Alipay\Aop\AopClient;

/**
 * 支付宝提现
 * @author Administrator
 */
class AlipayToAccountTransfer
{
	use PayTrait;
	
	private $error = '';
	
	private $data = [];
	
	public function __construct(array $data, array $payData)
	{
		//AlipaySubmit
		$this->data = $data;
		
		$this->payData = $payData;
		
	}
	
	/**
	 * @return the $error
	 */
	public function getError()
	{
		return $this->error;
	}
	
	/**
	 * 支付寶退款
	 */
	public function transfer()
	{
		
		if (empty($this->data)) {
			$this->error = '退货数据错误';
			return false;
		}
		
		// 获取支付宝配置
		$alipayConfig = $this->payData;
		
		if (empty($alipayConfig)) {
			$this->error = '支付数据错误';
			return false;
		}
		
		
		$monery = $this->data['price'];
		if (empty($monery)) {
			
			$this->error = '金额错误';
			
			return false;
			
		}
		
		$config = [];
		
		$config['app_id'] = $alipayConfig['pay_account'];
		$config['merchant_private_key'] = $alipayConfig['private_pem'];
		$config['alipay_public_key'] = $alipayConfig['public_pem'];
		
		$requestBuilder = new AlipayFundTransToaccountTransferRequest();
		
		$param = [
			'out_biz_no' => '',
			'payee_type' => 'ALIPAY_LOGONID',
			'payee_account' => 'op@',
			'amount' => '',
			'remark' => '提现'
		];
		
		$requestBuilder->setBizContent(json_encode($param, JSON_UNESCAPED_UNICODE));
		
		$aopClient = new AopClient();
		
		$aopClient->appId = $alipayConfig['pay_account'];
		
		$aopClient->rsaPrivateKey = $alipayConfig['private_pem'];
		
		$aopClient->alipayrsaPublicKey = $alipayConfig['private_pem'];
		
		
		//建立请求
		$res = $aopClient->execute($requestBuilder);
		if ($res->code != 10000) {
			
			$this->error = $res->sub_msg;
			return false;
		}
		
		return true;
	}
}