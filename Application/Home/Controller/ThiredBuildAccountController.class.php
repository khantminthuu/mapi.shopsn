<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\UserLogic;
use Validate\CheckParam;
use Common\TraitClass\GETConfigTrait;
use Common\TraitClass\SmsVerification;
use Common\Logic\UserAuthLogic;
use Think\SessionGet;

/**
 * 
 * @author Administrator
 *
 */
class ThiredBuildAccountController
{
	use InitControllerTrait;
	
	use GETConfigTrait;
	
	use SmsVerification;
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->init();
		
		$this->logic = new UserLogic($this->args);
		
	}
	
	/**
	 * 验证手机号码(QQ)
	 */
	public function checkMobile()
	{
		$checkObj = new CheckParam($this->logic->getMessageByBindAccount(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		//读取短信配置
		
		$this->key = 'alipay_config';
		
		$data = $this->getGroupConfig();
		
		$this->config = $data;
		
		$this->mobile = $this->args['mobile'];
		
		$res = $this->sendVerification();
		
		$this->objController->promptPjax($res, $this->error);//获取失败提示并返回
		
		$this->objController->ajaxReturnData($this->args['token']);//返回数据
	}
	
	/**
	 * 绑定账号
	 */
	public function bindAccount()
	{
		$checkObj = new CheckParam($this->logic->checkPhoneNumber(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$status = $this->logic->addUserByQQ();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$authLogic = new UserAuthLogic($this->logic->getAcccountInfo());
		
		$this->objController->promptPjax($authLogic->parseUserAuth(), $authLogic->getErrorMessage());
		
		SessionGet::getInstance('rand_nubmer')->delete();
		
		$this->objController->ajaxReturnData(session_id());
	}
}