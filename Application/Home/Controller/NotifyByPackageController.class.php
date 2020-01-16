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

namespace Home\Controller;

use Think\Controller;

use Common\Controller\AbstractNotifyController;

/**
 * 套餐支付通知
 * @author Administrator
 */
class NotifyByPackageController extends AbstractNotifyController
{
	/**
	 * 套餐支付
	 * @var integer
	 */
	const PlanToPay = 1;
	
	public function __construct(array $args = [])
	{
		$this->headerOriginInit();
		
		$this->orderType = static::PlanToPay;
	}
}