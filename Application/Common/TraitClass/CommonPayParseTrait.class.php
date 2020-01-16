<?php
namespace Common\TraitClass;

use Validate\CheckParam;
use Common\Logic\GoodsLogic;
use Think\SessionGet;

trait CommonPayParseTrait
{
	private $payErrorMessage = '';
	/**
	 * 发起支付处理
	 */
	private function initiate()
	{
		
		$checkObj = new CheckParam($this->logic->getValidateByPay(), $this->args);
		
		if ($checkObj->checkParam() === false) {
			$this->payErrorMessage = $checkObj->getErrorMessage();
			
			return [];
		}
		
		$goodsStock = SessionGet::getInstance('goods_id_by_user')->get();
		
		//判断库存是否足够
		$goodsLogic = new GoodsLogic($goodsStock);
		
		if ($goodsLogic->checkStock() === false) {
			
			$this->payErrorMessage = $goodsLogic->getErrorMessage();
			
			return [];
		}
		
		$payConfig = $this->logic->getResult();
		if (empty($payConfig)) {
			
			$this->payErrorMessage = '无法获取支付配置';
			
			return [];
		}
		
		$orderData = SessionGet::getInstance('order_data')->get();
		
		$result = $this->dispatcherPay($payConfig, $orderData);
		
		if (empty($result)) {
			
			$this->payErrorMessage = '无法三方支付配置';
			
			return [];
		}
		return $result;
	}
}