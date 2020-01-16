<?php
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\UserLogic;
use Validate\CheckParam;
use Common\Tool\Extend\CURL;
use Common\Logic\IntegralLogic;
use Common\Logic\UserLevelLogic;
use Common\Logic\UserDataLogic;

/**
 * 个人中心用户相关
 * @author 王强
 *
 */
class UserController
{
	use InitControllerTrait;
	use IsLoginTrait;
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		
		$this->args = $args;
		$this->_initUser();
		$this->logic = new UserLogic($args);
	}
	
	/**
	 * @name 获取个人信息
	 * 
	 * @des 获取个人信息
	 * @updated 2017-12-16 11:01
	 */
	public function getUserInfo()
	{
		$ret = $this->logic->getUserInfo();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$integralLogic = new UserDataLogic();
		
		$integral = $integralLogic->getIntegralByUserId();
		
		$userLevelLogic = new UserLevelLogic($integral, $integralLogic->getSplitKeyByIntegral());
		
		$userLevel = $userLevelLogic->getLevelByUser();
		
		$ret = array_merge($ret, $userLevel);
		
		$this->objController->ajaxReturnData($ret);//返回数据
	}
	
	/**
	 * @name 修改个人资料
	 * @author 王波
	 * @des 修改个人资料
	 * @updated 2017-12-16 19:34
	 */
	public function editUserInfo(){
		
		$checkObj = new CheckParam($this->logic->getRuleByEditUserInfo(), $this->args);
		//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		$status = $checkObj->checkParam();
		//检测参数，类似jQuery Validate自动验证方法
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		//获取失败提示并返回
		$ret = $this->logic->editUserInfo();
		//逻辑处理
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		//获取失败提示并返回
		$this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
		//返回数据
	}
	
	/**
	 * @name 修改手机号绑定-发送短信
	 * 
	 * @des 修改手机号绑定-发送短信
	 * @updated 2017-12-16 18:41
	 */
	public function editMobileSendSms()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleBySendSmsLogin(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->editMobileSendSms();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
	/**
	 * @name 修改手机号绑定
	 * 
	 * @des 修改手机号绑定
	 * @updated 2017-12-16 19:34
	 */
	public function editMobile()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByEditMobile(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->editUserBindMobile();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
	
	/**
	 * @name 修改密码
	 * 
	 * @des 修改密码
	 * @updated 2017-12-16 18:41
	 */
	public function modifyPassword()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByModifyPassword(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->modifyPassword();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
	
	/**
	 * 上传头像
	 */
	public function uploadPicture()
	{
		$this->objController->promptPjax(!empty($_FILES['adv_content']), '请上传图片');
		
		$checkObj = new CheckParam($this->logic->getMessageByPic(), $_FILES['adv_content']);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$this->objController->promptPjax($this->logic->checkImageWidthAndHeight(), $this->logic->getErrorMessage());
		
		$curlFile = new CURL($_FILES['adv_content'], C('create_header_image'));
		
		$file = $curlFile->uploadFile();
		
		echo $file;die;
	}
	
	/**
	 * 删除广告图片
	 */
	public function delPic()
	{
		$curlFile = new CURL($this->args, C('unlink_image_no_thumb'));
		
		echo $curlFile->deleteFile();die;
	}
}