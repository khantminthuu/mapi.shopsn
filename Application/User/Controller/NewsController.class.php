<?php
namespace User\Controller;
use Common\Logic\NewsLogic;
use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;

/**
 * @name 用户消息控制器
 * 
 * @des 用户消息控制器
 * @updated 2017-12-22 20:25
 */
 class NewsController
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
		
		 $this->objController->promptPjax(IS_POST, '不允许请求');
		 
		 $this->logic = new NewsLogic($args);
	 }
	 /**
	  * @name 我的消息列表
	  * 
	  * @des 我的消息列表
	  * @updated 2017-12-22
	  */
     public function lists()
     {
		 $checkObj = new CheckParam($this->logic->getRuleByLists(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
	
		 $status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
	
		 $this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
	
		 $ret = $this->logic->lists();//逻辑处理
	
		 $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
	
		 $this->objController->ajaxReturnData($ret);//返回数据
     }
	 /**
	  * @name 查看我的消息详情
	  * 
	  * @des 查看我的消息详情
	  * @updated 2017-12-22
	  */
     public function info(){
		 $checkObj = new CheckParam($this->logic->getRuleByInfo(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
	
		 $status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
	
		 $this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		 
		 $ret = $this->logic->info();//逻辑处理
	
		 $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
	
		 $this->objController->ajaxReturnData($ret);//返回数据
     }
 }