<?php

namespace Common\Logic;

use Common\Model\CouponListModel;
use Think\Cache;
use Common\SessionParse\SessionManager;
use Think\SessionGet;

/**
 * 用户优惠券逻辑处理层
 * @author 薛松
 */
class CouponListLogic extends AbstractGetDataLogic
{
	/**
	 * 要更新的数据
	 * @var array
	 */
	private $couponData = [];
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new CouponListModel();
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
		return CouponListModel::class;
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
			CouponListModel::$cId_d,
		];
	}
	/**
	 * @name 优惠券列表验证规则
	 * 
	 * @des 优惠券列表验证规则
	 * @updated 2017-12-21
	 */
	public function getRuleByMyCouponLists()
	{
		$message = [
			'page' => [
				'number' => '参数不正确',
			],
		];
		return $message;
	}
	/**
	 * @name 优惠券列表逻辑
	 * 
	 * @des 优惠券列表逻辑
	 * @updated 2017-12-21
	 */
	public function myCouponLists()
	{
		$userId = session('user_id');
		$this->data['user_id'] = $userId;
		$retData = $this->modelObj->myCouponLists($this->data);
		if(empty($retData) || !isset($retData)){
			$this->errorMessage = '查询数据为空!';
			return [];
		}
		return $retData;
	}
	
	/**
	 * 用户可用代金券
	 * @return array
	 */
	public function getUsersCanUseCoupons()
	{
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$key = 'coupon_key_'.$userId.'145_d';
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->modelObj
			->where(CouponListModel::$status_d.' = 0 and '.CouponListModel::$userId_d.'=:u_id')
			->bind([':u_id' => $userId])
			->getField($this->getColumByGetField());
		
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
		
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			CouponListModel::$id_d,
			CouponListModel::$cId_d,
			CouponListModel::$userId_d,
			CouponListModel::$code_d
		];	
	}
	
	protected function getColumByGetField()
	{
		return CouponListModel::$id_d.','.CouponListModel::$cId_d.','.CouponListModel::$code_d;
	}
	
	/**
	 * 获取优惠券相关字段
	 */
	public function getSplitKeyByCoupon()
	{
		return CouponListModel::$cId_d;
	}
	
	/**
	 * 优惠券 处理
	 */
	public function couponParse()
	{ 
		$coupon = SessionManager::GET_COUPON_LIST();
		
		if (empty($coupon)) {
			return true;
		}
		
		$res = [];
		
		$time = time();
		
		$doUseSession = SessionGet::getInstance('do_use');
		
		$doUse = $doUseSession->get();
		
		foreach ($this->data as $key => $value) {
			if (empty($coupon[$value['store_id']])) {
				continue;
			}
			
			if (empty($doUse[$coupon[$value['store_id']]['id']])) {
				continue;
			}
			
			$res[$coupon[$value['store_id']]['id']][] = $key;
			
			$res[$coupon[$value['store_id']]['id']][] = $time;
			
			$res[$coupon[$value['store_id']]['id']][] = 1;
		}
	
		if (empty($res)) {
			$this->errorMessage = '优惠券使用错误';
			
			$this->modelObj->rollback();
			
			return false;
		}
		
		$this->couponData = $res;
		
		$sql = $this->buildUpdateSql();
		
		try {
			$status = $this->modelObj->execute($sql);
		} catch (\Exception $e) {
			$this->errorMessage = $e->getMessage();
			$this->modelObj->rollback();
			return false;
		}
		
		if (!$this->traceStation($status)) {
			return false;
		}
		
		$doUseSession->delete();
		
		SessionManager::REMOVE_COUPON_LIST();
		
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated()
	 */
	protected function getColumToBeUpdated() :array
	{
		return [
			CouponListModel::$orderId_d,
			CouponListModel::$useTime_d,
			CouponListModel::$status_d
		];	
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getDataToBeUpdated()
	 */
	protected function getDataToBeUpdated() :array
	{
		return $this->couponData;
	}
}