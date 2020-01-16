<?php
namespace Common\Logic;

use Think\Cache;
use Common\Model\PayTypeModel;
use Common\Model\PayModel;
use Common\TraitClass\PayTrait;

/**
 *
 * @author Administrator
 */
class PayLogic extends AbstractGetDataLogic {
	use PayTrait;
	
	/**
	 * 微信回调数据
	 * @var array
	 */
	private $resource = [];
	
	/**
	 * 获取订单编号
	 * @return []
	 */
	public function getResource()
	{
		return $this->resource;
	}
	
	/**
	 * 构造方法
	 *
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new PayModel ();
	}
	
	/**
	 * 
	 */
	public function getValidateByPay()
	{
		return [
			PayModel::$payType_id_d => [
				'number' => '支付编号必须是数字'
			],
		];
	}
	
	/**
	 * 余额充值验证
	 * @return string[][]
	 */
	public function getValidateByRecharge()
	{
		return [
			PayModel::$payType_id_d => [
				'number' => '支付编号必须是数字'
			],
			'money' => [
				'number' => '充值金额必须是数字'
			]
		];
	}
	
	/**
	 * 余额充值验证
	 * @return string[][]
	 */
	public function getValidateByOpenShop()
	{
		return [
			PayModel::$payType_id_d => [
				'number' => '支付编号必须是数字'
			]
		];
	}
	
	/**
	 * 获取支付信息
	 */
	public function getResult() {
		if (empty($this->data)) {
			return array ();
		}
		
		$cache = Cache::getInstance('', ['expire' => 300]);
		
		$key = 'MONERYD'.$this->data[PayModel::$payType_id_d].'WHERE_IS_YOU'.$this->data['platform'].$this->data['special_status'];
		$data = $cache->get( $key );
		if (!empty ( $data )) {
			return $data;
		}
		
		$field = [PayModel::$createTime_d, PayModel::$updateTime_d];
		
		$data = $this->modelObj->field ( $field,true )
			->where ( PayModel::$payType_id_d . '= %d and ' . PayModel::$type_d . '= %d and '.PayModel::$specialStatus_d.'=%d', [
						$this->data[PayModel::$payType_id_d],
						$this->data['platform'],
						$this->data['special_status']
			] )->find ();
		if (empty ( $data )) {
			return array ();
		}
		
		$data['token'] = $_COOKIE['PHPSESSID'];
		
		$cache->set( $key, $data);
		
		return $data;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return PayModel::class;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array {
		return [ 
				PayTypeModel::$id_d,
				PayTypeModel::$typeName_d,
				PayTypeModel::$isDefault_d 
		];
	}
	
	/**
	 * 获取数据(微信回调)
	 * @return []
	 */
	public function assignKey()
	{
		$paySource = $this->data;
		
		if (empty($paySource['attach'])) {
			return null;
		}
		
		$payData = $this->getPayConfigByPrimarykey();
		
		$obj = $this->getPayConfig($payData);
		
		if (!is_object($obj)) {
			return null;
		}
		return $payData;
	}
	
	/**
	 * 获取支付配置
	 */
	public function getConfigByAlipay()
	{
		$payConf = $this->data;
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		
		$key = 'ALIPAY_CONFIG_BY_ID'.$this->data['id'];
		
		$data = $cache->get($key);
		
		if (empty($data)) {
			$field = [PayModel::$createTime_d, PayModel::$updateTime_d];
			
			$data = $this->modelObj->field($field, true)->where(PayModel::$id_d.'= %d', $this->data['id'])->find();
		} else {
			return $data;
		}
		
		if (empty($data)) {
			$this->errorMessage = '暂无数据';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 根据主键获取支付信息
	 */
	public function getPayConfigByPrimarykey()
	{
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$attch = json_decode($this->data['attach'], true);
		
		$this->resource = $attch;
		
		$key = 'PAY_CONFIG_BY_ID'.$attch['pay_id'];
		
		$data = $cache->get($key);
		
		if (empty($data)) {
			$field = [PayModel::$createTime_d, PayModel::$updateTime_d];
			
			$data = $this->modelObj->field($field, true)->where(PayModel::$id_d.'= %d', $attch['pay_id'])->find();
		} else {
			return $data;
		}
		
		if (empty($data)) {
			$this->errorMessage = '暂无数据';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 * 根据主键获取支付信息（余额支付）
	 */
	public function getPayConfigByBalancePay()
	{
		$cache = Cache::getInstance('', ['expire' => 600]);
		
		
		$key = 'BALANCE_PAY_CONFIG_BY_ID'.$this->data['pay_id'];
		
		$data = $cache->get($key);
		
		if (empty($data)) {
			$field = [PayModel::$createTime_d, PayModel::$updateTime_d];
			
			$data = $this->modelObj->field($field, true)->where(PayModel::$id_d.'= %d', $this->data['pay_id'])->find();
		} else {
			return $data;
		}
		
		if (empty($data)) {
			$this->errorMessage = '暂无数据';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
}