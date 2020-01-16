<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\CouponLogic;

/**
 * 验证优惠券是否可用
 * @author Administrator
 */
class CheckIsUseCouponController 
{
	use InitControllerTrait;
	
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();
		
		$this->logic = new CouponLogic($args);
		
	}
	
	/**
	 * 判断优惠券是否可用
	 */
	public function check()
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
		$status = $this->logic->checkCouponIsUse();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($status);
		
	}
}