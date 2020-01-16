<?php
namespace Common\Logic;
use Common\Model\StoreCompanyBankInformationModel;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class StoreCompanyBankInformationLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreCompanyBankInformationModel();
      
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
    	return StoreCompanyBankInformationModel::class;
    }

	
    public function addBankInfo(){
		
    	if (empty(SessionGet::getInstance('add_join_company_id')->get())) {
    		$this->errorMessage = '店铺异常';
    		return false;
    	}
    	
        $result = $this->addData();
        
        return $result;
    }
    
    /**
     * 验证添加信息
     */
    public function getMessageValidateBankInfo() :array
    {
    	return [
	    	StoreCompanyBankInformationModel::$accountName_d => [
    				'required' => '开户名必填',
    				'specialCharFilter' => '开户名必须是中英文及其字符串的组合',
	    	],
	    	StoreCompanyBankInformationModel::$companyAccount_d => [
	    			'number' => '公司银行账号必填',
	    	],
	    	StoreCompanyBankInformationModel::$branchBank_d => [
    				'required' => '开户银行支行名称必填',
    				'specialCharFilter' => '开户银行支行名称必须是中英文及其字符串的组合',
	    	],
	    	StoreCompanyBankInformationModel::$settleName_d => [
	    			'required' => '结算账户开户名必填',
	    			'specialCharFilter' => '结算账户开户名必须是中英文及其字符串的组合',
	    	],
	    	StoreCompanyBankInformationModel::$settleAccount_d => [
	    			'number' => '结算公司银行账号必填',
	    	],
	    	StoreCompanyBankInformationModel::$settleBank_d => [
	    			'required' => '结算开户银行支行名称必填',
	    			'specialCharFilter' => '结算开户银行支行名称必须是中英文及其字符串的组合',
	    	],
	    	StoreCompanyBankInformationModel::$certificateNumber_d => [
	    			'required' => '税务登记证号必填',
	    			'combinationOfDigitalEnglish' => '税务登记证号必须是数字及其字母的组合'
	    	],
	    	StoreCompanyBankInformationModel::$registrationElectronic_d => [
	    			'required' => '税务登记证号电子版必填',
	    	],
	    	StoreCompanyBankInformationModel::$wxAccount_d => [
	    			'required' => '微信支付账号必填',
	    			'specialCharFilter' => '微信支付账号必须是中英文及其字符串的组合',
	    	],
	    	StoreCompanyBankInformationModel::$alipayAccount_d => [
	    			'required' => '支付宝支付账号必填',
	    			'specialCharFilter' => '支付宝支付账号必须是中英文及其字符串的组合',
	    	]
    	];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
     */
    protected function getParseResultByAdd() :array
    {
    	$data = $this->data;
    	
    	$data[StoreCompanyBankInformationModel::$storeId_d] =  SessionGet::getInstance('add_join_company_id')->get();
    	
    	return $data;
    }
   
}
