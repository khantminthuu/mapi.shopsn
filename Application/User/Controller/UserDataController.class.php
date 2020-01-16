<?php
declare(strict_types = 1);
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\UserDataLogic;

class UserDataController
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
		
		$this->logic = new UserDataLogic($args);
	}
	
	//获取积分
	public function getIntegral() :void
	{
		$ret = $this->logic->getIntegralAndSaveSession();
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($ret);
	}
}