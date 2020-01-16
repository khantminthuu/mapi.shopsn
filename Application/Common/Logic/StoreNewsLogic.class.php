<?php
namespace Common\Logic;
use Common\Model\StoreModel;
use Think\Cache;

/**
 * 商铺逻辑处理层
 */
class StoreNewsLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new StoreModel();
	}
	
	/**
	 * 获取结果
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
		return StoreModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array
	{
		return [
						
		];
	}
	
	/**
	 * 获取店铺信息
	 */
	public function getStoreTitileAndPic()
	{
		if (empty($this->data[$this->splitKey])) {
			$this->errorMessage = '店铺详情错误';
			return [];
		}
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$key = $this->data[$this->splitKey].'_store_logo';
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->modelObj->field(StoreModel::$storeLogo_d.','.StoreModel::$shopName_d)
			->where(StoreModel::$id_d.'=:id')
			->bind([':id' => $this->data[$this->splitKey]])
			->find();
		
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
}