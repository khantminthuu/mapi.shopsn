<?php
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;

/**
 * 物流查询
 * @author Administrator
 *
 */
class LogisticsController
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
		
		$this->logic = new OrderLogic($args);
	}
}