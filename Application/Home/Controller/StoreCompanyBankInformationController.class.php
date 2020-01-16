<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreCompanyBankInformationLogic;


class StoreCompanyBankInformationController
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
        
        $this->logic = new StoreCompanyBankInformationLogic($args);

     

    }
    /***
     * 填写银行卡信息
     */
    public function storeBank(){
    	$checkObj = new CheckParam($this->logic->getMessageValidateBankInfo(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->addBankInfo();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

}