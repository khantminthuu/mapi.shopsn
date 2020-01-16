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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsCartLogic;

/**
 * 再次购买
 * @author Administrator
 */
class OrderBuyAgainController
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
		
		$this->logic = new OrderGoodsLogic($args);
	}
	
	/**
	 * 订单再次购买
	 */
	public function buyAgain()
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getMessageNotice());
		
		$goods = $this->logic->getGoodsByOrderId();
		$this->objController->promptPjax($goods, '数据错误');
		
		//验证库存
		$goodsLogic = new GoodsLogic($goods, $this->logic->getSplitKeyByGoods());
		
		$this->objController->promptPjax($goodsLogic->orderBuyAgainCheckStock(), $goodsLogic->getErrorMessage());
		
		$goodsCartLogic = new GoodsCartLogic($goods);
		
		$status = $goodsCartLogic->addCartByOrder();
		
		$this->objController->promptPjax($status, $goodsCartLogic->getErrorMessage());
		
		$this->objController->ajaxReturnData($status);
		
	}
}