<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreJoinCompanyLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StorePersonLogic;
use Common\Logic\StoreAddressLogic;
use Think\SessionGet;
class StoreJoinCompanyController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
    	$this->args = $args;
		
    	$this->_initUser();

        $this->logic = new StoreJoinCompanyLogic($args);

    } 
    /**
     * 企业入驻
     */
    public function storeJoinCompany()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
        //检测个人是否开店
        $personLogic = new StorePersonLogic();
        
        $status = $personLogic->isCheckIn();
        
        $this->objController->promptPjax($status, $personLogic->getErrorMessage());
        
        $ret = $this->logic->add_company_info();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        $addressData = $this->logic->getAddressData();
        
        $storeAddressLogic = new StoreAddressLogic($addressData);
        
        $resultAddress =  $storeAddressLogic->addAddressStore();
        
        $this->objController->promptPjax($resultAddress, $storeAddressLogic->getErrorMessage());
        
        SessionGet::getInstance('add_join_company_id', $this->logic->getInsertId())->set();
        
         SessionGet::getInstance('store_name', $this->args['store_name'])->set();
        
         SessionGet::getInstance('store_type', 1)->set();//企业入住
        
        $this->objController->ajaxReturnData($ret);
    }
}