<?php
namespace Home\Controller;


use Common\Controller\AbstractPayOrderController;
use Common\Logic\IntegralUseLogic;
use Think\SessionGet;
use Common\Logic\UserDataLogic;

class PayOrderByIntegralController extends AbstractPayOrderController
{

	/**
	 * @param array $args
	 */
	public function __construct(array $args = []) {
		$this->specialStatus = 2;
		parent::__construct($args);
		
	}
	
	/**
	 * 积分支付处理
	 * @return array
	 */
	protected function integralCallBack() :bool
	{
		$orderData = SessionGet::getInstance('order_data')->get();
		
		$userDataLogic = new UserDataLogic($orderData);
		
		$status = $userDataLogic->integralSettleMement();
		
		if (empty($status)) {
			$this->setErrorMessage($userDataLogic->getErrorMessage());
			return false;
		}
		
		$integralUseLogic = new IntegralUseLogic($orderData);
		
		$integralUseLogic->setAlreadyIntegral($userDataLogic->getIntegralShopping());
		
		$status = $integralUseLogic->addIntegralLog();
		
		if ($status === false) {
			$this->setErrorMessage($integralUseLogic->getErrorMessage());
			return false;
		}
		
		return true;
	}
}