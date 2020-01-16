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
declare(strict_types = 1);
namespace Common\TraitClass;

use Think\Hook;
use Common\Logic\OrderLogic;
use Common\Logic\IntegralUseLogic;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\StoreLogic;
use Think\Log;
use Common\Logic\StoreMemberLevelLogic;
use Common\Logic\StoreMemberLogic;
use Common\Tool\Tool;
use Common\SessionParse\SessionManager;
use Common\Logic\UserDataLogic;
use Think\SessionGet;


/**
 * 支付通知
 * @author Administrator
 */
trait OrderNoticeTrait
{
	/**
	 * 支付宝流水号
	 * @var int
	 */
	private $payIntegral;
	protected $tradeNo = 0;
	
	private $payReturnData = [];
	
	private $errorMessage;
	
	/**
	 * 订单类型
	 * @var integer
	 */
	protected $orderType;
	
	// 0 普通订单 1优惠套餐订单 2 积分订单
	private $orderParseClass = [
			'Common\\Logic\\OrderLogic',
			'Common\\Logic\\OrderPackageLogic',
			'Common\\Logic\\OrderIntegralLogic'
	];
	
	/**
	 * 订单商品处理
	 * @var array
	 */
	private $orderGoodsParseClass = [
			'Common\\Logic\\OrderGoodsLogic',
			'Common\\Logic\\OrderPackageGoodsLogic',
			'Common\\Logic\\OrderIntegralGoodsLogic'
	];
	
