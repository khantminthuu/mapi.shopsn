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

/**
 * 余额支付行为
 * @author Administrator
 */
class Balance
{
    public function aplipaySerial(&$param)
    {
       return $param; 
    }
    
    
    public function aplipayBalanceSerial(& $param) {
    	
    }
}