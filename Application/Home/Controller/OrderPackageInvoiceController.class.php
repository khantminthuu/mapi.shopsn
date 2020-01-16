<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderPackageInvoiceLogic;
use Common\TraitClass\IsLoginTrait;
class OrderPackageInvoiceController
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
        $this->logic = new OrderPackageInvoiceLogic($args);
    }
    //获取发票信息
    public function getAllInvoice(){
        $ret = $this->logic->getAllInvoiceInfo(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //保存订单发票
    public function invoicesOrderAdd(){
         //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByAddnvoices(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getInvoicesOrderAdd(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //修改订单发票
    public function invoicesOrderSave(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateBySaveIvoices(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getInvoicesOrderSave(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //根据订单获取发票数据
    public function getInvoiceByOrder(){
         //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getInvoiceInfoByOrder(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}