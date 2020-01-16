<?php
namespace Common\Logic;

use Common\Model\AlipaySerialNumberModel;
use Common\Tool\Tool;
use Think\Log;

class AlipaySerialNumberLogic extends AbstractGetDataLogic
{
	private $orderALiData = [];
	
	/**
	 * 支付宝订单号
	 * @var unknown
	 */
	private $aliOrderId;
	
	/**
	 * @return \Common\Logic\unknown
	 */
	public function getAliOrderId()
	{
		return $this->aliOrderId;
	}
	
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '') 
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new AlipaySerialNumberModel();
	}
	
	/**
	 * 更新支付宝单号
	 */
	public function getResult() 
	{
		$data = $this->data;
		if (empty($data)) {
			$this->rollback();
			return false;
		}
		
		$status = $this->addAll();
		if (!$this->traceStation($status)) {
			return false;
		}
		
		return $status;
	}
	
	/**
	 * 添加时处理参数
	 * @return array
	 */
	protected function getParseResultByAddAll() :array
	{
		$bitchData = explode(',', $this->data['order_id']);
		
		$result = [];
		$i = 0;
		
		foreach ($bitchData as $key => $value) {
			
			$result[$i][AlipaySerialNumberModel::$orderId_d] = $value;
			
			$result[$i][AlipaySerialNumberModel::$alipayCount_d] = $this->data['trade_no'];
			
			$result[$i][AlipaySerialNumberModel::$type_d] = $this->data['type'];
			
			$i++;
		}
		
		return $result;
	}
	
	
	/**
	 * 获取凭据
	 */
	public function getOrderAli()
	{
		if (empty($this->data['id'])) {
			return [];
		}
		
		return $this->modelObj->field('order_id, alipay_count')
			->where('order_id = %d and '.AlipaySerialNumberModel::$type_d.'= %d and '.AlipaySerialNumberModel::$status_d.' = 1' , [$this->data['id'],$this->data['pay_logic_type']])
			->find();
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return AlipaySerialNumberModel::class;
	}
	
	/**
	 * 回调处理
	 */
	public function parseByPay()
	{
		$status = $this->addData();
		if (!$this->traceStation($status)) {
			Log::write($this->modelObj->getLastSql().'---alipay-sql---', Log::INFO, '', './Log/open_shop/'.date('y_m_d'));
			return false;
		}
		return true;
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultByAdd() :array
	{
		$result = [
			AlipaySerialNumberModel::$alipayCount_d => $this->data['trade_no'],
			AlipaySerialNumberModel::$orderId_d => $this->data['order_sn_id'],
			AlipaySerialNumberModel::$type_d => $this->data['type']
		];
		return $result;
	}
	
	
}