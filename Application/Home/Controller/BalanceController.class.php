<?php
namespace Home\Controller;

use Common\Logic\BalanceLogic;
use Common\TraitClass\InitControllerTrait;

class BalanceController
{
	use InitControllerTrait;
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{   
		$this->args = $args;
		
		$this->_initUser();
		
		$this->logic = new BalanceLogic($args);
	}
	
	/**
	 * 獲取餘額
	 */
	public function getBalance()
	{
		$ret = $this->logic->getBalance();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
	}
}