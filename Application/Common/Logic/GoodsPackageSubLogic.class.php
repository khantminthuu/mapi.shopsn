<?php
namespace Common\Logic;
use Common\Model\GoodsPackageSubModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;
/**
 * 商品套餐
 * @author Administrator
 *
 */
class GoodsPackageSubLogic extends AbstractGetDataLogic
{
	/**
	 * 字符串
	 * @var string
	 */
	private $packageString = '';
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new GoodsPackageSubModel();
		
	}
	
	/**
	 * 获取结果
	 */
	public function getResult() :array
	{
		
		$packageIdString = implode(',', array_column($this->data, $this->splitKey));
		
		$cackeKey = 's_package_sub'.$packageIdString;
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($cackeKey);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->modelObj
			->field(GoodsPackageSubModel::$id_d.','.GoodsPackageSubModel::$packageId_d.','.GoodsPackageSubModel::$goodsId_d.','.GoodsPackageSubModel::$discount_d.' as goods_discount')
			->where(GoodsPackageSubModel::$packageId_d.' in (%s)', $packageIdString)
			->select();
		
		if (empty($data)) {
			$this->errorMessage = '套餐商品获取失败';
			return [];
		}
		
		$ss = $cache->set($cackeKey, $data);
		
		return $data;
	}
	
	
	/**
	 * 获取套餐商品
	 * @return array
	 */
	public function getGoodsPackageSubListByPackageCache() :array
	{
		
		$key = md5($this->getPackageIdString().'whate_sub_g');
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getGoodsPackageSubListByPackage();
		
		if (count($data) === 0) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 处理商品是否在套餐内
	 * @return array
	 */
	public function parseGoodsIsInPackage() :array
	{
		$data = $this->getGoodsPackageSubListByPackageCache();
		
		if (empty($data)) {
			return [];
		}
		
		$args = $this->data['args'];
		
		$temp = [];
		
		foreach ($data as $key => $value) {
			
			$temp[$value[GoodsPackageSubModel::$packageId_d]][] = $value[GoodsPackageSubModel::$goodsId_d];
		}
		
		$package = [];
		
		foreach ($temp as $key => $value) {
			if (!in_array($args['goods_id'], $value)) {
				$package[] = $key;
			}
		}
		
		if ( ($length = count($package)) === 0 ) {
			return $data;
		}
		
		$threeDimensionalArray = (new ArrayChildren($data))->inTheSameState(GoodsPackageSubModel::$packageId_d);
		
		$subData = [];
		
		$i = 0;
		
		foreach ($temp as $key => $value){
			
			$subData = array_merge($threeDimensionalArray[$key], $subData);
		}
		
		return $subData;
	}
	
	
	/**
	 * 获取套餐编号字符串
	 * @return string
	 */
	protected function getPackageIdString() :string
	{
		if ($this->packageString === '') {
			
			$package = $this->data['package'];
			
			$idString = implode(',', array_column($package, $this->splitKey));
			
			$this->packageString = $idString;
		}
		
		return $this->packageString;
	}
	
	/**
	 * 获取套餐商品
	 * @return array
	 */
	public function getGoodsPackageSubListByPackage() :array
	{
		$args = $this->data['args'];
		
		$idString = $this->getPackageIdString();
		
		$data = $this->modelObj
			->field(GoodsPackageSubModel::$id_d.','.GoodsPackageSubModel::$packageId_d.','.GoodsPackageSubModel::$goodsId_d.','.GoodsPackageSubModel::$discount_d.' as goods_discount')
			->where(GoodsPackageSubModel::$packageId_d.' in (%s)', $idString)
			->select();
		
		if (count($data) === 0) {
			$this->errorMessage = '商品套餐数据有误';
			
			return [];
		}
			
		return $data;
		
	}
	
	/**
	 * 获取商品套餐信息
	 * @return array
	 */
	public function getGoodsPackageSub() :array
	{
		$field = [
			GoodsPackageSubModel::$id_d,
			GoodsPackageSubModel::$packageId_d,
			GoodsPackageSubModel::$goodsId_d,
			GoodsPackageSubModel::$discount_d
		];
		
		$data = $this->getDataByOtherModel($field, GoodsPackageSubModel::$packageId_d);
		
		return $data;
	}
	
	/**
	 * 获取套餐数据
	 */
	public function getPackageSubInfoByOrderPackage()
	{
		$packageData = $this->getResult();
		
		if (empty($packageData)) {
			$this->modelObj->rollback();
			$this->errorMessage = '找不到套餐商品信息';
			return [];
		}
		
		return $packageData;
	}
	
	public function getModelClassName() :string
	{
		return GoodsPackageSubModel::class;
	}
	
	/**
	 * 根据购物车数据获取套餐数据
	 * @return array
	 */
	public function getGoodsPackageSubDataByGoodsCart() :array
	{
		$packageIdString = implode('', array_column($this->data, $this->splitKey));
		
		$cackeKey = 'what_cart_heppend_package_sub'.$packageIdString;
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($cackeKey);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getSlaveDataByMaster();
		
		if (count($data) === 0) {
			$this->errorMessage = '套餐数据错误';
			return [];
		}
		
		$cache->set($cackeKey, $data);
		
		return $data;
	}
	
	/**
	 * 数据处理组合
	 * @param array $slaveData
	 * @param string $slaveColumnWhere
	 * @return array
	 */
	protected function parseSlaveData(array $slaveData, $slaveColumnWhere) :array
	{
		$data = $this->data;
		
		foreach( $slaveData as $key => &$value ){
			if( empty( $data[ $value[$slaveColumnWhere] ] ) ){
				continue;
			}
			unset($data[$value[$slaveColumnWhere]][$this->splitKey], $data[$value[$slaveColumnWhere]]['id']);
			$value = array_merge( $value, $data[ $value[$slaveColumnWhere] ]);
		}
		return $slaveData;
	}
	
	/**
	 * 获取从表字段（根据主表数据查从表数据的附属方法）
	 * @return array
	 */
	protected function getSlaveField () :array {
		return [
			GoodsPackageSubModel::$packageId_d,
			GoodsPackageSubModel::$goodsId_d,
			GoodsPackageSubModel::$discount_d .' as goods_discount',
			GoodsPackageSubModel::$id_d
		];
	}
	
	/**
	 * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
	 */
	protected function getSlaveColumnByWhere() :string
	{
		return GoodsPackageSubModel::$packageId_d;
	}
	
	public function getSplitKeyByGoods() :string
	{
		return GoodsPackageSubModel::$goodsId_d;
	}
}
	