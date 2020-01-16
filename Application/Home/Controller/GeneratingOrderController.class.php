<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
declare(strict_types = 1);
namespace Home\Controller;

use Common\Logic\PanicLogic;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderLogic;
use Validate\CheckParam;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\OrderInvoiceLogic;
use Common\Logic\CouponListLogic;
use Common\SessionParse\SessionManager;
use Common\Logic\GoodsCartLogic;

/**
 * 生成订单
 * @author Administrator
 */
class GeneratingOrderController
{
	/**
	 * 支付数据
	 * @var array
	 */
	private $payData = [];
	
	/**
	 * 订单商品逻辑
	 */
	private $orderGoodsLogic;
	
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
		
		$this->logic = new OrderLogic($args);
	}
	
	/**
	 * 配件立即购买
	 */
	public function partsBuyNow() :void
	{
		$checkObj = new CheckParam($this->logic->getMessageValidateByParts(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->commonMultiCommodityGeneratedOrder();
		
		$this->logic->submitTranstion();
		
		//支付数据
		SessionManager::SET_ORDER_DATA($this->payData);
		
		// 普通订单 0 套餐订单 1
		SessionManager::SET_ORDER_TYPE_BY_USER(0);
		
		SessionManager::REMOVE_GOODS_DATA_SOURCE();
		
		$this->objController->ajaxReturnData('');
	}
	
	/***
	 * 在商品列表直接购买商品->直接下单
	 */
	public function orderBeginFromGood() :void
	{
		$checkObj = new CheckParam($this->logic->getValidateByGoods(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
		$ret = $this->logic->placeTheOrder();
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		$orderGoodsLogic = new OrderGoodsLogic(['order_id' => $this->logic->getPlaceTheOrderId()]);
		
		$status = $orderGoodsLogic->placeTheOrderGoods();
		
		$this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
		
		//发票更新
		$invoiceData = $this->logic->getInvoiceData();
		
		$orderInvoiceLogic = new OrderInvoiceLogic($invoiceData);
		
		$status = $orderInvoiceLogic->updateInvoiceByPlaceTheOrder();
		
		$this->objController->promptPjax($status, $orderInvoiceLogic->getErrorMessage());
		
		//支付的时候需要的订单数据
		$payData = $this->logic->getPayData();
		//是否更新优惠券
		$couponListLogic = new CouponListLogic($payData);
		
		$status = $couponListLogic->couponParse();
		
		$this->objController->promptPjax($status, $couponListLogic->getErrorMessage());
		
		$this->logic->submitTranstion();
		
		SessionManager::SET_ORDER_DATA($payData);
		
		SessionManager::SET_ORDER_TYPE_BY_USER(0);
		
		SessionManager::REMOVE_GOODS_DATA_SOURCE();
		
		$this->objController->ajaxReturnData($ret);
		
	}
	
	/**
	 * 购物车->去结算
	 */
	public function buildOrderByCart() :void
	{
		//检测传值                  //检测方法
		$checkObj = new CheckParam($this->logic->getCartIdInfo(), $this->args);
		
		$status = $checkObj->checkParam();
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());
		
		$this->commonMultiCommodityGeneratedOrder();
		
		//删除购物车
		$cartLogic = new GoodsCartLogic($this->orderGoodsLogic->getCartId());
		
		$status = $cartLogic->deleteCartByTrans();
		
		$this->objController->promptPjax($status, $cartLogic->getErrorMessage());
		
		//支付数据
		SessionManager::SET_ORDER_DATA($this->payData);
		
		// 普通订单 0 套餐订单 1
		SessionManager::SET_ORDER_TYPE_BY_USER(0);
		
		SessionManager::REMOVE_GOODS_DATA_SOURCE();
		
		$this->objController->ajaxReturnData('');
	}
	
	/**
	 * 订单公共数据处理
	 */
	private function commonMultiCommodityGeneratedOrder() :void
	{
		$orderData = $this->logic->multiCommodityGeneratedOrder();
		
		$this->objController->promptPjax($orderData, $this->logic->getErrorMessage());
		
		$this->orderGoodsLogic = new OrderGoodsLogic($orderData);
		
		$status = $this->orderGoodsLogic->buildOrderGoods();
		
		$this->objController->promptPjax($status, $this->orderGoodsLogic->getErrorMessage());
		
		//发票更新
		$invoiceData = $this->logic->getInvoiceData();
		
		$orderInvoiceLogic = new OrderInvoiceLogic($invoiceData);
		
		$status = $orderInvoiceLogic->updateInvoice();
		
		$this->objController->promptPjax($status, $orderInvoiceLogic->getErrorMessage());
		
		//支付的时候需要的订单数据
		$payData = $this->logic->getPayData();
		//是否更新优惠券
		$couponListLogic = new CouponListLogic($payData);
		
		$status = $couponListLogic->couponParse();
		
		$this->objController->promptPjax($status, $couponListLogic->getErrorMessage());
		
		$this->payData = $payData;
	}

    /***
     * 在商品列表直接购买商品->直接下单
     */
    public function orderBeginFromPanicGood() :void
    {
        $checkObj = new CheckParam($this->logic->getValidateByGoods(), $this->args);

        $status = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        $ret = $this->logic->placeTheOrder();

        $this->objController->promptPjax($ret, $this->logic->getErrorMessage());

        $orderGoodsLogic = new OrderGoodsLogic(['order_id' => $this->logic->getPlaceTheOrderId()]);

        $status = $orderGoodsLogic->placeTheOrderGoods();

        $this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
        //修改抢购表商品已购买数量
        $panicLogic = new PanicLogic($this->args);
        $res = $panicLogic->changePanicNum();

        $this->objController->promptPjax($res, $panicLogic->getErrorMessage());
        //发票更新
        $invoiceData = $this->logic->getInvoiceData();

        $orderInvoiceLogic = new OrderInvoiceLogic($invoiceData);

        $status = $orderInvoiceLogic->updateInvoiceByPlaceTheOrder();

        $this->objController->promptPjax($status, $orderInvoiceLogic->getErrorMessage());

        //支付的时候需要的订单数据
        $payData = $this->logic->getPayData();
        //是否更新优惠券
        $couponListLogic = new CouponListLogic($payData);

        $status = $couponListLogic->couponParse();

        $this->objController->promptPjax($status, $couponListLogic->getErrorMessage());

        $this->logic->submitTranstion();

        SessionManager::SET_ORDER_DATA($payData);

        SessionManager::SET_ORDER_TYPE_BY_USER(0);

        SessionManager::REMOVE_GOODS_DATA_SOURCE();

        $this->objController->ajaxReturnData($ret);

    }
	
	public function __destruct()
	{
		unset($this->args, $this->logic, $this->orderGoodsLogic, $this->objController);
	}
}