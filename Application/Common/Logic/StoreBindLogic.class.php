<?php
declare(strict_types = 1);
namespace Common\Logic;

use Common\Model\StoreBindClassModel;

/**
 * 店铺绑定分类逻辑处理
 */
class StoreBindLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new StoreBindClassModel();
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
		return StoreBindClassModel::class;
	}
	
	/**
	 * 获取绑定的分类
	 * @return array
	 */
	public function getStoreBindClass() :array
	{
		$this->searchTemporary = [
				StoreBindClassModel::$storeId_d => (int)$this->data['store_id'],
				StoreBindClassModel::$status_d => 1
		];
		
		$data = $this->getDataList();
		
		return $data;
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			StoreBindClassModel::$id_d,
			StoreBindClassModel::$classOne_d,
			StoreBindClassModel::$classTwo_d,
			StoreBindClassModel::$classThree_d
		];	
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getPageNumber()
	 */
	protected function getPageNumber() :int
	{
		return 10;
	}
	
	/**
	 * 验证店铺编号
	 * @return array
	 */
	public function getValidateStoreId() :array
	{
		return [
			'store_id' => [
				'number' => '店铺编号必须是数字'
			]
		];
	}
}