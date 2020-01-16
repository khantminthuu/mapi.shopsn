<?php

namespace Common\Logic;
use Common\Model\RegionModel;

/**
 * @name 城市选择逻辑层
 * 
 * @des 城市选择逻辑层
 * @updated 2017-12-22 19:42
 */
class RegionLogic extends AbstractGetDataLogic
{
	protected $userModelObj = '';
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		$this->modelObj = new RegionModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return RegionModel::class;
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
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array
	{
		return [
			RegionModel::$name_d,
		];
	}
	
	/**
	 * @name 城市选择验证规则
	 * 
	 * @des 城市选择验证规则
	 * @updated 2017-12-22
	 */
	public function getRuleByRegionLists()
	{
		$message = [
			'parent_id'          => [
				'required'          => '参数不正确',
				'specialCharFilter' => '参数不正确',
			],
		];
		
		return $message;
	}
	
	/**
	 * @name 城市选择列表逻辑
	 * 
	 * @des 城市选择列表逻辑
	 * @updated 2017-12-22
	 */
	public function regionLists()
	{
		$where = [];
		if(!empty($this->data['id'])){
			$where = [RegionModel::$id_d => $this->data['id']];
		}
		if(!empty($this->data['parent_id'])){
			$where1 = [RegionModel::$parentid_d => (int)$this->data['parent_id']];
		}else{
			$where1 = [RegionModel::$parentid_d => 0];
		}
		//#TODO 这里是查询条件
		$this->searchTemporary = array_merge($where, $where1);
		$this->searchOrder = 'displayorder ASC';
		//#TODO 调用通用的获取列表的接口并返回数据  data=>['countTotal'=>2, 'records'=>[.....]]
		$data = parent::getNoPageList();
		if(empty($data)){
			$this->errorMessage = '查询数据为空';
			return [];
		}
		return $data;
	}
}
