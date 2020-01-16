<?php
namespace Common\Logic;

use Common\Model\UserAuthsModel;
use Think\Cache;
use Think\SessionGet;

/**
 * 
 * @author Administrator
 *
 */
class UserAuthLogic extends AbstractGetDataLogic
{
	/**
	 * open_id 数据
	 * @var array
	 */
	private $open = [];
	
	/**
	 * 授权是否过期
	 * @var bool
	 */
	private $isLate = false;
	
	/**
	 * @return boolean
	 */
	public function getIsLate()
	{
		return $this->isLate;
	}
	
	/**
	 * 构造方法
	 * @param array $data
	 * @param array $open open_id
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new UserAuthsModel();
	}
	
	/**
	 * 检测是否已授权(qq)
	 */
	public function getResult()
	{
		$accessToken = $this->data['access_token'];
		
		$openId = $this->data['openid'];
		
		$key = $accessToken.$openId.$this->data['identity_type'];
		
		$cache = Cache::getInstance('', ['expire' => 77]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$field = UserAuthsModel::$local_d;
		
		$data = $this->modelObj->field($field, true)->where(UserAuthsModel::$identityType_d.' = :type_id and '.UserAuthsModel::$credential_d.'=:access_token and '.UserAuthsModel::$identifier_d.'=:op_id')
			->bind([
				':access_token' => $accessToken, 
				':op_id' => $openId,
				':type_id' => $this->data['identity_type']
			])
			->find();
		if (empty($data)) {
			return [];
		}
		
		$time = $data[UserAuthsModel::$expiresIn_d] + $data[UserAuthsModel::$updateAt_d] - time();
		
		if ($time < 21600) {
			//授权过期
			$this->isLate = true;
			return $data;
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 检测是否已授权(wx)
	 */
	public function getWxData()
	{
		
		$openId = $this->data['openid'];
		
		$key = $openId.$this->data['identity_type'];
		
		$cache = Cache::getInstance('', ['expire' => 77]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$field = UserAuthsModel::$local_d;
		
		$data = $this->modelObj->field($field, true)->where(UserAuthsModel::$identityType_d.' = :type_id and '.UserAuthsModel::$identifier_d.'=:op_id')
			->bind([
				':op_id' => $openId,
				':type_id' => $this->data['identity_type']
			])
			->find();
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return UserAuthsModel::class;
	}
	
	/**
	 * 授权处理
	 */
	public function parseUserAuth()
	{
		$openIdData = SessionGet::getInstance('open_id_data')->get();
		
		if (empty($openIdData)) {
			$this->modelObj->rollback();
			$this->errorMessage = "授权数据错误";
			return false;
		}
		
	    $status = $this->addData();
	    
	    if (!$this->traceStation($status)) {
	    	return false;
	    }
	    
	    $this->modelObj->commit();
	    
	    return $status;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$openIdData = SessionGet::getInstance('open_id_data')->get();
		$accessToken = SessionGet::getInstance('access_token')->get();
		$data = [];
		
		$time = time();
		
		$data[UserAuthsModel::$userId_d] =  SessionGet::getInstance('user_id')->get();
		
		$data[UserAuthsModel::$identifier_d] =$openIdData['openid'];
		
		$data[UserAuthsModel::$expiresIn_d] = $accessToken['expires_in'];
		
		$data[UserAuthsModel::$identityType_d] = SessionGet::getInstance('identitifer')->get();
		
		$data[UserAuthsModel::$credential_d] = $accessToken['access_token'];
		
		$data[UserAuthsModel::$updateAt_d] = $time;
		
		$data[UserAuthsModel::$createAt_d] = $time;
		
		$data[UserAuthsModel::$refreshToken_d] = $accessToken['refresh_token'];
		
		$data[UserAuthsModel::$unionid_d] =  isset($accessToken['unionid']) ? "" : $accessToken['unionid'];
		
		return $data;
	}
	
	/**
	 * 获取open id
	 */
	public function getOpenId()
	{
		if (empty($this->data)) {
			return "";
		}
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$key = $this->data['user_id'].$this->data['type'];
		
		$openId = $cache->get($key);
		
		if (!empty($openId)) {
			return $openId;
		}
		
		$openId = $this->modelObj->where(UserAuthsModel::$userId_d.'=:u_id and '.UserAuthsModel::$identityType_d.'=:type')
			->bind([':u_id' => $this->data['user_id'], ':type' => $this->data['type']])
			->getField(UserAuthsModel::$identifier_d);
		if (empty($openId)) {
			return "";
		}
		
		$cache->set($key, $openId);
		
		return $openId;
	}
}