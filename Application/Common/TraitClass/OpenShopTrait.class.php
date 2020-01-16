<?php
namespace Common\TraitClass;

use Common\Logic\OpenShopOrderLogic;
use Think\Log;
use Think\Hook;
use Think\SessionGet;

/**
 * 开店回调组件
 * @author Administrator
 *
 */
trait OpenShopTrait
{
	/**
	 * 支付宝流水号
	 */
	protected $tradeNo = '';
	
	private $errorMessage;
	
	
	private $listener = '';
	
	/**
	 * 开店回调
	 */
	private function opShopNofity()
	{
		$orderData = SessionGet::getInstance('order_data_by_open_shop')->get();
		
		$config = SessionGet::getInstance('pay_config_by_user')->get();
		
		$reslut = [
			'order_id' => $orderData['order_id'],
			'pay_id' => $config['pay_type_id'],
			'platform' => $config['type']
		];
		
		$openShopLogic = new OpenShopOrderLogic($reslut);
		
		$status = $openShopLogic->saveStatus();
		
		$day = date('y_m_d');
		
		Log::write('开店订单修改----'.$status, Log::INFO, '', './Log/open_shop/'.$day.'.txt');
		
		if ($status === false) {
			return false;
		}
		
		$param = [
			'order_sn_id' => $orderData['order_id'],
			'trade_no'    => $this->tradeNo,
			'wx_order_id' => $orderData['order_id'],
			'type'        => 3
		];
		
		Log::write(print_r($param, true).'---param---', Log::INFO, '', './Log/open_shop/'.$day.'.txt');
		
		Hook::listen('aplipayBalanceSerial', $param);
		if (empty($param)) {
			Log::write('---回调开店---', Log::ERR, '', './Log/open_shop/'.$day.'.txt');
			
			return false;
		}
		
		$storeData = SessionGet::getInstance('store_data_by_person')->get();
		
		if (empty($storeData)) {
			Log::write('---开店修改支付状态失败---', Log::ERR, '', './Log/open_shop/'.$day.'.txt');
		}
		
		Hook::listen($this->listener, $storeData);
		
		if (empty($orderData)) {
			
			Log::write('---回调开店订单---', Log::ERR, '', './Log/open_shop/'.$day.'.txt');
			
			return false;
		}
		
		SessionGet::getInstance('store_data_by_person')->delete();
		
		SessionGet::getInstance('order_data_by_open_shop')->delete();
		
		SessionGet::getInstance('pay_config_by_user')->delete();
		
		return true;
	}
	
}