<?php
namespace User\Controller;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Think\SessionGet;

class LogOutController
{
	use InitControllerTrait;
	use IsLoginTrait;
	
	public function __construct(array $args = [])
	{
		$this->args = $args;
		$this->_initUser();
	}
	
	/**
	 * 退出登录
	 */
	public function logOut()
	{
		SessionGet::getInstance('user_id')->destroy();
		
		$this->objController->ajaxReturnData('');
	}
}