<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\StoreJoinCompanyModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class StoreJoinCompanyLogic extends AbstractGetDataLogic
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
	
	public function getAddressData()
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
        $this->modelObj = new StoreJoinCompanyModel();
      
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin() :array
    {
        $message = [
        	'store_name' => [
        		'required' => '店铺名称必填'
        	],
            'company_name' => [
                'required' => '公司名称必填',
            ],
            'company_mobile' => [
                'required' => '公司电话必填'
            ],
            'registered_capital' => [
                'number'     => '注册资金必须为数字',
            ],
            'name'          => [
                'required' => '联系人姓名必填',
            ],
            'mobile'        => [
                'number'    => '请填入正确的手机号'
            ],
            'prov_id' => [
                'number'     => '省ID必须为数字',
            ],
            'city' => [
                'number'     => '市ID必须为数字',
            ],
            'dist' => [
                'number'     => '区ID必须为数字',
            ],
            'address' => [
                'required' => '公司具体地址必填',
            ],
        	'license_number' => [
        		'required' => '营业执照号必填'
        	],
        	'validity_start' => [
        		'number' => '营业执照开始时间必填',
        	],
        	'electronic_version' => [
        		'required' => '营业执照电子版必填',
        	],
        	'validity_end'          => [
        		'number' => '营业执照结束时间必填',
        	],
        	'scope_of_operation'        => [
        		'required'  => '法定企业经营范围',
        	],
        	'organization_code' => [
        		'required' => '组织机构代码必填'
        	],
        	'organization_electronic' => [
        		'required' => '组织机构代码电子版必填',
        	],
        	'taxpayer_certificate' => [
        		'required' => '一般纳税人证明照片必填',
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
        return StoreJoinCompanyModel::class;
    }

    /**
     * 企业入驻信息提交
     */
    public function add_company_info() :bool{
    	
       $this->modelObj->startTrans();
       
       $status = $this->addData();
        
       if (!$this->traceStation($status)) {
          
       		$this->errorMessage .= '开店失败或者重复开店';
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
    	
    	$data[StoreJoinCompanyModel::$createTime_d] = $time;
    	$data[StoreJoinCompanyModel::$updateTime_d] = $time;
    	
    	$data[StoreJoinCompanyModel::$userId_d] = SessionGet::getInstance('user_id')->get();
    	
    	$data[StoreJoinCompanyModel::$status_d] = 0;
    	
    	return $data;
    }
	
    /**
     * 获取店铺信息
     */
    public function getStoreInfo()
    {
    	$data = $this->modelObj
	    	->field(array_values($this->getStaticProperties()))
	    	->where(StoreJoinCompanyModel::$userId_d.'=:u_id')
	    	->bind([':u_id' => SessionGet::getInstance('user_id')->get()])
	    	->find();
	    if (empty($data)) {
	    	return false;
	    }
    	
    	SessionGet::getInstance('store_data_by_person',[
    			'id' => $data[StoreJoinCompanyModel::$id_d],
    			'type' => 1
    	])->set();
    	return $data;
    }
    
    /**
     * 修改状态
     * @return bool
     */
    public function editStatus()
    {
    	$status = $this->saveData();
    	
    	if (!$this->traceStation($status)) {
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
    	
    	$result[StoreJoinCompanyModel::$id_d] = $this->data['id'];
    	
    	$result[StoreJoinCompanyModel::$status_d] = 1;
    	
    	return $result;
    }
    
    /**
     * 是否可以入住
     */
    public function isCheckIn() :bool
    {
    	$data = $this->modelObj->where(StoreJoinCompanyModel::$userId_d.'=:u_id')
    		->bind([':u_id' => [SessionGet::getInstance('user_id')->get(), \PDO::PARAM_INT]])
    		->getField(StoreJoinCompanyModel::$id_d);
    	
    	if (!empty($data)) {
    		
    		$this->errorMessage = '企业不能重复开店';
    		return false;
    	}
    	
    	return true;
    }
   
}
