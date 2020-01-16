<?php
declare(strict_types=1);
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\OrderGoodsLogic;
use Think\SessionGet;

/**
 * 订单控制器
 * @author Administrator
 */
class OrderController
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
        
        $this->logic = new OrderLogic($args);
    }

    /**
     * 获取我的积分兑换商品
     */
    public function myConfirm() :void
    {
        $ret = $this->logic->getMyConfirm();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    /**
     * 取消订单
     */
    public function cancelOrder() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->cancelOrder();
		
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        //修改订单商品
        $orderGoodsLogic = new OrderGoodsLogic($this->args);
        
        $status = $orderGoodsLogic->cancelOrderGoods();
        
        $this->objController->promptPjax($status,$orderGoodsLogic->getErrorMessage());
        
        $this->objController->ajaxReturnData('');
    }
    /**
     * 获取积分兑换订单详情
     *
     */
    public function integralGoodsInfo() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->getConfirmInfo();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);
    }
    
    /**
     * 订单 确认收货
     */
    public function confirmGet() :void
    {

        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());

        $ret = $this->logic->confirmGetgoods();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
        //修改订单商品
        $orderLogic = new OrderGoodsLogic($this->args);
        
        $status = $orderLogic->getResult();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData($status);
    }
   

    /**
     * 支付收银台（获取订单金额）
     */
    public function getOrderPriceById() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->logicGetOrderPriceById();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret);

    }
    //我的订单--全部订单
    public function orderListAll() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['status'] = 0;       
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待付款
    public function pendingPayment() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 0; 
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待发货
    public function pendingDelivery() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 1;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待收货
    public function goodsToBeReceived() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 3;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待评价
    public function toBeEvaluated() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 4;
        $where['comment_status'] = 0;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--已取消
    public function haveBeenCancelled() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = "-1";
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--已完成
    public function completed() :void
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 4;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--订单详情 
    public function orderDetail() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderDetails();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //去评价获取订单信息
    public function orderInfoCommont() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderInfo();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //申请售后获取订单信息 
    public function orderInfoReturnGoods() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByReturn(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderInfoByReturn();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }

    /**
     * 确认订单是否已完成支付
     */
    public function getOrderStatus() :void
    {    
    	$this->objController->promptPjax(IS_POST,'不允许请求');
    	
    	$checkObj = new CheckParam($this->logic->getValidateByOrderId(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$status = $this->logic->checkOrderStatus();
    	
    	$this->objController->promptPjax($status, $this->logic->getErrorMessage());
    	
    	$this->objController->ajaxReturnData($status);
    }
    
    //删除订单--修改状态
    public function orderDel() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderDel();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}