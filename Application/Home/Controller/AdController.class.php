<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\AdLogic;
use Validate\CheckParam;


/**
 * 广告控制器
 * @author Administrator
 */
class AdController
{
	use InitControllerTrait;
	
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->args = $args;
		
		$this->logic = new AdLogic($args);
	}
	
	/**
	 * 获取广告
	 */
	public function getAd()
	{
		$checkObj = new CheckParam($this->logic->getValidateByClassPage(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$ad = $this->logic->getResult();
		
		$this->objController->ajaxReturnData($ad);
	}
	// 测试Git,测试完成;
}