<?php
namespace Home\Controller;


use Common\Controller\AbstractPayOrderController;

/**
 * 订单支付
 * @author Administrator
 */
class PayOrderController extends AbstractPayOrderController
{
	
	/**
	 * @param array $args
	 */
	public function __construct(array $args = []) {
		$this->specialStatus = 0;
		parent::__construct($args);
		
	}
	
}