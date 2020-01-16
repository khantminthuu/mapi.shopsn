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
use Common\Logic\BalanceLogic;
use Think\SessionGet;

/**
 * 余额支付
 */
class BalancePay 
{
    use PayTrait;
    
    private $config = [];
    
    private $orderData = [];
    
    /**
     *
     */
    public function __construct(array $config = [], array $orderData = [])
    {
    	$this->config = $config;
    	
    	$this->orderData = $orderData;
    }
    
    /**
     * 余额支付
     */
    public function pay()
    {
    	$info = $this->orderData;
        if (empty($info)) {
        	return [
        		'data'=> '',
        		'message'=>  '价格异常',
        		'status'=>  0
        	];
        }
        
        $balanceLogic = new BalanceLogic($info, '', '余额支付');
        
        $result = $balanceLogic->getResult();
        if ($result['status'] == 0) {
        	return $result;
        }
        
        $config = $this->config;
        
        $token = $config['token'];
        
        unset($config['token']);
        
        SessionGet::getInstance('pay_config_by_user', $config)->set();
        
        $result['data'] = $config['notify_url'];
        
        return $result;
    }
}