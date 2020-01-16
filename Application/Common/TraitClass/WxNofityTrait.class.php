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

use Extend\Wxpay\Notify\NotifyCommonPub;
use Think\Log;

/**
 * 微信处理
 */
trait WxNofityTrait
{
	private $payConfData = [];
	
	private $returnData = [];
	
	/**
	 * 通知对象
	 * @var NotifyCommonPub
	 */
	private $notify;
	
	/**
	 * 获取自定义参数
	 */
	private function getTheCustomParamter()
	{
		// 使用通用通知接口
		$notify = new NotifyCommonPub();
		
		// 存储微信的回调
		$xml = $this->returnData;
		
		$notify->saveData($xml);
		
		$data = $notify->getData();
		
		
		
		if ($data["return_code"] == "FAIL") {
			
			$day = date('y_m_d');
			
			Log::write('订单处理-return_code-支付失败--'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_wx_check_'.$day.'.txt');
			
			return [];
		}
		
		$this->notify = $notify;
		
		$data['token'] = $data['attach'];
		
		unset($data['attach']);
		
		return $data;
	}
	
    /**
     * 通知
     */
    private function nofityWx()
    {
        $isSign = $this->notify->checkSign();
        
        if ($isSign == FALSE) {
        	
        	$day = date('y_m_d');
        	
        	Log::write('签名失败--'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/order_wx_check_'.$day.'.txt');
        	
        	return false;
        } else {
        	return true;
        }
    }
    
    public function __destruct()
    {
		unset($this->notify, $this->payConfData, $this->returnData);    
    }
}

