<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreJoinCompanyLogic;
use Common\Logic\StorePersonLogic;

class CanICheckInController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();
	}
	
	/**
	 * 检测是否可以入住
	 */
	public function isCheckIn()
	{
		$store = new StoreJoinCompanyLogic();
		
		$this->objController->promptPjax($store->isCheckIn(), $store->getErrorMessage());
		
		$person = new StorePersonLogic();
		
		$this->objController->promptPjax($person->isCheckIn(), $person->getErrorMessage());
		
		$this->objController->ajaxReturnData(1);
	}
}