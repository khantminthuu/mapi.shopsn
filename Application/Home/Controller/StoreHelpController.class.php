<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\StoreHelpLogic;


class StoreHelpController
{
    use InitControllerTrait;

    /**
     * 架构方法
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {
        $this->init();

        $this->args = $args;

        $this->logic = new StoreHelpLogic($args);

     

    }
    /**
     * 入驻流程
     *
     */
    public function enterFlow()
    {
        //检测传值                  //检测方法
        $ret = $this->logic->enter_flow("入驻流程");

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 招商标准
     *
     */
    public function standardOfInvestment()
    {
        //检测传值                  //检测方法
        $ret = $this->logic->enter_flow("入驻标准");

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 合作细则
     *
     */
    public function rulesOfCooperation()
    {
        //检测传值                  //检测方法
        $ret = $this->logic->enter_flow("合作细则");

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    public function agreementOfInvestment(){
        //检测传值                  //检测方法
        $ret = $this->logic->enter_flow("入驻协议"); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    public function storeHelp(){
        //检测传值                  //检测方法
        $ret = $this->logic->getStoreHelp(); 

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    //获取问题查询分类
    public function helpClass(){
        $ret = $this->logic->getHelpClass();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //获取问题查询列表
    public function helpList(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getMessageByClass(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getHelpList();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //获取问题查询详情
    public function helpInfo(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getMessageById(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getHelpInfo();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

    public function customerService(){
        $ret = $this->logic->getCustomerUrl();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}