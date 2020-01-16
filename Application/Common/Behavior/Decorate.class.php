<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------
namespace Common\Behavior;


use Common\Logic\OrderWxpayLogic;

class Decorate
{
    public function aplipaySerial(&$param)
    {
        if (empty($param)) {
            $param = [];
        }
        
        //更改订单微信表
        $status = (new OrderWxpayLogic($param))->nofityUpdate();
        if (empty($status)) {
             $param = [];
        }
    }
    
    public function aplipayBalanceSerial(&$param)
    {
    	if (empty($param)) {
    		$param = [];
    	}
    	
    	//更改订单微信表
    	$status = (new OrderWxpayLogic($param))->nofityUpdateBySpecial();
    	
    	if (empty($status)) {
    		$param = [];
    	}
    }
}