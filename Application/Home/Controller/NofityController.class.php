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
namespace Home\Controller;

use Think\Controller;

use Common\Controller\AbstractNotifyController;

/**
 * 商品支付通知
 * @author Administrator
 */
class NofityController extends AbstractNotifyController
{
   	/**
   	 * 普通支付
   	 * @var integer
   	 */
	const GeneralMerchandisePayment = 0;
	
    public function __construct(array $args = [])
    {
    	$this->headerOriginInit();
    	
    	$this->orderType = static::GeneralMerchandisePayment;
    }
}