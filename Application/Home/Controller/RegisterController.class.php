<?php
namespace Home\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\UserLogic;
use Validate\CheckParam;
use Common\TraitClass\GETConfigTrait;
use Common\TraitClass\SmsVerification;

class RegisterController
{
	use InitControllerTrait;
	
	use GETConfigTrait;
	use SmsVerification;
	
	/**
	 * 架构方法
	 *
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->init();
	
		$this->objController->promptPjax(IS_GET, '不允许请求');
		
		$this->logic = new UserLogic($args);
	}
	
	/**
	 * 用户登录操作
	 * @author 王强 < QQ:2272597637 > <opjklu@126.com>
	 */
	public function loginAccount()
	{
		$checkObj = new CheckParam($this->logic->getRuleByLogin(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
		$ret = $this->logic->userLogin();
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($ret);
	}
	
	/**
	 * @name 注册发送验证码
	 * @author 王强
	 * @des 注册发送验证码
	 * @updated 2017-12-20
	 */
	public function registerSendMsg()
	{
		$checkObj = new CheckParam($this->logic->getRuleByRegSendSms(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->checkUserMobileIsExits();//逻辑处理

		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		//读取短信配置
		$this->key = 'alipay_config';
		
		$data = $this->getGroupConfig();
		
		$this->config = $data;
		
		$this->mobile = $this->args['mobile'];
		
		$res = $this->sendVerification();
		
		$this->objController->promptPjax($res, $this->error);//获取失败提示并返回
		
		$this->objController->ajaxReturnData([]);//返回数据
	}
	
	
	/**
	 * @name 用户注册
	 * 
	 * @des 用户注册
	 * @updated 2017-12-16
	 */
	public function register()
	{
			
		$checkObj = new CheckParam($this->logic->getRuleByUserRegister(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->userRegister();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData($ret);//返回数据
	}
	/**
	 * @name 账户注册
	 * 
	 * @des 账户注册
	 * @updated 2017-12-16
	 */
	public function accountRegister()
	{
			
		$checkObj = new CheckParam($this->logic->getRuleByAccountRegister(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->userAccountRegister();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData($ret);//返回数据
	}
	/**
	 * @name 手机号注册发送验证码
	 * 
	 * @des 手机号注册发送验证码
	 * @updated 2017-12-20
	 */
	public function mobileRegisterSendMsg()
	{
		$checkObj = new CheckParam($this->logic->getRuleBySendSmsLogin(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->backUserPwdSendSms();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData($ret);//返回数据
	}
	

	/**
	 * @name 短信登录 --短信发送
	 * 
	 * @des 短信登录--短信发送操作
	 * @updated 2017-12-15
	 */
	public function smsLoginSend()
	{
		$this->sendSmsMessage();
	}
	
	/**
	 * 发送验证码（验证码登录 及其 忘记密码通用）
	 */
	private function sendSmsMessage()
	{
		$checkObj = new CheckParam($this->logic->getRuleByVerifySendSms(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->checkUserMobileIsExitsBySendVerfityLogin();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		//读取短信配置
		$this->key = 'alipay_config';
		
		$data = $this->getGroupConfig();
		
		$this->config = $data;
		
		$this->mobile = $this->args['mobile'];
		
		$res = $this->sendVerification();
		
		$this->objController->promptPjax($res, $this->error);//获取失败提示并返回
		
		$this->objController->ajaxReturnData([]);//返回数据
	}
	
	/**
	 * @name 短信登录
	 * 
	 * @des 短信登录
	 * @updated 2017-12-15
	 */
	public function smsLogin()
	{
		$checkObj = new CheckParam($this->logic->getRuleBySmsLogin(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->smsUserLogin();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData([]);//返回数据
	}
	
	/**
	 * @name 找回密码 -发送验证码
	 * 
	 * @des 找回密码-发送验证码
	 * @updated 2017-12-16
	 */
	public function backPwdSendSms()
	{
		$this->sendSmsMessage();
	}
	
	/**
	 * @name 找回密码
	 * 
	 * @des 找回密码
	 * @updated 2017-12-16
	 */
	public function backPwd()
	{
		$checkObj = new CheckParam($this->logic->getRuleByBackPwd(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->backUserPwd();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData([]);//返回数据
	}
}