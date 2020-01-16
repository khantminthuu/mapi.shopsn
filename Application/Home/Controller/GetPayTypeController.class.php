<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\PayTypeLogic;
/**
 * 获取支付方式
 * @author Administrator
 */
class GetPayTypeController
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
		
		$this->logic = new PayTypeLogic($args);
	}
	
	/**
	 * 获取支付类型
	 */
	public function getPayResult()
	{
		$this->objController->ajaxReturnData($this->logic->getResult());
	}
	
	/**
	 * 获取支付类型
	 */
	public function getPayRechargeResult()
	{
		$this->objController->ajaxReturnData($this->logic->getPayType());
	}
}