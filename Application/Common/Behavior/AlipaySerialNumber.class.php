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
namespace Common\Behavior;

use Common\Logic\AlipaySerialNumberLogic;

class AlipaySerialNumber
{
	/**
	 * 商品支付相关
	 * @param unknown $param
	 */
    public function aplipaySerial(&$param)
    {
    	$status = (new AlipaySerialNumberLogic($param))->getResult();

        $param = empty($status) ? [] : $param;
    }
    
    /**
     * 余额支付
     * @param unknown $param
     */
    public function aplipayBalanceSerial(&$param)
    {
    	$status = (new AlipaySerialNumberLogic($param))->parseByPay();
    	
    	$param = empty($status) ? [] : $param;
    }
    
}