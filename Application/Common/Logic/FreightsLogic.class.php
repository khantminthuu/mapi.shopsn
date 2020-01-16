<?php

namespace Common\Logic;

use Think\Cache;
use Common\Model\FreightsModel;
use Common\Tool\Extend\ArrayChildren;
use Think\Log;
use Think\SessionGet;

/**
 *
 * @author 王强
 */
class FreightsLogic extends AbstractGetDataLogic {
	/**
	 * 包邮数组
	 */
	private $isToPost = [];
	
	private $isAllToPost = FALSE;
	
	/**
	 * 店铺编号
	 * @var integer
	 */
	private $storeId = 0;
	
	/**
	 * 
	 * @return number
	 */
	public function getStoreId()
	{
		return $this->storeId;
	}
	/**
	 * @return []
	 */
	public function getIsPost() {
		return $this->isToPost;
	}
	
	/**
	 * @return bool
	 */
	public function getIsAllPost() {
		return $this->isAllToPost;
	}
	
	/**
	 * 构造方法
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new FreightsModel ();
	}
	
	/**
	 * 获取运费(立即购买)
	 *
	 * @return float
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
		return FreightsModel::class;
	}
	
	/**
	 * 验证数据(计算运费运算量大 特此简化验证操作)
	 * @return bool
	 */
	public function getValidateSource() {
		
		$expressId = SessionGet::getInstance('express_id')->get();
		
		if (empty($expressId)) {
			$this->errorMessage = '运费模板为空';
			return false;
		}
		
		if (!isset($this->data['prov_id']) || !is_numeric($this->data['prov_id'])) {
			$this->errorMessage = '省级必须都是数字';
			return false;
		}
		
		if (!isset($this->data['city_id']) || !is_numeric($this->data['city_id'])) {
			$this->errorMessage = '市级必须都是数字';
			return false;
		}
		
		if (!isset($this->data['dist_id']) || !is_numeric($this->data['dist_id'])) {
			$this->errorMessage = '县级必须都是数字';
			return false;
		}
		
		return true;
	}
	
	/**
	 * 获取运费配置
	 * @return float
	 */
	public function getFreightTemplate() {
		
		
		$cache = Cache::getInstance ( '', [ 
			'expire' => 100 
		] );
		
		$expressId = SessionGet::getInstance('express_id')->get();
		
		$idString = implode(',', array_keys($expressId));
		
		$cacheKey = base64_encode($idString);
		
		$data = $cache->get($cacheKey);
		
		if (!empty ( $data )) {
			
			return $data;
		}
		try {
			$data = $this->modelObj
				->field ( $this->getTableColum () )
				->where ( FreightsModel::$id_d . ' in(%s)', $idString)
				->select();
		} catch (\Exception $e) {
			Log::write('运费处理'.print_r($expressId, true), Log::ERR, '', './Log/express/exp_'.date('y_m_d', time()).'.txt');
			$this->errorMessage = '运费计算异常';
			return [];
		}
		if (empty ( $data )) {
			$this->errorMessage = '运费模板配置错误';
			return [];
		}
		
		$data = (new ArrayChildren($data))->convertIdByData(FreightsModel::$id_d);
		
		$flag = 0;
		
		foreach ( $expressId as $key => $storeData) {
			
			if (empty($data[$key])) {
				
				$this->errorMessage = '该商铺没有设置对应的模板';
				
				$this->storeId = $storeData;
				
				return [];
			}
		}
		
		//谁指定条件包邮
		foreach ($data as $key => $value) {
			
			if ($value[FreightsModel::$isFree_shipping_d] == 1 && $value[FreightsModel::$isSelect_condition_d] == 0) {
				$flag ++;
			} elseif ($value[FreightsModel::$isFree_shipping_d] == 0 && $value[FreightsModel::$isSelect_condition_d] == 1) {
				$this->isToPost[$key] = $value;
			}
		}
		
		$this->isAllToPost = $flag ===count($data);
		
		$cache->set($cacheKey, $data );
		return $data;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array {
		return [ 
			FreightsModel::$id_d,
			FreightsModel::$expressTitle_d,
			FreightsModel::$isFree_shipping_d,
			FreightsModel::$valuationMethod_d,
			FreightsModel::$isSelect_condition_d,
			FreightsModel::$sendTime_d 
		];
	}
}