	private function orderWxNotice()
	{
		
		$payConf = SessionGet::getInstance('pay_config_by_user')->get();
		
		$day = date('y_m_d');
		
		if ( empty($payConf) ) {
			$this->errorMessage = '参数错误';
			
			Log::write('订单处理--没有对应的支付数据', Log::ERR, '', './Log/order/order_pay_'.$day.'.txt');
			return false;
		}
		
		if (!isset($this->orderParseClass[$this->orderType])) {
			$this->errorMessage = '没有对应的订单类型';
			Log::write('订单处理--没有对应的订单类型', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
			return false;
		}
		
//		$orderData = SessionManager::GET_ORDER_DATA();
        $orderId = $this->callBackOrderId;
		//订单操作
		{
			try {
				//订单类型序号
				$class = $this->orderParseClass[$this->orderType];
				
				$refClass = new \ReflectionClass($class);
				
//				$orderId = implode(',', array_column($orderData, 'order_id'));
				
				$orderLogic = $refClass->newInstance(['id' => $orderId, 'pay_conf' => $payConf]);
				
				$status = $refClass->getMethod('paySuccessEditStatus')->invoke($orderLogic);
				
				$time= date('Y-m-d H:i:s', time());
				
				Log::write('修改订单状态'.$time.' -- '.$status.'--订单--id--'.$orderId, Log::INFO, '', './Log/order/order_status_'.$day.'.txt');
				
			} catch (\Exception $e) {
				
				Log::write('订单处理--'.$e->getMessage(), Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
				
				return false;
			}
			
			
			if (empty($status)) {
				
				Log::write('订单处理--修改订单状态失败或者已修改成功', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
				$this->errorMessage='修改订单状态失败或者已修改成功';
				return false;
			}
			
			try {
				
				$refOrderGoodsClass =$this->orderGoodsParseClass[$this->orderType];
				
				$refOrderObj = new \ReflectionClass($refOrderGoodsClass);
				
				$oderGoodsLogic = $refOrderObj->newInstance(['order_id' => $orderId]);
				
				$status = $refOrderObj->getMethod('updateOrderGoodsStatus')->invoke($oderGoodsLogic);
				
				Log::write('订单状态'.$time.' -- '.$status.'--'.$refOrderGoodsClass, Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');
				
			} catch (\Exception $e) {
				Log::write('订单状态--'.$time.' -- '.$e->getMessage(), Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');
				return false;
			}
			
			if (empty($status)) {
				$this->errorMessage = '订单商品状态操作错误';
				return false;
			}
		}
		
		//事件监听 回调数据
		$param = [
				'order_id' => $orderId,
				'trade_no' => $this->tradeNo,
				'wx_order_id' => $orderId,
				'type'        => $this->orderType
		];
		
		Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::INFO, '', './Log/order/aplipaySerial_'.$day.'.txt');
		
		
		Hook::listen('aplipaySerial', $param);
		
		if (empty($param)) {
			
			Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::ERR, '', './Log/order/aplipaySerial_'.$day.'.txt');
			
			$this->errorMessage = '生成标志失败';
			return false;
		}
		
		
		//积分操作
		{
			if ($this->orderType !=2) {
				
				$addIntegral = new IntegralUseLogic($orderId);
				
				if(($status = $addIntegral->addIntegrals((int)$this->payIntegral)) === false){
					
					Log::write('积分操作错误'.$time.' -- ', Log::ERR, '', './Log/order/order_integral_'.$day.'.txt');
					
					$this->errorMessage = '积分操作错误';
					return false;
				}
				
				$totalIntegral = $addIntegral->getTotailIntegral();
				
				
				if ($totalIntegral !== 0) {
					
					//统计积分加入用户数据表，防止积分支付频繁读取
					$userDataLogic = new UserDataLogic(['total_integral' => $totalIntegral]);
					
					$sataus = $userDataLogic->updateIntegralByAdd();
					
					if ($sataus === false) {
						
						Log::write('订单状态'.$time.' -- '.$status.'-积分-'.$totalIntegral, Log::INFO, '', './Log/order/order_integral_'.$day.'.txt');
						return false;
					}
				}
			}
			
		}
		
//		$orderGoodsData = SessionManager::GET_ORDER_GOODS_DATA();
		
		// 减库存
		$goodsLogic = new GoodsLogic($orderId);
		
		$status = $goodsLogic->delStocks();
		
		Log::write('减少库存'. $time.' -- '.$status, Log::INFO, '', './Log/order/order_goods_stock_'.$day.'.txt');
		
		if (empty($status)) {
			$this->errorMessage = $goodsLogic->getErrorMessage();
			return false;
		}
		
		// 店铺增加销量
		$storeLogic = new StoreLogic($orderId);
		
		$status = $storeLogic->updateSales();
		
		if (empty($status)) {
			$this->errorMessage = $storeLogic->getErrorMessage();
			
			Log::write('店铺增加销量'.$time.' -- '.$this->errorMessage, Log::ERR, '', './Log/order/store_sale_'.$day.'.txt');
			
			return false;
		}
		
		//验证是否在此店铺添加该会员
		$storeMemberLevelLogic = new StoreMemberLevelLogic($orderId, 'store_id');
		
		Tool::connect('parseString');
		
		$result = $storeMemberLevelLogic->getResults();
		
		//不需要添加
		if ($storeMemberLevelLogic->getIsAddMember() === false) {
			return true;
		}
		
		//增加积分(获取用户编号)
		$userId =SessionGet::getInstance('user_id')->get();
		
		
		if (!is_numeric($userId)) {
			$this->errorMessage = '获取用户信息失败';
			Log::write('获取用户信息失败'.$time.' -- ', Log::ERR, '', './Log/order/order_user_'.$day.'.txt');
			return false;
		}
		
		//要添加
		$storeMemberLogic = new StoreMemberLogic($result, '', $userId);
		
		$sataus = $storeMemberLogic->getResult();
		
		if ($sataus === false) {
			Log::write('店铺增加会员--'.$time.' -- 处理失败', Log::ERR, '', './Log/order/store_member_'.$day.'.txt');
			
			return false;
		}
		
		//清空全部与订单相关的session
		SessionManager::REMOVE_ALL();
		return true;
	}
    private function orderNotice()
    {

        $payConf = SessionGet::getInstance('pay_config_by_user')->get();

        $day = date('y_m_d');

        if ( empty($payConf) ) {
            $this->errorMessage = '参数错误';

            Log::write('订单处理--没有对应的支付数据', Log::ERR, '', './Log/order/order_pay_'.$day.'.txt');
            return false;
        }

        if (!isset($this->orderParseClass[$this->orderType])) {
            $this->errorMessage = '没有对应的订单类型';
            Log::write('订单处理--没有对应的订单类型', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
            return false;
        }

        $orderData = SessionManager::GET_ORDER_DATA();

        //订单操作
        {
            try {
                //订单类型序号
                $class = $this->orderParseClass[$this->orderType];

                $refClass = new \ReflectionClass($class);

                $orderId = implode(',', array_column($orderData, 'order_id'));

                $orderLogic = $refClass->newInstance(['id' => $orderId, 'pay_conf' => $payConf]);

                $status = $refClass->getMethod('paySuccessEditStatus')->invoke($orderLogic);

                $time= date('Y-m-d H:i:s', time());

                Log::write('修改订单状态'.$time.' -- '.$status.'--订单--id--'.$orderId, Log::INFO, '', './Log/order/order_status_'.$day.'.txt');

            } catch (\Exception $e) {

                Log::write('订单处理--'.$e->getMessage(), Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');

                return false;
            }


            if (empty($status)) {

                Log::write('订单处理--修改订单状态失败或者已修改成功', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
                $this->errorMessage='修改订单状态失败或者已修改成功';
                return false;
            }

            try {

                $refOrderGoodsClass =$this->orderGoodsParseClass[$this->orderType];

                $refOrderObj = new \ReflectionClass($refOrderGoodsClass);

                $oderGoodsLogic = $refOrderObj->newInstance(['order_id' => $orderId]);

                $status = $refOrderObj->getMethod('updateOrderGoodsStatus')->invoke($oderGoodsLogic);

                Log::write('订单状态'.$time.' -- '.$status.'--'.$refOrderGoodsClass, Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');

            } catch (\Exception $e) {
                Log::write('订单状态--'.$time.' -- '.$e->getMessage(), Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');
                return false;
            }

            if (empty($status)) {
                $this->errorMessage = '订单商品状态操作错误';
                return false;
            }
        }

        //事件监听 回调数据
        $param = [
            'order_id' => $orderId,
            'trade_no' => $this->tradeNo,
            'wx_order_id' => $orderId,
            'type'        => $this->orderType
        ];

        Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::INFO, '', './Log/order/aplipaySerial_'.$day.'.txt');


        Hook::listen('aplipaySerial', $param);

        if (empty($param)) {

            Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::ERR, '', './Log/order/aplipaySerial_'.$day.'.txt');

            $this->errorMessage = '生成标志失败';
            return false;
        }


        //积分操作
        {
            if ($this->orderType !=2) {

                $addIntegral = new IntegralUseLogic($orderData);

                if(($status = $addIntegral->addIntegral((int)$this->payIntegral)) === false){

                    Log::write('积分操作错误'.$time.' -- ', Log::ERR, '', './Log/order/order_integral_'.$day.'.txt');

                    $this->errorMessage = '积分操作错误';
                    return false;
                }

                $totalIntegral = $addIntegral->getTotailIntegral();


                if ($totalIntegral !== 0) {

                    //统计积分加入用户数据表，防止积分支付频繁读取
                    $userDataLogic = new UserDataLogic(['total_integral' => $totalIntegral]);

                    $sataus = $userDataLogic->updateIntegralByAdd();

                    if ($sataus === false) {

                        Log::write('订单状态'.$time.' -- '.$status.'-积分-'.$totalIntegral, Log::INFO, '', './Log/order/order_integral_'.$day.'.txt');
                        return false;
                    }
                }
            }

        }

        $orderGoodsData = SessionManager::GET_ORDER_GOODS_DATA();

        // 减库存
        $goodsLogic = new GoodsLogic($orderGoodsData);

        $status = $goodsLogic->delStock();

        Log::write('减少库存'. $time.' -- '.$status, Log::INFO, '', './Log/order/order_goods_stock_'.$day.'.txt');

        if (empty($status)) {
            $this->errorMessage = $goodsLogic->getErrorMessage();
            return false;
        }

        // 店铺增加销量
        $storeLogic = new StoreLogic($orderData);

        $status = $storeLogic->updateSale();

        if (empty($status)) {
            $this->errorMessage = $storeLogic->getErrorMessage();

            Log::write('店铺增加销量'.$time.' -- '.$this->errorMessage, Log::ERR, '', './Log/order/store_sale_'.$day.'.txt');

            return false;
        }

        //验证是否在此店铺添加该会员
        $storeMemberLevelLogic = new StoreMemberLevelLogic($orderData, 'store_id');

        Tool::connect('parseString');

        $result = $storeMemberLevelLogic->getResult();

        //不需要添加
        if ($storeMemberLevelLogic->getIsAddMember() === false) {
            return true;
        }

        //增加积分(获取用户编号)
        $userId =SessionGet::getInstance('user_id')->get();


        if (!is_numeric($userId)) {
            $this->errorMessage = '获取用户信息失败';
            Log::write('获取用户信息失败'.$time.' -- ', Log::ERR, '', './Log/order/order_user_'.$day.'.txt');
            return false;
        }

        //要添加
        $storeMemberLogic = new StoreMemberLogic($result, '', $userId);

        $sataus = $storeMemberLogic->getResult();

        if ($sataus === false) {
            Log::write('店铺增加会员--'.$time.' -- 处理失败', Log::ERR, '', './Log/order/store_member_'.$day.'.txt');

            return false;
        }

        //清空全部与订单相关的session
        SessionManager::REMOVE_ALL();
        return true;
    }
    private function orderBalanceNotice()
    {

        $payConf = SessionGet::getInstance('pay_config_by_user')->get();

        $day = date('y_m_d');

        if ( empty($payConf) ) {
            $this->errorMessage = '参数错误';

            Log::write('订单处理--没有对应的支付数据', Log::ERR, '', './Log/order/order_pay_'.$day.'.txt');
            return false;
        }

        if (!isset($this->orderParseClass[$this->orderType])) {
            $this->errorMessage = '没有对应的订单类型';
            Log::write('订单处理--没有对应的订单类型', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
            return false;
        }

        $orderData = SessionManager::GET_ORDER_DATA();

        //订单操作
        {
            try {
                //订单类型序号
                $class = $this->orderParseClass[$this->orderType];

                $refClass = new \ReflectionClass($class);

                $orderId = implode(',', array_column($orderData, 'order_id'));

                $orderLogic = $refClass->newInstance(['id' => $orderId, 'pay_conf' => $payConf]);

                $status = $refClass->getMethod('paySuccessEditStatus')->invoke($orderLogic);

                $time= date('Y-m-d H:i:s', time());

                Log::write('修改订单状态'.$time.' -- '.$status.'--订单--id--'.$orderId, Log::INFO, '', './Log/order/order_status_'.$day.'.txt');

            } catch (\Exception $e) {

                Log::write('订单处理--'.$e->getMessage(), Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');

                return false;
            }


            if (empty($status)) {

                Log::write('订单处理--修改订单状态失败或者已修改成功', Log::ERR, '', './Log/order/order_parse_'.$day.'.txt');
                $this->errorMessage='修改订单状态失败或者已修改成功';
                return false;
            }

            try {

                $refOrderGoodsClass =$this->orderGoodsParseClass[$this->orderType];

                $refOrderObj = new \ReflectionClass($refOrderGoodsClass);

                $oderGoodsLogic = $refOrderObj->newInstance(['order_id' => $orderId]);

                $status = $refOrderObj->getMethod('updateOrderGoodsStatus')->invoke($oderGoodsLogic);

                Log::write('订单状态'.$time.' -- '.$status.'--'.$refOrderGoodsClass, Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');

            } catch (\Exception $e) {
                Log::write('订单状态--'.$time.' -- '.$e->getMessage(), Log::INFO, '', './Log/order/order_goods_status_'.$day.'.txt');
                return false;
            }

            if (empty($status)) {
                $this->errorMessage = '订单商品状态操作错误';
                return false;
            }
        }

        //事件监听 回调数据
        $param = [
            'order_id' => $orderId,
            'trade_no' => $this->tradeNo,
            'wx_order_id' => $orderId,
            'type'        => $this->orderType
        ];

        Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::INFO, '', './Log/order/aplipaySerial_'.$day.'.txt');


        Hook::listen('aplipaySerial', $param);

        if (empty($param)) {

            Log::write('第三方支付配置'.$time.' -- '.print_r($param, true), Log::ERR, '', './Log/order/aplipaySerial_'.$day.'.txt');

            $this->errorMessage = '生成标志失败';
            return false;
        }


        //积分操作
        {
            if ($this->orderType !=2) {

                $addIntegral = new IntegralUseLogic($orderData);

                if(($status = $addIntegral->addIntegral((int)$this->payIntegral)) === false){

                    Log::write('积分操作错误'.$time.' -- ', Log::ERR, '', './Log/order/order_integral_'.$day.'.txt');

                    $this->errorMessage = '积分操作错误';
                    return false;
                }

                $totalIntegral = $addIntegral->getTotailIntegral();


                if ($totalIntegral !== 0) {

                    //统计积分加入用户数据表，防止积分支付频繁读取
                    $userDataLogic = new UserDataLogic(['total_integral' => $totalIntegral]);

                    $sataus = $userDataLogic->updateIntegralByAdd();

                    if ($sataus === false) {

                        Log::write('订单状态'.$time.' -- '.$status.'-积分-'.$totalIntegral, Log::INFO, '', './Log/order/order_integral_'.$day.'.txt');
                        return false;
                    }
                }
            }

        }

        $orderGoodsData = SessionManager::GET_ORDER_GOODS_DATA();

        // 减库存
        $goodsLogic = new GoodsLogic($orderGoodsData);

        $status = $goodsLogic->delStock();

        Log::write('减少库存'. $time.' -- '.$status, Log::INFO, '', './Log/order/order_goods_stock_'.$day.'.txt');

        if (empty($status)) {
            $this->errorMessage = $goodsLogic->getErrorMessage();
            return false;
        }

        // 店铺增加销量
        $storeLogic = new StoreLogic($orderData);

        $status = $storeLogic->updateSale();

        if (empty($status)) {
            $this->errorMessage = $storeLogic->getErrorMessage();

            Log::write('店铺增加销量'.$time.' -- '.$this->errorMessage, Log::ERR, '', './Log/order/store_sale_'.$day.'.txt');

            return false;
        }

        //验证是否在此店铺添加该会员
        $storeMemberLevelLogic = new StoreMemberLevelLogic($orderData, 'store_id');

        Tool::connect('parseString');

        $result = $storeMemberLevelLogic->getResult();

        //不需要添加
        if ($storeMemberLevelLogic->getIsAddMember() === false) {
            return true;
        }

        //增加积分(获取用户编号)
        $userId =SessionGet::getInstance('user_id')->get();


        if (!is_numeric($userId)) {
            $this->errorMessage = '获取用户信息失败';
            Log::write('获取用户信息失败'.$time.' -- ', Log::ERR, '', './Log/order/order_user_'.$day.'.txt');
            return false;
        }

        //要添加
        $storeMemberLogic = new StoreMemberLogic($result, '', $userId);

        $sataus = $storeMemberLogic->getResult();

        if ($sataus === false) {
            Log::write('店铺增加会员--'.$time.' -- 处理失败', Log::ERR, '', './Log/order/store_member_'.$day.'.txt');

            return false;
        }

        //清空全部与订单相关的session
        SessionManager::REMOVE_ALL();
        return true;
    }
}