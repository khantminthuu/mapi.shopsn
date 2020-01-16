<?php

namespace User\Controller;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\UserAddressLogic;
use Validate\CheckParam;
/**
 * @name 用户收货地址控制器
 * 
 * @des 用户收货地址控制器
 * @updated 2017-12-16 15:11
 */
class UserAddressController
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
		
		$this->logic = new UserAddressLogic($args);
	}
	/**
	 * @name 新增和编辑收货地址
	 * 
	 * @des 新增和编辑收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function addAddress()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByAddEditAddress(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->addAddress();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
	/**
	 * @name 新增和编辑收货地址
	 * 
	 * @des 新增和编辑收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function editAddress()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByAddEditAddress(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->editAddress();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);//返回数据
		}
	}
	/**
	 * @name 查看收货地址
	 * 
	 * @des 查看收货地址
	 * @updated 2017-12-16 15:11
	 */
	public function addressInfo(){
		
		$checkObj = new CheckParam($this->logic->getRuleByAddressLook(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->addressLook();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
		
		$this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);//返回数据
		
	}
	/**
	 * @name 用户收货地址列表
	 * 
	 * @des 用户收货地址列表
	 * @updated 2017-12-16 17:43
	 */
	public function addressLists()
	{
		if (IS_GET) {
			$checkObj = new CheckParam($this->logic->getRuleByAddressLists(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->addressLists();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}
	/**
	 * @name 用户收货地址删除
	 * 
	 * @des 用户收货地址删除
	 * @updated 2017-12-16 18:26
	 */
	public function addressDelete()
	{
		if (IS_POST) {
			$checkObj = new CheckParam($this->logic->getRuleByAddressLook(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
			
			$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
			
			$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
			
			$ret = $this->logic->addressDel();//逻辑处理
			
			$this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回
			
			$this->objController->ajaxReturnData($ret);//返回数据
		}
	}

	public function getDefaultAddress(){ 
        $ret = $this->logic->getUserDefaultAddress();//逻辑处理

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

        $this->objController->ajaxReturnData($ret);//返回数据
    }
    //默认收货地址
    public function getDefault(){ 
        $ret = $this->logic->getUserDefault();//逻辑处理

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());//获取失败提示并返回

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);//返回数据
    }
}