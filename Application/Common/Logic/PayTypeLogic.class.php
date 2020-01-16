<?php
namespace Common\Logic;

use Think\Cache;
use Common\Model\PayTypeModel;

/**
 * @author Administrator
 */
class PayTypeLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new PayTypeModel();
	}
	
	/**
	 * 获取结果(支付列表)
	 */
	public function getResult()
	{
		$cache = Cache::getInstance('', ['expire' => 600]);
		
		$data = $cache->get('pay_type_wap');
		
		if (empty($data)) {
			$data = $this->getNoPageList();
		} else {
			return $data;
		}
		
		if (empty($data)) {
			return [];
		}
		$cache->set('pay_type_wap', $data);
		
		return $data;
	}
	
	/**
	 * 获取结果(支付列表)
	 */
	public function getPayType()
	{
		$cache = Cache::getInstance('', ['expire' => 600]);
		
		$data = $cache->get('pay_type_special');
		
		if (empty($data)) {
			$data = $this->modelObj->field($this->getTableColum())->where(PayTypeModel::$isSpecial_d.' = 0 and '.PayTypeModel::$status_d.' = 1')
				->select();
		} else {
			return $data;
		}
		
		if (empty($data)) {
			return [];
		}
		$cache->set('pay_type_special', $data);
		
		return $data;
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return PayTypeModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			PayTypeModel::$id_d,
			PayTypeModel::$typeName_d,
			PayTypeModel::$isDefault_d,
			PayTypeModel::$logo_d
		];
	}
}