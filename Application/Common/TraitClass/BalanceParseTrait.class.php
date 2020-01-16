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
namespace Common\TraitClass;

use Think\Hook;
use Common\Logic\RechargeLogic;
use Common\Logic\BalanceLogic;
use Think\Cache;
use Think\Log;
use Think\SessionGet;

trait BalanceParseTrait
{
    protected $result = [];
    
    protected $tradeNo = 0;
    
    protected function parseByBalance ()
    {
        //防止恶意点击造成损失
        
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$key = $userId.'_'.$this->tradeNo;
    	
    	$result = Cache::getInstance('', ['expire' => 1440])->get($key);
    	
    	$day = date('y_m_d');
    	
    	Log::write(print_r($result, true).'---result--'.__FILE__.'---'.__LINE__, Log::INFO, '', './Log/reacharge/'.$day);
    	if (!empty($result)) {
    		// 已经修改
    		return true;
    	}
    	
    	//支付配置
    	$payConfig = SessionGet::getInstance('pay_config_by_user')->get();
    	
    	//充值信息
    	$orderRecharge = SessionGet::getInstance('order_data_by_balance')->get();
    	
        $userRecharge = new RechargeLogic(['id' => $orderRecharge['order_id'], 'pay_id' => $payConfig['pay_type_id']]);
        
        //修改余额充值记录表
        
        $status = $userRecharge->saveStatus();
        if (empty($status)) {
        	
        	Log::write($status.'---result--'.'--修改余额充值记录表-', Log::ERR, '', './Log/reacharge/'.$day);
        	
            return false;
        }
        
        $param = [
        	'order_sn_id' => $orderRecharge['order_id'],
            'trade_no'    => $this->tradeNo,
        	'wx_order_id' => $orderRecharge['order_id'],
            'type'        => 4
        ];
       
        Log::write($status.'---修改支付状态--'.print_r($param, true), Log::INFO, '', './Log/reacharge/'.$day.'.txt');
        
        
        Hook::listen('aplipayBalanceSerial', $param);
        if (empty($param)) {
        	
        	Log::write($status.'---修改支付状态--', Log::ERR, '', './Log/reacharge/'.$day.'.txt');
        	
            return false;
        }
        
        $data = $this->result;
        
        $data['trade_no'] = $this->tradeNo;
        
        $balanceLogic = new BalanceLogic($data);
        
        $status = $balanceLogic->rechargeMoney();
        Log::write($status.'---修改充值订单---', Log::INFO, '', './Log/reacharge/'.$day);
        if (empty($status)) {
           return false;
        }
        return true;
    }
}