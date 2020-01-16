<?php
namespace Common\Logic;

use Common\Model\OpenShopOrderModel;
use Common\Tool\Tool;
use Think\Log;
use Think\SessionGet;

/**
 * 
 * @author Administrator
 *
 */
class OpenShopOrderLogic extends AbstractGetDataLogic
{
	/**
	 * 订单号
	 * @var string
	 */
	private $orderSn = '';
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new OpenShopOrderModel();
		
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
		return OpenShopOrderModel::class;
	}
	
	protected function getParseResultByAdd() :array
	{
		$result = [];
		
		$this->orderSn = Tool::connect('Token')->toGUID();
		
		$result[OpenShopOrderModel::$orderSn_d] = $this->orderSn;
		$result[OpenShopOrderModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		$result[OpenShopOrderModel::$createTime_d] = time();
		$result[OpenShopOrderModel::$storeId_d] = $this->data['id'];
		$result[OpenShopOrderModel::$type_d] = $this->data['type'];
		
		return $result;
	}
	
	/**
	 * @return string
	 */
	public function getOrderSn()
	{
		return $this->orderSn;
	}
	
	/**
	 * 修改状态
	 * @return boolean
	 */
	public function saveStatus()
	{
		$this->modelObj->startTrans();
		
		$status = $this->saveData();
		
		if (empty($status)) {
			
			$this->modelObj->rollback();
			
			$this->errorMessage = '修改状态失败';
			
			Log::write($this->modelObj->getLastSql(), Log::INFO);
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * 保存时处理参数
	 */
	protected function getParseResultBySave() :array
	{
		
		$data = $this->data;
		
		$save = [
			OpenShopOrderModel::$id_d => $data['order_id'],
			OpenShopOrderModel::$payTime_d => time(),
			OpenShopOrderModel::$orderStatus_d => 1,
			OpenShopOrderModel::$payType_d => $data['pay_id'],
			OpenShopOrderModel::$payPlatform_d => 1
		];
		
		return $save;
	}
	
}