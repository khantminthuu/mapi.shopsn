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
declare(strict_types = 1);
namespace Common\TraitClass;

use Extend\Alipay\Wappay\Service\AlipayTradeService;
use Think\Log;
use Think\SessionGet;

/**
 * 支付宝回调验证
 */
trait AlipayNotifyTrait
{
	
	//支付配置
	private $payConfData = [];
	
	private $data = [];
	
	/**
	 * 支付返回的自定义参数
	 * @var array
	 */
	private $resource = [];
	
    protected $returnURL = '';

    public function alipayResultParse()
    {
    	
    	$day = date('y_m_d');
    	
    	if ($this->data['trade_status'] != 'TRADE_FINISHED' && $this->data['trade_status'] != 'TRADE_SUCCESS') {
    		
    		Log::write('支付宝-支付失败-'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_alipay_param_'.$day.'.txt');
    		
    		return [];
    	}
    	
    	$resource = $this->resource;
    	
    	if (empty($resource)) {
    		
    		Log::write('支付宝自定义参数-异常-'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_alipay_param_'.$day.'.txt');
    		
    		return [];
    	}
    	
    	$config = SessionGet::getInstance('pay_config_by_user')->get();
     
    	if (empty($config)) {
    		
    		Log::write('订单处理支付配置--'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_pay_'.$day.'.txt');
    		
    		return [];
        }
        
        $alipayConfig['app_id'] = $config['pay_account'];
        $alipayConfig['merchant_private_key'] = $config['private_pem'];
        
        $alipayConfig['alipay_public_key'] = $config['public_pem'];
        
        $alipayNotify = new AlipayTradeService($alipayConfig);
       
        $verifyResult = $alipayNotify->check($this->data);
       
        if (! $verifyResult) {
        	
        	Log::write('支付宝check-sign-异常-'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_sign_param_'.$day.'.txt');
        	
        	return [];
        }
        
        $this->payConfData = $config;
        return $resource;
    }
	
    private function parseResultConf()
    {
    	$data = $this->data;
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$data = json_decode($this->data['passback_params'], true);
    	
    	$this->resource = $data;
    	
    	return $data;
    }
    
    private function msg($status)
    {
    	if (empty($status)) {
    		echo 'ERROR';
    		Log::write('参数-异常-'.print_r($status, true), Log::ERR, '', './Log/order/order_comm'.date('y_m_d').'.txt');
    		die();
    	}
    }
}