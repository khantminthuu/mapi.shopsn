<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\InvoiceTypeLogic;
use Common\TraitClass\IsLoginTrait;

class InvoiceTypeController
{
    use InitControllerTrait;
    use IsLoginTrait;
    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {   $this->args = $args;
        $this->init();
        $this->isLogin();
        

        $this->invoiceTypeLogic = new InvoiceTypeLogic($args);

     

    }
    /**
     * 获取发票数据
     * @author 王波
     */
    public function getAllInvoice()
    {
        $ret = $this->invoiceTypeLogic->getAllInvoiceInfo(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 根据订单获取发票数据
     * @author 王波
     */
    public function getInvoiceByOrder()
    {   
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->invoiceTypeLogic->getInvoiceInfoByOrder(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 添加发票抬头发票数据
     * @author 王波
     */
    public function invoicesAreRaisedAdd()
    {    
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByAddAreRaised(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getInvoicesAreRaisedAdd(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 删除发票抬头发票数据
     * @author 王波
     */
    public function invoicesAreRaisedDelete()
    {   
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByDelAreRaised(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getInvoicesAreRaisedDel(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 修改发票抬头发票数据
     * @author 王波
     */
    public function invoicesAreRaisedSave()
    {   
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByDelAreRaised(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getInvoicesAreRaisedSave(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 添加发票数据
     * @author 王波
     */
    public function invoicesOrderAdd()
    {   
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByAddnvoices(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->invoiceTypeLogic->getInvoicesOrderAdd(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 修改发票数据
     * @author 王波
     */
    public function invoicesOrderSave()
    {   
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateBySaveIvoices(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getInvoicesOrderSave(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的发票
    public function myInvoice(){
        $ret = $this->invoiceTypeLogic->getInvoices(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
     //我的发票--添加增值发票
    public function capitaAdd(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByAddCapita(), $this->args);
        
        $status = $checkObj->checkParam();
        
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->invoiceTypeLogic->getCapitaAdd(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    
     //我的发票--修改增值发票
    public function capitaSave(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByDelAreRaised(), $this->args);
        
        $status = $checkObj->checkParam();
        
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByAddCapita(), $this->args);
        
        $status = $checkObj->checkParam();
        
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getCapitaSave(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    
     //我的发票--删除增值发票
    public function capitaDelete(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->invoiceTypeLogic->getValidateByDelAreRaised(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        $ret = $this->invoiceTypeLogic->getCapitaDelete(); 

        $this->objController->promptPjax($ret, $this->invoiceTypeLogic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}
