<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderReturnGoodsLogic;
use Common\TraitClass\IsLoginTrait;
class OrderReturnGoodsController
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

        $this->logic = new OrderReturnGoodsLogic($args);

    }
    //提交售后申请
    public function customerService(){
    	$checkObj = new CheckParam($this->logic->getValidateByApply(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->applyForAfterSale();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //填写快递单号
    public function courierNumber(){
    	$checkObj = new CheckParam($this->logic->getValidateByNumber(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->setCourierNumber();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //申请售后列表
    public function progressQueryList(){
    	$ret = $this->logic->getProgressQuery();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //申请售后进度查询
    public function progressQuery(){
    	$checkObj = new CheckParam($this->logic->getValidateByQuery(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
    	$ret = $this->logic->returnInfo();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //查询订单
    public function searchOrder(){
        $checkObj = new CheckParam($this->logic->getValidateBySearch(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getSearchOrder();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}