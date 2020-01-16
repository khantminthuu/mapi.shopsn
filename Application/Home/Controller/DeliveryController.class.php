<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\DeliveryLogic;
use Common\TraitClass\IsLoginTrait;
use Validate\CheckParam;


/**
 * 配送员控制器
 * @author Administrator
 */
class DeliveryController
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
		$this->logic = new DeliveryLogic($args);
	}
	
	/**
	 * 获取配送费
	 */
	public function deliveryMoney()
	{
		$checkObj = new CheckParam($this->logic->getValidateByDelivery(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$money = $this->logic->getDeliveryMoney();
		
		$this->objController->ajaxReturnData($money['data'],$money['status'],$money['message']);
	}
}