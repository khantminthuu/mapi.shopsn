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
namespace User\Controller;

use Think\Controller;
use Common\TraitClass\WxNofityTrait;
use Common\TraitClass\AlipayNotifyTrait;
use Common\TraitClass\BalanceParseTrait;
use Common\Behavior\Decorate;
use Common\Behavior\AlipaySerialNumber;
use Common\TraitClass\InitControllerTrait;
use Think\Hook;
use Common\TraitClass\PayTrait;
use Think\SessionGet;

class RechargeNofityController
{
	use InitControllerTrait;
    use WxNofityTrait;
    use AlipayNotifyTrait;
    use BalanceParseTrait;
    
    use PayTrait;
    
    /**
     * 余额充值相关页面
     * @var string
     */
    const RECHARGE_RELEVANT = 'Nofity/rechargeRelevant';
    
    public function __construct(array $args)
    {
    	
    	$this->headerOriginInit();
    }
    
    /**
     * 支付宝余额充值通知
     */
    public function aplipayRechargeNofity ()
    {
    	
    	$this->data = $_POST;
    	
    	$alipayConf = $this->parseResultConf();
    	
    	$this->msg($alipayConf);
    	
    	$this->args = $alipayConf;
    	
    	$this->sessionInit();
    	
    	$data = $this->parseResultConf();
    	
    	$this->msg($data);
    	
    	$this->tradeNo = $this->data['trade_no'];
    	
    	Hook::add('aplipayBalanceSerial', AlipaySerialNumber::class);
    	
    	$data['total_amount'] = $this->data['total_amount'];
    	
    	$this->result = $data;
    	
    	$status = $this->parseByBalance();
    	
    	$this->msg($status);
		
		echo 'SUCCESS';
		die();
    	
    }
    
    /**
     * 微信余额通知
     */
    public function rechargeWxNotify()
    {
    	$this->returnData= file_get_contents('php://input');
    	
    	$this->args = $this->getTheCustomParamter();
    	
    	$this->sessionInit();
    	
    	$payConfig = SessionGet::getInstance('pay_config_by_user')->get();
    	
    	$this->msg($payConfig);
    	
    	$this->getPayConfig($payConfig);
    	
    	$status = $this->nofityWx();
    	
    	$this->msg($status);
    	
    	Hook::add('aplipayBalanceSerial', Decorate::class);
    	
    	$this->tradeNo = $this->args['out_trade_no'];
    	
    	$payParam = [];
    	
    	$payParam['total_amount'] = $this->args['total_fee']/100;
    	
    	$this->result = $payParam;
    	
    	$status = $this->parseByBalance();
    	
    	$this->msg($status);
    	
    	echo 'SUCCESS';die();
    }
    
    private function msg($status)
    {
    	if (empty($status)) {
    		echo 'ERROR';
    		die();
    	}
    }
}