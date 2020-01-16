<?php
declare(strict_types = 1);
namespace Common\Controller;

use Common\Logic\PayLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\DispatcherPayTrait;
use Think\SessionGet;

/**
 * 支付抽象控制器
 * @author Administrator
 */
abstract class AbstractPayOrderController
{
	use InitControllerTrait;
	
	use DispatcherPayTrait;
	
	/**
	 * 0 商品支付(第三方支付) 1 余额充值
	 * @var integer
	 */
	private $type = 0;
	
	private $orderInfo = [];
	
	
	/**
	 * 支付类型
	 * @var integer
	 */
	protected $specialStatus;
	
	/**
	 * 架构方法
	 * @param array
	 * $args   传入的参数数组
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->_initUser();
		
		//wap支付
		$args['platform'] = 1;
		
		$args['special_status'] = $this->specialStatus;
		
		$orderData = SessionGet::getInstance('order_data')->get();
		
		$this->objController->promptPjax( $orderData, '订单请求失败');
		
		$this->logic = new PayLogic($args);
	}
	
	public function initiatePayment() :void
	{
		$this->objController->promptPjax(IS_POST , '不允许请求');
		
		$data = $this->initiate();
		
		$this->objController->promptPjax($data, $this->errorMessage);
		
		$ret = $this->integralCallBack();
		
		$this->objController->promptPjax($ret, $this->errorMessage);
		
		$this->objController->ajaxReturn($data);
	}
	
	/**
	 * 积分支付处理
	 * @return array
	 */
	protected function integralCallBack() :bool
	{
		return true;
	}
	
}