<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StorePersonLogic;
use Common\Logic\StoreJoinCompanyLogic;
use Common\Logic\StoreAddressLogic;
use Think\SessionGet;

class StorePersonController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
    	$this->args = $args;
    	
        $this->_initUser();

        $this->logic = new StorePersonLogic($args);

    }
    /**
     * 填写基本开店信息
     *
     */
    public function personEnter()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam(); 

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
        //检测企业是否开店
        $personLogic = new StoreJoinCompanyLogic();
        
        $status = $personLogic->isCheckIn();
        
        $this->objController->promptPjax($status, $personLogic->getErrorMessage());
        
        $ret = $this->logic->personEnter();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        $addressData = $this->logic->getAddressData();
        
        $storeAddressLogic = new StoreAddressLogic($addressData);
        
        $resultAddress =  $storeAddressLogic->addAddressStore();
        
        $this->objController->promptPjax($resultAddress, $storeAddressLogic->getErrorMessage());
        
         SessionGet::getInstance('store_name', $this->args['store_name'])->set();
        
         SessionGet::getInstance('add_join_company_id', $this->logic->getInsertId())->set();
        
         SessionGet::getInstance('store_type', 0)->set();//个人入住
        
        $this->objController->ajaxReturnData('');
    }
}