<?php
declare(strict_types = 1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderIntegralLogic;
use Validate\CheckParam;
use Common\Logic\OrderIntegralGoodsLogic;
use Common\SessionParse\SessionManager;

/**
 * 积分生成订单
 * @author 王强
 */
class GeneratingOrderIntegralController
{
	use InitControllerTrait;
	
	public function __construct(array $args = [])
	{
		
		$this->args = $args;
		
		$this->_initUser();
		
		$this->logic = new OrderIntegralLogic($args);
	}
	
	/**
	 * 积分兑换处理 - 下订单
	 */
	public function confirmExchange()
	{
		
		$checkObj = new CheckParam($this->logic->getValidateByBuildOrder(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
		$status = $this->logic->commitConfirm();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$insertId = $this->logic->getOrderIntegralInsertId();
		
		$orderIntegralGoods = new OrderIntegralGoodsLogic(['integral_order_id' => $insertId]);
		
		$status = $orderIntegralGoods->addOrderIntegral();
		
		$this->objController->promptPjax($status, $orderIntegralGoods->getErrorMessage());
		
		$payData = $this->logic->getPayData();
		
		SessionManager::SET_ORDER_DATA($payData);
		
		SessionManager::REMOVE_GOODS_DATA_SOURCE();
		
		$this->objController->ajaxReturnData([
//			'integral' => $payData[$insertId]['integral'],     报错  注释   ---meng
//			'money' => $payData[$insertId]['total_money']      报错  注释   ---meng
            'integral' => $payData[0]['integral'],
			'money' => $payData[0]['total_money']
		]);
		
	}
	
}