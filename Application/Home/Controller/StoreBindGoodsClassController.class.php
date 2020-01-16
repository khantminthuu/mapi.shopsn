<?php
declare(strict_types = 1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\StoreBindLogic;
use Validate\CheckParam;
use Common\Logic\GoodsClassLogic;

/**
 * 获取店铺所绑定的分类
 * @author Administrator
 *
 */
class StoreBindGoodsClassController
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
		$this->logic = new StoreBindLogic($args);
	}
	
	/**
	 * 获取绑定的分类
	 */
	public function getBindGoodsClass() :void
	{
		$checkParam = new CheckParam($this->logic->getValidateStoreId(), $this->args);
		
		$this->objController->promptPjax($checkParam->checkParam(), $checkParam->getErrorMessage());
		
		$data = $this->logic->getStoreBindClass();
		
		$this->objController->promptPjax($data['records'], '没有分类');
		
		$goodsClassLogic = new GoodsClassLogic($data['records']);
		
		$data['records'] = $goodsClassLogic->getGoodsClassByBindClass();
		
		$this->objController->ajaxReturnData($data);
	}
}