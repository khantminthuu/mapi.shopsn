<?php
namespace Common\Logic;

use Common\Model\OrderWxpayModel;
use Common\Tool\Tool;
use Think\Log;

class OrderWxpayLogic extends AbstractGetDataLogic
{
	private $orderWxData = [];
	
	/**
	 * 余额相关数据
	 * @var array
	 */
	private $balanceData = [];
	/**
	 * 余额充值
	 * @var integer
	 */
	const BALANCE_RECHARGE = 1;
	
	/**
	 * 微信订单号
	 * @var unknown
	 */
	private $wxOrderId;
	
	/**
	 * 支付类型 0 商品支付，1套餐支付，2积分支付,3开店支付，4余额充值 
	 * @var integer
	 */
	private $payType = 0;
	
	
	/**
	 * @return \Common\Logic\unknown
	 */
	public function getWxOrderId()
	{
		return $this->wxOrderId;		
	}
	
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '', $isGoodsPay = 0) 
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new OrderWxpayModel();
		
		$this->payType = $isGoodsPay;
		
	}
	
	/**
	 * 更新微信订单号
	 */
	public function getResult() 
	{
		$isHave = $this->getOrderWx();
		
		$status = false;
		if (empty($isHave)) {
			$status = $this->addAll();
		} else { // 失败更新订单号
			$this->orderWxData = $isHave;
			
			$sql = $this->buildUpdateSql();
			try {
				$status = $this->modelObj->execute($sql);
			} catch (\Exception $e) {
				$this->errorMessage = $e->getMessage();
				return false;
			}
		}
		return $status;
	}
	
	/**
	 * 余额充值处理订单号
	 */
	public function getResultByPay()
	{
		$isHave = $this->getOrderWxpayCredentials();
		
		if (empty($isHave)) {
			$status = $this->addData();
		} else { // 失败更新订单号
			$this->orderWxData = $isHave;
			
			$status = $this->saveData();
		}
		
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultBySave() :array
	{
		// 生成不同的支付码
		$wxPay = Tool::connect('Token')->toGUID();
		
		$this->wxOrderId = $wxPay;
		
		$result = [
			OrderWxpayModel::$id_d => $this-> balanceData[OrderWxpayModel::$id_d],
			OrderWxpayModel::$wxPay_id_d => $wxPay,
		];
		return $result;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		// 生成不同的支付码
		$wxPay = Tool::connect('Token')->toGUID();
		
		$this->wxOrderId = $wxPay;
		$result = [
			OrderWxpayModel::$orderId_d => $this->data['order_id'],
			OrderWxpayModel::$wxPay_id_d => $wxPay,
			OrderWxpayModel::$status_d => 0,
			OrderWxpayModel::$type_d => $this->payType
		];
		return $result;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated()
	 */
	protected function getColumToBeUpdated() :array
	{
		return [
			OrderWxpayModel::$wxPay_id_d
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getDataToBeUpdated()
	 */
	protected function getDataToBeUpdated() :array
	{
		$data = $this->orderWxData;
		
		// 生成不同的支付码
		$wxPay = Tool::connect('Token')->toGUID();
		
		$this->wxOrderId = $wxPay;
		
		$result = [];
		
		foreach ($data as $key => $value) {
			$result[$key][] = $wxPay;
		}
		
		return $result;
	}
	
	/**
	 * 批量添加时处理
	 * @return []
	 */
	protected function getParseResultByAddAll() :array
	{
		if (empty($this->data)) {
			return false;
		}
		
		$data = $this->data;
		
		$result = [];
		
		$i = 0;
		
		// 生成不同的支付码
		$wxPay = Tool::connect('Token')->toGUID();
		
		$this->wxOrderId = $wxPay;
		
		foreach ($data as $key => &$value) {
			$result[$i][OrderWxpayModel::$orderId_d] = $key;
			$result[$i][OrderWxpayModel::$wxPay_id_d] = $wxPay;
			$result[$i][OrderWxpayModel::$type_d] = $this->payType;
			$result[$i][OrderWxpayModel::$status_d] = 0;
			$i++;
		}
		
		return $result;
	}
	/**
	 * 获取凭据
	 */
	public function getOrderWx()
	{
		if (empty($this->data)) {
			return [];
		}
		
		$wxOrderId = implode(',', array_keys($this->data));
		
		$data = $this->modelObj
			->where('order_id  in(%s) and '.OrderWxpayModel::$type_d.'= %d' , [$wxOrderId, $this->payType])
			->getField('id, order_id, wx_pay_id');
		return $data;
	}
	
	/**
	 * 获取微信订单凭据 
	 */
	public function getOrderWxpayCredentials()
	{
		if (empty($this->data)) {
			return [];
		}
		
		$wxOrderId = $this->data['order_id'];
		
		$data = $this->modelObj
		->where('order_id = %d and '.OrderWxpayModel::$type_d.'= %d' , [$wxOrderId, $this->payType])
			->getField('id, order_id, wx_pay_id');
		return $data;
	}
	
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return OrderWxpayModel::class;
	}
	
	/**
	 * 微信回调更新微信订单号
	 */
	public function nofityUpdate ()
	{
		$param = $this->data;
		
		if (empty($param)) {
			$this->modelObj->rollback();
			return false;
		}
		
		$wxOrderId = $param['wx_order_id'];
		
		$type    = $param['type'];
		
		try {
			$status = $this->modelObj
				->where(OrderWxpayModel::$orderId_d.' in(%s) and '.OrderWxpayModel::$type_d.'=:type', $wxOrderId)
				->bind([
					':type'=> $type
				])->save([
					OrderWxpayModel::$status_d => 1
				]);
				
			if (!$this->traceStation($status)) {
				Log::write('sql -- '.$this->modelObj->getLastSql(), Log::INFO, '', './Log/order/wx_sql_'.date('y_m_d').'.txt');
				return false;
			}
		} catch (\Exception $e) {
			Log::write('sql -- '.$e->getMessage(), Log::ERR, '', './Log/order/wx_sql_'.date('y_m_d').'.txt');
			return false;
		}
		return $status;
	}
	
	/**
	 * 微信回调更新微信订单号(余额充值及其 开店)
	 */
	public function nofityUpdateBySpecial ()
	{
		$param = $this->data;
		
		if (empty($param)) {
			$this->modelObj->rollback();
			return false;
		}
		
		$wxOrderId = $param['wx_order_id'];
		
		$type    = $param['type'];
		
		$status = $this->modelObj
			->where(OrderWxpayModel::$orderId_d.' =:id and '.OrderWxpayModel::$type_d.'=:type', $wxOrderId)
			->bind([
				':id' => $wxOrderId,
				':type'=> $type
			])->save([
				OrderWxpayModel::$status_d => 1
			]);
		if (!$this->traceStation($status)) {
			Log::write($this->modelObj->getLastSql(), Log::INFO, '', './Log/open_shop/'.date('y_m_d'));
			return false;
		}
		return true;
	}
}