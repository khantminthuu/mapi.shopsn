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
declare(strict_types = 1);
namespace Common\TraitClass;

use Common\Tool\Event;
use Extend\Wxpay\WxPayConfPub;

/**
 * 支付数据处理
 * @version 1.0.1
 */
trait PayTrait
{
    private $payData; //支付数据配置
    
    private $returnMonery = 0;//退款
    
    private $isPass = true;
    
    /**
     * @return boolean
     */
    public function getIsPass()
    {
    	return $this->isPass;
    }
    
    /**
     * @return the $returnMonery
     */
    public function getReturnMonery()
    {
        return $this->returnMonery;
    }

    /**
     * @param number $returnMonery
     */
    public function setReturnMonery($returnMonery)
    {
        $this->returnMonery = $returnMonery;
    }

    protected function payConfig (array $config)
    {
        
        if (empty($config) || !is_array($config)) {
            return null ;
        }
        
        $wxConfig = new \ReflectionClass(WxPayConfPub::class);
        
        $configObj = $wxConfig->newInstance();
        
        
        $wxConfigConst = $wxConfig->getStaticProperties();
      
        //添加监控触发方法
        Event::listen('payConfig', $wxConfigConst);//后台 退款时触发
       
        $count = count($config);
        $i = 0;
        foreach ($wxConfigConst as $key => $value) {
            if ($i > $count) {
                break;
            }
            $configObj::$$key = $config[$i];
            $i++;
        }
        return $configObj;
    }
    
    /**
     * @return the $payData
     */
    public function getPayData()
    {
        return $this->payData;
    }
    
    /**
     * @param field_type $payData
     */
    public function setPayData($payData)
    {
        $this->payData = $payData;
    }
    
    /**
     * 输出错误
     */
    protected function getPayConfig ( array $data)
    {
        try {
            $config = [
                $data['pay_account'],
                $data['mchid'],
                $data['pay_key'],
                $data['open_id'],
            	$data['return_url'],
            	$data['private_pem'],
                $data['public_pem'],
            	$data['notify_url']
            ];
            $payConfig = $this->payConfig($config);
            return $payConfig;
        }catch (\Exception $e) {
            echo $e->getMessage();die();
        }
    }
    
    /**
     * 计算总价
     */
    private function totaMoney()
    {
    	$info = $this->orderData ;
    	
    	$totalMoney = 0;
    	
    	foreach ($info as $value) {
    		$totalMoney += $value['total_money'];
    	}
    	return $totalMoney;
    }
}