<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Logic;

use Common\Model\CouponModel;
use Think\Cache;
use Common\Tool\Tool;
use Common\SessionParse\SessionManager;
use Think\SessionGet;

/**
 * 代金券
 * @author Administrator
 */
class CouponLogic extends AbstractGetDataLogic
{
	/**
	 * 主键编号字符串
	 * @var string
	 */
	private $idString = '';
	
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new CouponModel();
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getResult()
	 */
	public function getResult()
	{
		if (empty($this->data)) {
			return [];
		}
		$key = Tool::characterJoin($this->data, $this->splitKey);
		
		$this->idString = $key;
		
		$key = base64_encode($key).'_'.SessionGet::getInstance('user_id')->get().'_con_what';
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$result = $cache->get($key);
		
		if (!empty($result)) {
			return $result;
		}
		
		$data = $this->getDataByOtherModel($this->getTableColum(), CouponModel::$id_d);
		
		if (empty($data)) {
			return [];
		}
		
		$time = time();
		
		//不可用
		$doNotUse = [];
		
		//可用
		$doUse = [];
		
		foreach ($data as $key => $value) {
			if ( $time < $value[CouponModel::$useStart_time_d]  || $time > $value[CouponModel::$useEnd_time_d] ) {
				$doNotUse[$key] = $value;
			} else {
				$doUse[$key] = $value;
			}
		}
		
		$result = [
			'do_not_use' => $doNotUse,
			'do_use' => $doUse
		];
		$cache->set($key, $result);
		
		return $result;
	}
	
	/**
	 * getDataByOtherModel 附属方法处理where
	 */
	protected function getParseWhereAgainBygetDataByOtherModel($where)
	{
		$store = SessionManager::GET_STORE_ID_BY_STATION();
		
		return $where .' and '.CouponModel::$storeId_d.' in ('.$store.') and '.CouponModel::$status_d.' = 1';
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return CouponModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			CouponModel::$id_d,
			CouponModel::$name_d,
			CouponModel::$money_d,
			CouponModel::$condition_d,
			CouponModel::$useStart_time_d,
			CouponModel::$useEnd_time_d,
			CouponModel::$storeId_d
		];
	}
	
	/**
	 * getDataByOtherModel 附属方法
	 */
	protected function getIdStringByOtherModel()
	{
		return $this->idString;
	}
	
	/**
	 * 优惠券是否可用
	 * @return boolean
	 */
	public function checkCouponIsUse()
	{
		$couponMoney  = SessionManager::GET_COUPON_MONEY();
		
		if (empty($couponMoney)) {
			$this->errorMessage = '商品信息有误';
			return false;
		}
		$data = $this->modelObj
			->field(CouponModel::$condition_d.','.CouponModel::$money_d.','.CouponModel::$storeId_d)
			->where(CouponModel::$id_d.'=%d', $this->data['id'])
			->find();
		if (empty($data)) {
			$this->errorMessage = '未找到优惠券:(';
			return false;
		}
		
		if (empty($couponMoney[$data[CouponModel::$storeId_d]])) {
			$this->errorMessage = '商品信息有误';
			return false;
		}
		
		$money = $couponMoney[$data[CouponModel::$storeId_d]];
		
		if ($money <= 0) {
			return false;
		}
		
		if (empty($this->data['coupon_list_id']) || !is_numeric($this->data['coupon_list_id'])) {
			$this->errorMessage = '参数异常';
			return false;
		}
		
		$compare = (float)($money - $data[CouponModel::$condition_d]);
	
		if ( $compare > 0 ) {
			
			//优惠券抵扣金额
			
			SessionManager::SET_COUPON_LIST([
					$data[CouponModel::$storeId_d] => [
							'c_id' => $this->data['id'],
							'money' => $data[CouponModel::$money_d],
							'condition' => $data[CouponModel::$condition_d],
							'id' => $this->data['coupon_list_id'],
							'store_id' => $data[CouponModel::$storeId_d],
					]
				]
			);
			
			return true;
		}
		
		$this->errorMessage = '未满足优惠金额条件';
		return false;
	}
	
}