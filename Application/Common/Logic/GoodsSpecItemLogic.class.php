<?php
declare(strict_types = 1);
namespace Common\Logic;

use Common\Model\GoodsSpecItemModel;

/**
 * 规格项逻辑
 * @author 王强
 * @updated 2018-01-05 18:27
 */
class GoodsSpecItemLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new GoodsSpecItemModel();
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
		return GoodsSpecItemModel::class;
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
            GoodsSpecItemModel::$item_d,
		];
	}

	/**
	 * 获取规格项名称
	 * @param array $data 商品数组
	 * @param string $splitKey 分割建
	 * @return array
	 */
	public function getSpecItemName() :array
	{
		$data = $this->data;
		
		
		if (empty($data)) {
			return array();
		}
		$keyArray = array_column($data, $this->splitKey);
		
		$length = count($keyArray);
		
		if ($length === 0) {
			return [];
		}
		
		$temp = [];
		
		
		for ($i= 0 ; $i < $length; $i++) {
			
			$temp = array_merge(explode('_', $keyArray[$i]), $temp);
		}
		
		$idString = implode(',', array_unique($temp));
		
		$whereField = GoodsSpecItemModel::$id_d;
		
		$specData = $this->modelObj->where($whereField.' in (%s)', $idString)->select();
		
		return  $specData;
	}
	/**
	 * 获取规格组相关字段
	 */
	public function getSplitKeyBySpecial()
	{
		return GoodsSpecItemModel::$specId_d;
	}
    
}
