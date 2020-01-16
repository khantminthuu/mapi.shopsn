<?php

namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\IntegralLogic;
use Validate\CheckParam;

/**
 * @name 积分控制器
 * 
 * @des 积分控制器
 * @updated 2018-01-05 18:24
 */
class IntegralController
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
		
		$this->objController->promptPjax(IS_POST, '不允许请求');
		
		$this->logic = new IntegralLogic($args);
		
	}
	
	/**
     * @name 会员积分规则
     * 
     * @des 会员积分规则
     * @updated 2018-01-05 18:24
     */
    public function rule()
    {
        $ret = $this->logic->rule();//逻辑处理

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

        $this->objController->ajaxReturnData($ret);//返回数据
    }

    /**
     * @name 会员积分统计规则
     * 
     * @des 会员积分统计规则
     * @updated 2018-01-06 11:00
     */
//    public function total()
//    {
//        if (IS_GET) {
//            $ret = $this->logic->total();//逻辑处理
//
//            $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
//
//            $this->objController->ajaxReturnData($ret);//返回数据
//        }
//    }

    /**
     * @name 会员积分日志明细
     * 
     * @des 会员积分日志明细
     * @updated 2018-01-06 12:42
     */
    public function integralLog()
    {
      	$ret = $this->logic->integralLog();//逻辑处理

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

        $this->objController->ajaxReturnData($ret);//返回数据
    }
}
