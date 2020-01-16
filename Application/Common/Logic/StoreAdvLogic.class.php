<?php
namespace Common\Logic;

use Common\Model\StoreAdvModel;
use Think\Cache;

/**
 * 
 * @author Administrator
 *
 */
class StoreAdvLogic extends AbstractGetDataLogic
{
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new StoreAdvModel();
	}
	
	/**
	 * 获取店铺广告验证信息
	 */
	public function getCheckMessageByStore()
	{
		return [
			'store_id' => [
				'number' => '必须是数字'
			]
		];
	}
	
	/**
	 * 获取结果（广告）
	 */
	public function getResult()
	{
		$storeId = $this->data['store_id'];
		
		$cacheKey = 's_list'.$storeId;
		
		$cache = Cache::getInstance('', ['expire' => 160]);
		
		$data = $cache->get($cacheKey);
		
		if (!empty($data)) {
			return $data;
		}
		//店铺广告(列表)
		
		$listWhere = [];
		
		$listWhere['store_id'] = $storeId;
		$listWhere['status'] = 0;
		$listWhere['adv_end_date'] = array("GT",time());
		$listWhere['adv_start_date'] = array("LT",time());
		$listWhere['ap_id'] = 1043;
		
		$field = 'id,adv_title,adv_content,ad_url';
		
		$list = $this->modelObj->field($field)->where($listWhere)->select();
		if (empty($list)) {
			return [];
		}
		
		$cache->set($cacheKey, $list);
		
		return $list;
	}
	
	/**
	 * 获取banner
	 */
	public function getBanner()
	{
		$storeId = $this->data['store_id'];
		
		$cacheKey = 'static_adds'.$storeId;
		
		$cache = Cache::getInstance('', ['expire' => 160]);
		
		$data = $cache->get($cacheKey);
		
		if (!empty($data)) {
			return $data;
		}
		
		// 获取店铺轮播图
		$b_where['store_id'] = $storeId;
		$b_where['status'] = 0;
		$b_where['adv_end_date'] = array("GT",time());
		$b_where['adv_start_date'] = array("LT",time());
		$b_where['ap_id'] = 11;
		$field = 'id,adv_title,adv_content,ad_url';
		$data = $this->modelObj->field($field)->where($b_where)->select();
		if (empty($data)) {
			return [];
		}
		
		$cache->set($cacheKey, $data);
		
		return $data;
	}
	
	/**
	 * 获取 不规则图片
	 */
	public function getBannerButton()
	{
		$storeId = $this->data['store_id'];
		
		$cacheKey = 'button_adds'.$storeId;
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($cacheKey);
		
// 		if (!empty($data)) {
// 			return $data;
// 		}
		
		//店铺广告(左)
		
		$lWhere = [];
		
		$lWhere['store_id'] = $storeId;
		$lWhere['status'] = 0;
		$lWhere['adv_end_date'] = array("GT",time());
		$lWhere['adv_start_date'] = array("LT",time());
		$lWhere['ap_id'] = 1038;
		
		$field = 'id,adv_title,adv_content,ad_url';
		
		$data = $this->modelObj->field($field)->where($lWhere)->limit(1)->select();
		
		$reslut = [];
		
		if (!empty($data)) {
			$reslut['left'] = $data;
		}
		
		//店铺广告(右)
		
		$rWhere = [];
		
		$rWhere['store_id'] = $storeId;
		$rWhere['status'] = 0;
		$rWhere['adv_end_date'] = array("GT",time());
		$rWhere['adv_start_date'] = array("LT",time());
		$rWhere['ap_id'] = 1052;
		$reight = $this->modelObj->field($field)->where($rWhere)->limit(2)->select();
		if (!empty($reight)) {
			$reslut['reight'] = $reight;
		}
		
		if (empty($reslut)) {
			return [
				'left' => [],
				'reight' => []
			];
		}
		
		$cache->set($cacheKey, $reslut);
		
		return $reslut;
	}
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return StoreAdvModel::class;
	}
}