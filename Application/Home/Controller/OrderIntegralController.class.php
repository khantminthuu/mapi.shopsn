<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderIntegralLogic;
use Common\SessionParse\SessionManager;


class OrderIntegralController
{
    use InitControllerTrait;

    /**
     * 积分兑换
     * @param array
     * $args   传入的参数数组
     */
    public function __construct(array $args = [])
    {

        $this->args = $args;
		
        $this->_initUser();
        
        $this->logic = new OrderIntegralLogic($args);

    }

    /**
     * 获取我的积分兑换商品
     */
    public function myConfirm(){
        $ret = $this->logic->getMyConfirm();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

    /**
     * 获取积分兑换订单详情 
     *
     */
    public function integralGoodsInfo(){
        $checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getConfirmInfo();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        
        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 积分兑换商品  确认收货
     *
     */
    public function confirmGet(){
        $checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->confirmGetgoods();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }

    /**
     * 取消订单
     *
     */
    public function cancelOrder(){
        $checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->cancel_order();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     *删除积分订单
     *
     */
    public function delOrder(){
        $checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getDelOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     *积分订单再次购买
     *
     */
    public function orderNewBuy(){
        $checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getNewBuyOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}