<?php
namespace Home\Controller;

use Common\Logic\StoreAdvLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;

/**
 * 店铺广告
 * @author Administrator
 *
 */
class StoreAdvController
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
		
		$this->logic = new StoreAdvLogic($args);
	}
	
	/**
	 * 获取店铺banner
	 */
	public function getBanner()
	{
		$checkObj = new CheckParam($this->logic->getCheckMessageByStore(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$data = $this->logic->getBanner();
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 获取店铺首页不规则图片
	 */
	public function getIrregular()
	{
		$checkObj = new CheckParam($this->logic->getCheckMessageByStore(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$data = $this->logic->getBannerButton();
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 获取店铺下面的广告
	 */
	public function getButton()
	{
		$checkObj = new CheckParam($this->logic->getCheckMessageByStore(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$data = $this->logic->getResult();
		
		$this->objController->ajaxReturnData($data);
	}
}