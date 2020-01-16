<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\StorePersonModel;
use Common\Model\CommonModel;
use Think\Log;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class StorePersonLogic extends AbstractGetDataLogic
{
	/**
	 * 插入的编号
	 * @var string
	 */
	private $insertId = '0';
	
	public function getInsertId() :string
	{
		return $this->insertId;
	}
	
	/**
	 * 要添加的店铺地址数据
	 * @var array
	 */
	private $addressData = [];
	
	public function getAddressData() :array
	{
		return $this->addressData;
	}
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new StorePersonModel();
	}
	/**
	 *
	 * 返回验证数据
	 */
	public function getValidateByLogin() :array
	{
		$message = [
						'store_name' => [
										'required' => '店铺名称必填',
						],
						'person_name' => [
										'required' => '姓名必填',
						],
						'id_card' => [
										'required' => '身份证号必填',
						],
						'prov_id' => [
										'required' => '省份必选',
						],
						'city' => [
										'required' => '市必选',
						],
						'dist' => [
										'required' => '区必选',
						],
						'address' => [
										'required' => '具体地址必填',
						],
						'mobile' => [
										'required' => '联系方式必填',
						],
						'wx_account' => [
										'required' => '微信支付账号必填'
						],
						'alipay_account' => [
										'required' => '支包付账号必填'
						],
						'bank_account' => [
										'required' => '银行卡号必填'
						],
						'bank_name' => [
										'required' => '银行名称必填'
						],
						'idcard_positive' => [
										'required' => '身份证正面必填',
						],
						'other_side' => [
										'required' => '身份证反面必填',
						],
						'bank_name' => [
										'required' => '银行名称必填',
						],
						'bank_account' => [
										'required' => '银行账号必填',
						],
		];
		return $message;
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
		return StorePersonModel::class;
	}
	
	
	/**
	 * 个人开店
	 * @return boolean
	 */
	public function personEnter() :bool
	{
		
		$this->modelObj->startTrans();
		
		$status = $this->addData();
		
		if (!$this->traceStation($status)) {
			$this->errorMessage .= '、已开店'; 
			return false;
		}
		
		$this->insertId = $status;
		
		$this->addressData = [
			'store_id' => $status,
			'prov_id' => $this->data['prov_id'],
			'city' => $this->data['city'],
			'dist' => $this->data['dist'],
			'address' => $this->data['address']
		];
		
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		$data = $this->data;
		
		$time = time();
		
		$data[StorePersonModel::$status_d] = 0;
		
		$data[StorePersonModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		
		$data[StorePersonModel::$createTime_d] = $time;
		
		$data[StorePersonModel::$updateTime_d] = $time;
		
		return $data;
	}
	
	/***
	 * 上传身份证信息 -- 个人入驻
	 */
	public function personCard(){
		// 验证用户与入驻信息是否匹配
		$result_check = $this->check_user_store($this->data[StorePersonModel::$id_d]);
		
		if ($result_check != true){
			return $result_check;
		}
		
		$where_id['id'] = $this->data[StorePersonModel::$id_d];
		
		$data['idcard_positive'] = $this->data[StorePersonModel::$idcardPositive_d];
		$data['other_side'] = $this->data[StorePersonModel::$otherSide_d];
		$data['update_time'] = time();
		$result_card = $this->modelObj->where($where_id)->save($data);
		
		if (!$result_card){
			return array("status"=>0,"message"=>"修改失败","data"=>"");
		}
		return array("status"=>1,"message"=>"修改成功","data"=>"");
	}
	
	/***
	 * 个人入驻 - 填写结算账号信息
	 */
	public function person_card_info(){
		$result_check = $this->check_user_store($this->data[StorePersonModel::$id_d]);
		if ($result_check != true){
			return $result_check;
		}
		$where_id['id'] = $this->data[StorePersonModel::$id_d];
		$data[StorePersonModel::$bankName_d] = $this->data[StorePersonModel::$bankName_d];
		$data[StorePersonModel::$bankAccount_d] = $this->data[StorePersonModel::$bankAccount_d];
		$data[StorePersonModel::$updateTime_d] = time();
		$result_card = $this->modelObj->where($where_id)->save($data);
		if ($result_card){
			return true;
		}
		return false;
		
	}
	
	/***
	 * @param $id
	 * @return bool
	 * 验证用户与店铺信息是否正确  用户只能操作自己的店铺
	 */
	public function check_user_store($id){
		$where  = [
						StorePersonModel::$userId_d  =>session('user_id'),
						StorePersonModel::$id_d  =>$id,
		];
		$result = $this->modelObj->where($where)->find();
		if (empty($result)){
			$result_data = [
							"status" => 0,
							"messsage"    => "个人或店铺信息错误",
							"data"=>""
			];
			return $result_data;
		}
		return true;
	}
	
	/**
	 * 获取店铺信息
	 */
	public function getStoreInfo()
	{
		$data = $this->getStore();
		
		
		
		if (empty($data)) {
			return [];
		}
		
		
		SessionGet::getInstance('store_data_by_person', [
			'id' => $data[StorePersonModel::$id_d],
			'type' => 0
		])->set();
		
		return $data;
	}
	
	/**
	 * 获取店铺信息
	 * @return array
	 */
	public function getStore()
	{
		$data = $this->modelObj
			->field(array_values($this->getStaticProperties()))
			->where(StorePersonModel::$userId_d.'=:u_id')
			->bind([':u_id' => SessionGet::getInstance('user_id')->get()])
			->find();
		
		if (empty($data)) {
			return [];
		}
		return $data;
	}
	
	/**
	 * 是否可以开店
	 * @return bool
	 */
	public function checkIsOpenStore() :bool
	{
		$data = $this->getStore();
		
		return empty($data);
	}
	
	/**
	 * 修改状态
	 * @return bool
	 */
	public function editStatus()
	{
		$status = $this->saveData();
		
		if (!$this->traceStation($status)) {
			Log::write($this->modelObj->getLastSql(), Log::INFO, '', './Log/open_shop/'.date('y_m_d'));
			return false;
		}
		
		$this->modelObj->commit();
		
		return true;
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultBySave() :array
	{
		$result = [];
		
		$result[StorePersonModel::$id_d] = $this->data['id'];
		
		$result[StorePersonModel::$status_d] = 1;
		
		return $result;
	}
	
	/**
	 * 是否可以入住
	 */
	public function isCheckIn() :bool
	{
		$data = $this->modelObj->where(StorePersonModel::$userId_d.'=:u_id')
		->bind([':u_id' => [SessionGet::getInstance('user_id')->get(), \PDO::PARAM_INT]])
		->getField(StorePersonModel::$id_d);
		
		if (!empty($data)) {
			$this->errorMessage = '个人不能重复开店';
			return false;
		}
		
		return true;
	}
	
}
