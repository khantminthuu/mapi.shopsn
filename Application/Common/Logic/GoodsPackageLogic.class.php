<?php
declare(strict_types = 1);
namespace Common\Logic;
use Common\Model\GoodsPackageModel;
use Think\Cache;
use Think\SessionGet;
/**
 * 商品套餐
 * @author Administrator
 *
 */
class GoodsPackageLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new GoodsPackageModel();
		
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
		return GoodsPackageModel::class;
	}
	
	public function getValidateByGoodsPackage() :array{
		$message = [ 
			'id' => [ 
				'required' => '套餐Id参数必须',
				'checkStringIsNumber' => '套餐Id,例如1,2,3' 
			] 
		];
		return $message;
	}
	
	//套餐立即购买--获取商品详情
	public function getPackageBuyNow() :array
	{
		
		$carry_id = $this->data['id'];
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$catch_name = $carry_id . "goods_package".$userId;
		// 检查缓存中时候有商品信息 如果没有则进行查询如果有的话则进行提取
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($catch_name);
		
		if (! empty ( $data )) {
			return $data;
		}
		
		$field = 'id,total,discount,store_id,title';
		
		$reData = $this->modelObj->field($field)->where('id in(%s)', $carry_id)->select();
		
		if (empty ($reData)) {
			return [];
		}
		
		$cache->set($catch_name, $reData);
		
		return $reData;
	}
	
	/**
	 * 获取套餐数据
	 */
	public function getPackageInfoByOrderPackage() :array
	{
		$packageData = $this->getPackageBuyNow();
		
		if (empty($packageData)) {
			$this->modelObj->rollback();
			$this->errorMessage = '找不到套餐信息';
			return [];
		}
		
		return $packageData;
	}
	
	/**
	 * 获取关联字段
	 * @return string
	 */
	public function getSplitKeyByStore() :string
	{
		return GoodsPackageModel::$storeId_d;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
	 */
	public function getMessageNotice() :array
	{
		return [
			'goods_id' => [
				'number' => '商品编号必须是数字',
			],
			'store_id' => [
				'number' => '店铺编号必须是数字',
			]
		];
	}
	
	
	/**
	 * 获取商品套餐
	 */
	public function getPackageListCache() :array
	{
		$key = $this->data['store_id'].'package_list_me';
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getPackageList();
		
		if (count($data) === 0) {
			$this->errorMessage = '没有套餐数据';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 获取商品套餐
	 */
	public function getPackageList() :array
	{
		$data = $this->modelObj
			->field($this->getTableColum())
			->where(GoodsPackageModel::$storeId_d.'=:store and '.GoodsPackageModel::$status_d.' = 1')
			->bind([':store' => $this->data['store_id']])
			->select();
		
		return $data;
	}
	
	/**
	 * 获取要查询的字段
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			GoodsPackageModel::$id_d,
			GoodsPackageModel::$discount_d,
			GoodsPackageModel::$title_d.' as package_title',
			GoodsPackageModel::$storeId_d,
			GoodsPackageModel::$total_d
		];
	}
	
	/**
	 * 根据套餐购物车获取套餐商品数据
	 * @return array
	 */
	public function getPackageByPackageCart() :array
	{
		$field = $this->getTableColum();
		
		$data = $this->getDataByOtherModel($field, GoodsPackageModel::$id_d);
		
		return $data;
	}
	
	/**
	 * 根据套餐购物车获取套餐商品数据并缓存
	 * @return array
	 */
	public function getPackageByPackageCartCache() :array
	{
		$key = md5(implode(',', array_column($this->data, $this->splitKey)).'whate_package_cart_nz');
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getPackageByPackageCart();
		
		if (count($data)=== 0) {
			$this->errorMessage = '找不到套餐数据';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
}