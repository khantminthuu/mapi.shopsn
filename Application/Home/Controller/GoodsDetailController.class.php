<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\GoodsDetailLogic;

/**
 * 商品图片
 * @author 王强
 */
class GoodsDetailController
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
		
		$this->init();
		
		$this->logic = new GoodsDetailLogic($this->args);
		
	}
	//商品图片
	public function getGoodsDetail()
	{
		$checkObj = new CheckParam($this->logic->getMessageByDetail(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$data = $this->logic->getGoodDetail();
		
		$this->objController->ajaxReturnData($data);
	}
}