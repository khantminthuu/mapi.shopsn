<?php
namespace Home\Controller;

use Common\Controller\AbstractPayOrderController;

/**
 * 套餐支付控制器
 * @author Administrator
 */
class PayOrderByPackageController extends AbstractPayOrderController
{
	/**
	 * @param array $args
	 */
	public function __construct(array $args = []) {
		
		$this->specialStatus = 1;
		parent::__construct($args);
		
	}

}