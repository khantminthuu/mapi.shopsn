<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\CouponListLogic;
use Common\Logic\CouponLogic;
use Common\Tool\Tool;
use Common\SessionParse\SessionManager;

/**
 * 订单优惠券选择
 * @author Administrator
 */
class OrderCouponOptionsController
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
		
		$this->logic = new CouponListLogic($args);
	}
	
	/**
	 * 用户可用代金券
	 */
	public function usersCanUseCoupons()
	{
		$data = $this->logic->getUsersCanUseCoupons();
		
		$this->objController->promptPjax($data, '暂无优惠券');
		
		$couponLogic = new CouponLogic($data, $this->logic->getSplitKeyByCoupon());
		
		Tool::connect('parseString');
		
		$dataSource = $couponLogic->getResult();
		
		SessionManager::SET_DO_USE($dataSource['do_use']);
		
		$this->objController->ajaxReturnData($dataSource);
		
	}
}