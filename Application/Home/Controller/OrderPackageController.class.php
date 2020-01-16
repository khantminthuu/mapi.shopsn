<?php
namespace Home\Controller;

use Common\TraitClass\InitControllerTrait;
use Validate\CheckParam;
use Common\Logic\OrderPackageLogic;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\OrderPackageGoodsLogic;
use Common\Logic\OrderPackageInvoiceLogic;
use Common\Logic\CouponListLogic;
use Common\SessionParse\SessionManager;
use Common\Logic\GoodsPackageCartLogic;
use Think\SessionGet;
class OrderPackageController
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
        // $this->init();
        $this->_initUser();
        // session("user_id",3);
        $this->logic = new OrderPackageLogic($args);
    }
    /**
     * 生成订单--立即购买
     * @author
     */
    public function orderBegin() 
    {
        $this->buildOrderCompont();
      
        $status = $this->logic->submitTranstion();
        
        $this->objController->promptPjax($status, '生成订单失败');
        
        SessionManager::REMOVE_GOODS_DATA_SOURCE();
        
        $this->objController->ajaxReturnData($status);
    } 
    
    private function buildOrderCompont() :void
    {
    	
    	//检测传值                   //检测方法
    	$checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);
    	
    	$status = $checkObj->checkParam();
    	
    	$this->objController->promptPjax($status, $checkObj->getErrorMessage());
    	
    	$ret = $this->logic->orderBegin();
    	
    	$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
    	
    	$orderPackageGoodsLogic = new OrderPackageGoodsLogic($ret);
    	
    	$status = $orderPackageGoodsLogic->getResult();
    	
    	$this->objController->promptPjax($status, $orderPackageGoodsLogic->getErrorMessage());
    	//发票更新
    	$invoiceData = $this->logic->getInvoiceData();
    	
    	$orderInvoiceLogic = new OrderPackageInvoiceLogic($invoiceData);
    	
    	$status = $orderInvoiceLogic->updateInvoice();
    	
    	$this->objController->promptPjax($status, $orderInvoiceLogic->getErrorMessage());
    	
    	//支付的时候需要的订单数据
    	$payData = $this->logic->getPayData();
    	//是否更新优惠券
    	$couponListLogic = new CouponListLogic($payData);
    	
    	$status = $couponListLogic->couponParse();
    	
    	$this->objController->promptPjax($status, $couponListLogic->getErrorMessage());
    	
    	SessionManager::SET_ORDER_DATA($payData);
    	
    	// 普通订单 0 套餐订单 1
    	SessionManager::SET_ORDER_TYPE_BY_USER(1);
    	
    }
    
    /**
     * 生成订单--购物车购买
     * @author
     */
    public function orderBeginByCart() 
    {
//         // $this->objController->promptPjax($_SESSION['order_status_freight'], '运费错误');
        
//         //检测传值                   //检测方法
//         $checkObj = new CheckParam($this->logic->getValidateByLogin(), $this->args);

//         $status = $checkObj->checkParam();

//         $this->objController->promptPjax($status, $checkObj->getErrorMessage());

//         $ret = $this->logic->orderBeginByCart();

//         $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
//         // $_SESSION['order_status_freight'] = false;

    	$this->buildOrderCompont();
    	
    	
    	$packageCartLogic = new GoodsPackageCartLogic();
    	
    	
    	$status = $packageCartLogic->deletePackageCart();
    	
    	$this->objController->promptPjax($status, $packageCartLogic->getErrorMessage());
    	
    	SessionManager::REMOVE_GOODS_DATA_SOURCE();
    	
    	$this->objController->ajaxReturnData($status);
    	
    } 

    //我的订单--全部订单
    public function orderListAll(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['status'] = 0;       
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待付款
    public function pendingPayment(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 0; 
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待发货
    public function pendingDelivery(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 1;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待收货
    public function goodsToBeReceived(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 3;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--待评价
    public function toBeEvaluated(){
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
    public function haveBeenCancelled(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = "-1";
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--已完成
    public function completed(){
    	$userId = SessionGet::getInstance('user_id')->get();
    	$where['user_id'] = $userId; 
        $where['order_status'] = 4;
        $where['status'] = 0;
        $ret = $this->logic->getOrder($where);
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--订单详情 
    public function orderDetail(){
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderDetails();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //去评价获取订单信息
    public function orderInfoCommont(){
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderInfo();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //申请售后获取订单信息
    public function orderInfoReturnGoods(){
        $checkObj = new CheckParam($this->logic->getValidateByReturn(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderInfoByReturn();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //删除订单--修改状态
    public function orderDel(){
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getOrderDel();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    /**
     * 取消订单
     *
     */
    public function cancelOrder(){
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->cancel_order();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
    //我的订单--订单再次购买 
    public function buyAgain(){
        $checkObj = new CheckParam($this->logic->getValidateByOrder(), $this->args);
        $status = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        $ret = $this->logic->getBuyAgain();
        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData($ret['data'],$ret['status'],$ret['message']);
    }
}