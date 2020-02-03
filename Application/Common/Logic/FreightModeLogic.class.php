<?php
namespace Common\Logic;

use Common\Model\FreightModeModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;

/**
 * 运送方式配置
 * @author 王强
 */
class FreightModeLogic extends AbstractGetDataLogic
{
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new FreightModeModel();
	}
	
	/**
	 * 获取运费(立即购买)
	 * @return float
	 */
	public function getResult()
	{
		
	}
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return FreightModeModel::class;
	}
	
	/**
	 * 获取运费配置
	 * @return float
	 */
	public function getFreightMoney()
	{
		$cache = Cache::getInstance('', ['expire' => 105]);
		
		$idString = implode(',', array_keys($this->data));
		
		$cacheKey = $idString.'wdf';
		
		$data = $cache->get($cacheKey);
		
		if (!empty($data)) {
			return $data;
		}
		
		
		$idString = implode(',', array_keys($this->data));
		
		$data = $this->modelObj
			->field($this->getTableColum())
			->where(FreightModeModel::$freightId_d.' in(%s)', $idString)
			->select();
		if (empty($data)) {
			$this->errorMessage = '运费模板设置错误';
			return [];
		}
		
		$data = (new ArrayChildren($data))->convertIdByData(FreightModeModel::$id_d);
		$tmp = 0;
		foreach ($data as $key => &$value) {
			
			if (empty($this->data[$value[FreightModeModel::$freightId_d]])) {
				return [];
			}
			$tmp = $value[FreightModeModel::$id_d];
			$value = array_merge($value, $this->data[$value[FreightModeModel::$freightId_d]]);
			
			$value[FreightModeModel::$id_d] = $tmp;
		}
		$cache->set($cacheKey, $data);
		return $data;
	}
}
