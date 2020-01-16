<?php

namespace User\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\FeedbackLogic;
use Validate\CheckParam;

/**
 * @name 意见反馈控制器
 * 
 * @des 意见反馈控制器
 * @updated 2017-12-22 19:43
 */
class FeedbackController
{
	use InitControllerTrait;
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();//#TODO 这里是需要用户必须登录时要初始化这个 否则初始化$this->init();
		
		$this->logic = new FeedbackLogic($args);
	}
	/**
	 * @name 意见反馈
	 * 
	 * @des 意见反馈
	 * @updated 2017-12-22 19:34
	 */
	public function feedback()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByFeedback(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->feedback();//逻辑处理 
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
}