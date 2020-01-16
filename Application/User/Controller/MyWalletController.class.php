<?php
namespace User\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\PcenterLogic;

/**
 * 我的钱包
 * @author Administrator
 */
class MyWalletController
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
		// $this->init();
		// session('user_id',3);
		$this->logic= new PcenterLogic($args);
	}
	
	/**
	 * 我的钱包
	 */
	public function myWalletMoney()
	{
		
		$result=$this->logic->myWallet();
		
		$this->objController->ajaxReturnData($result);
		
	}
	
	/**
	 * 可提现金额
	 */
	public function distill(){
		
		$result=$this->logic->logdistill();
		
		$this->objController->ajaxReturnData($result);
	}
	
	/**
	 * 确定提现
	 */
	public function makeDistill(){
		
		$result=$this->logic->logMakeDistill();
		
		$this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
	}
}