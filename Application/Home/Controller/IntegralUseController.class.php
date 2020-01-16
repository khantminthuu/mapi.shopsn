<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\IntegralUseLogic;

class IntegralUseController
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

        $this->logic = new IntegralUseLogic($args);

    }
    
    /**
     * 积分兑换处理 --生成订单 -立即兑换
     */
    public function confirmExchange()
    {
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->commitConfirm();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    
    /**
     * 积分兑换处理 -- 确认支付
     */
    public function confirmPay(){
        //检测传值                  //检测方法
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->commitPayForGoods();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    
   
} 