<?php

namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\CouponListLogic;
use Validate\CheckParam;

/**
 * @name 用户优惠券控制器
 * 
 * @des 用户优惠券控制器
 * @updated 2017-12-16 15:11
 */
class CouponListController
{
	use InitControllerTrait;
	/**
	 * 架构方法
	 *
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();//#TODO 这里是需要用户必须登录时要初始化这个 否则初始化$this->init();
		
		$this->logic = new CouponListLogic($args);
		
	}
	
	/**
	 * @name 我的优惠券列表
	 * 
	 * @des 我的优惠券：未使用，已使用，已过期
	 * @updated 2017-12-16 22:14
	 */
	public function myCouponLists()
	{
		if (IS_GET) {
			$checkObj = new CheckParam($this->logic->getRuleByMyCouponLists(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->myCouponLists();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
}