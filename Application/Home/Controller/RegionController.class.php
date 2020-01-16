<?php

namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\RegionLogic;
use Validate\CheckParam;

class RegionController
{
	
	use InitControllerTrait;
	
	
	/**
	 * 架构方法
	 *
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->args = $args;
		
		$this->logic = new RegionLogic($args);
		
	}
	
	/**
	 * @name 获取城市列表控制器
	 * 
	 * @des 获取城市列表控制器
	 * @updated 2017-12-23
	 */
	public function regionLists()
	{
		if (IS_GET) {
			$checkObj = new CheckParam($this->logic->getRuleByRegionLists(), $this->args);
			
			$status = $checkObj->checkParam();
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());
			
			$ret = $this->logic->regionLists();
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
			
			$this->objController->ajaxReturnData($ret);
		}
	}
}