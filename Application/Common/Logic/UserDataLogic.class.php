<?php
declare(strict_types = 1);

namespace Common\Logic;

use Common\Model\UserDataModel;
use Think\SessionGet;
use Think\Cache;

class UserDataLogic extends AbstractGetDataLogic
{
	/**
	 * @var integer
	 */
	private $integralShopping = 0;
	
	public function getIntegralShopping() :int
	{
		return $this->integralShopping;
	}
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new UserDataModel();
	}
	
	/**
	 * 获取结果
	 */
	public function getResult()
	{
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName() :string
	 */
	public function getModelClassName() :string
	{
		return UserDataModel::class;
	}
	
	public function updateIntegralByAdd() :bool
	{
		$userId = SessionGet::getInstance('user_id')->get();
		
		
		$data = $this->getIntegralByUserIdCache();
		
		$status = false;
			
		if (empty($data)) {
			$status = $this->addData();
		} else {
			$status = $this->modelObj
				->where(UserDataModel::$id_d.'='.$data[UserDataModel::$id_d])
				->setInc(UserDataModel::$currentIntegral_d, $this->data['total_integral']);
		}
			
		if (!$this->traceStation($status)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 根据用户获取积分
	 * @return array
	 */
	public function getIntegralByUserId() :array
	{
		$userId = SessionGet::getInstance('user_id')->get();
		
		$data = $this->modelObj
			->field(UserDataModel::$id_d.','.UserDataModel::$currentIntegral_d)
			->where(UserDataModel::$userId_d.'=:u_id')
			->bind([':u_id' => $userId])
			->find();
		
		if (empty($data)) {
			$this->errorMessage = '没有积分';
			return [];
		}
		
		return $data;
	}
	
	public function getIntegralAndSaveSession() :array
	{
		$data = $this->getIntegralByUserId();
		
		if (empty($data)) {
			return [];
		}
		
		SessionGet::getInstance('my_integ_number', $data[UserDataModel::$currentIntegral_d])->set();
		
		return $data;
	}
	
	
	/**
	 * 根据用户获取积分
	 * @return array
	 */
	public function getIntegralByUserIdCache() :array
	{
		$key = 'whar_user_integral_s'.SessionGet::getInstance('user_id')->get();
		
		$cache = Cache::getInstance('', ['expire' => 77]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getIntegralByUserId();
		
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$data = [];
		
		$data[UserDataModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		
		$data[UserDataModel::$currentIntegral_d] = $this->data['total_integral'];
		
		$data[UserDataModel::$alreadyIntegral_d] = 0;
		
		$data[UserDataModel::$createTime_d] = $time = time();
		
		$data[UserDataModel::$updateTime_d] = $time;
		
		return $data;
	}
	
	
	/**
	 * 积分结算
	 */
	public function integralSettleMement() :bool
	{
		if (!$this->integralIsEnough()) {
			return false;
		}
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$this->modelObj->startTrans();
		
		$tableName = $this->modelObj->getTableName();
		
		$time = time();
		
		$sql = <<<aaa
		Update {$tableName} set current_integral = current_integral - {$this->integralShopping}, 
		already_integral = already_integral + {$this->integralShopping},
		update_time = {$time}
		where user_id = {$userId};
aaa;
		
		$status = $this->modelObj->execute($sql);
			
		if (!$this->traceStation($status)) {
			$this->errorMessage = '积分操作失败';
			return false;
		}
		
		return true;
		
	}
	
	
	/**
	 * 积分是否足够
	 */
	private function integralIsEnough()
	{
		if (empty($this->data)) {
			$this->errorMessage = '积分订单数据错误';
			return false;
		}
		
		$integral = $this->sumShoppingIntegral();
		
		$myIntegral = SessionGet::getInstance('my_integ_number')->get();
		
		if ($myIntegral - $integral < 0) {
			$this->errorMessage = '积分订不足';
			return false;
		}
		
		$this->integralShopping = $integral;
		return true;
	}
	/**
	 *
	 * @return number
	 */
	private function sumShoppingIntegral()
	{
		$integral = 0;
		
		foreach ($this->data as $key => $value) {
			$integral += $value['integral'];
		}
		
		return $integral;
	}
	
	/**
	 * 积分关联字段
	 * @return string
	 */
	public function getSplitKeyByIntegral() :string
	{
		return UserDataModel::$currentIntegral_d;
	}
}
