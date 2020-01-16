<?php
declare(strict_types=1);
namespace Common\Logic;

use Common\Model\OrderInvoiceModel;

class OrderInvoiceLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new OrderInvoiceModel();
	}
	/**
	 * 获取结果
	 */
	public function getResult() {
	}
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return OrderInvoiceModel::class;
	}
	
	/**
	 * 发票更新
	 */
	public function updateInvoice() :bool
	{
		$data = $this->data;
		
		if (empty($data)) {
			return true;
		}
		
		$sql = $this->buildUpdateSql();
		
		try {
			$status = $this->modelObj->execute($sql);
		} catch (\Exception $e) {
			$this->errorMessage = $e->getMessage();
			$this->modelObj->rollback();
			return false;
		}
		
		return true;
	}
	
	/**
	 * 要更新的字段
	 * @return array
	 */
	protected function getColumToBeUpdated() :array
	{
		return [
			OrderInvoiceModel::$orderId_d,
			OrderInvoiceModel::$updateTime_d
		];
	}
	
	/**
	 * 要更新的数据【已经解析好的】
	 * @return array
	 */
	protected function getDataToBeUpdated() :array
	{
		//批量更新
		$pasrseData = array();
		$time = time();
		foreach ($this->data as $key => $value)
		{
			$pasrseData[$value['id']][] = $value['order_id'];
			
			$pasrseData[$value['id']][] = $time;
		}
		
		return $pasrseData;
	}
	
	/**
	 * 发票->立即购买更新
	 */
	public function updateInvoiceByPlaceTheOrder() :bool
	{
		$data = $this->data;
		
		if (empty($data)) {
			return true;
		}
		
		$status = $this->saveData();
		
		if (!$this->traceStation($status)) {
			$this->errorMessage = '发票更新失败';
			return false;
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
		return [
			OrderInvoiceModel::$orderId_d => $this->data['order_id'],
			OrderInvoiceModel::$id_d => $this->data['id']
		];
	}
}