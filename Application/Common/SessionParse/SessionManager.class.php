<?php
declare(strict_types=1);
namespace Common\SessionParse;

use Think\SessionGet;

/**
 * 订单所需session处理（方便统一管理订单所需session）
 * @author Administrator
 */
class SessionManager
{
	/**
	 * 优惠券抵扣信息（商品总金额）
	 * @var string
	 */
	const OWN_MY_COUPON = 'own_my_coupon';
	
	/**
	 * 获取惠券抵扣金额
	 */
	public static function GET_OWN_MY_COUPON()
	{
		return SessionGet::getInstance(static::OWN_MY_COUPON)->get();
	}
	
	/**
	 * 清理惠券抵扣金额
	 */
	public static function REMOVE_OWN_MY_COUPON() :void
	{
		SessionGet::getInstance(static::OWN_MY_COUPON)->delete();
	}
	
	/**
	 * 获取已使用的优惠券列表
	 * @var string
	 */
	const COUPON_LIST = 'coupon_list';
	
	/**
	 * 获取已使用的优惠券列表
	 */
	public static function GET_COUPON_LIST() 
	{
		return SessionGet::getInstance(static::COUPON_LIST)->get();
	}
	
	/**
	 * 设置已使用的优惠券列表
	 */
	public static function SET_COUPON_LIST(array $couponList) :void
	{
		SessionGet::getInstance(static::COUPON_LIST, $couponList)->set();
	}
	
	/**
	 * 清理获取已使用的优惠券列表
	 */
	public static function REMOVE_COUPON_LIST() :void
	{
		SessionGet::getInstance(static::COUPON_LIST)->delete();
	}
	
	/**
	 * 总价格
	 * @var string
	 */
	const COUPON_MONEY = 'coupon_money';
	
	/**
	 * 获取每个上铺总价格
	 */
	public static function GET_COUPON_MONEY() :array
	{
		return SessionGet::getInstance(static::COUPON_MONEY)->get();
	}
	
	/**
	 * 清理总价格
	 */
	public static function REMOVE_COUPON_MONEY() :void
	{
		SessionGet::getInstance(static::COUPON_MONEY)->delete();
	}
	
	/**
	 * 运费模板数据
	 * @var string
	 */
	const EXPRESS_ID = 'express_id';
	
	/**
	 * 清理运费模板数据
	 */
	public static function REMOVE_EXPRESS_ID() :void
	{
		SessionGet::getInstance(static::EXPRESS_ID)->delete();
	}
	
	/**
	 * 计算运费数据
	 * @var string
	 */
	const FREIGHT_MODE_DATA = 'freight_mode_data';
	
	public static function GETFREIGHT_MODE_DATA() :array
	{
		return SessionGet::getInstance(static::FREIGHT_MODE_DATA)->get();
	}
	
	/**
	 * 清理计算运费数据
	 */
	public static function REMOVE_FREIGHT_MODE_DATA() :void
	{
		SessionGet::getInstance(static::FREIGHT_MODE_DATA)->delete();
	}
	
	/**
	 * 查询优惠券使用（获取店铺id组成的字符串）
	 * @var string
	 */
	const  STORE_ID_BY_STATION = 'store_station';
	
	/**
	 * 获取查询优惠券使用（获取店铺id组成的字符串）
	 * @return string
	 */
	public static function GET_STORE_ID_BY_STATION() :string
	{
		return SessionGet::getInstance(static::STORE_ID_BY_STATION)->get();
	}
	
	/**
	 * 清理查询优惠券使用（获取店铺id组成的字符串）
	 */
	public static function REMOVE_STORE_ID_BY_STATION() :void
	{
		SessionGet::getInstance(static::STORE_ID_BY_STATION)->delete();
	}
	/**
	 * 可以使用的优惠券
	 * @var string
	 */
	const DO_USE = 'do_use';
	
	/**
	 * 清理可以使用的优惠券
	 */
	public static function REMOVE_DO_USE() :void
	{
		SessionGet::getInstance(static::DO_USE)->delete();
	}
	
	/**
	 * 清理可以使用的优惠券
	 */
	public static function SET_DO_USE(array $doUse) :void
	{
		SessionGet::getInstance(static::DO_USE, $doUse)->set();
	}
	
	/**
	 * 订单数据 支付请求 回调时用
	 * @var string
	 */
	const ORDER_DATA = 'order_data';
	
	/**
	 * 设置订单数据 支付请求 回调时用
	 */
	public static function SET_ORDER_DATA( array $data) :void
	{
		SessionGet::getInstance(static::ORDER_DATA, $data)->set();
	}
	
	
	/**
	 * 清理订单数据 支付请求 回调时用
	 */
	public static function REMOVE_ORDER_DATA() :void
	{
		SessionGet::getInstance(static::ORDER_DATA)->delete();
	}
	
	/**
	 * 清理订单数据 支付请求 回调时用
	 */
	public static function GET_ORDER_DATA() :array
	{
		return SessionGet::getInstance(static::ORDER_DATA)->get();
	}
	
	/**
	 * 订单商品数据
	 * @var string
	 */
	const ORDER_GOODS_DATA = 'order_goods_data';
	
	/**
	 * 获取订单商品数据
	 */
	public static function SET_ORDER_GOODS_DATA( array $orderGoodsData) :void
	{
		SessionGet::getInstance(static::ORDER_GOODS_DATA, $orderGoodsData)->set();
	}
	
	/**
	 * 获取订单商品数据
	 */
	public static function GET_ORDER_GOODS_DATA() :array
	{
		return SessionGet::getInstance(static::ORDER_GOODS_DATA)->get();
	}
	
	/**
	 * 订单商品临时数据
	 * @var string
	 */
	const GOODS_DATA_SOURCE = 'goods_data_source';
	
	/**
	 * 获取订单商品临时数据
	 */
	public static function GET_GOODS_DATA_SOURCE() :array
	{
		return SessionGet::getInstance(static::GOODS_DATA_SOURCE)->get();
	}
	
	/**
	 * 清理订单商品临时数据
	 */
	public static function REMOVE_GOODS_DATA_SOURCE() :void
	{
		SessionGet::getInstance(static::GOODS_DATA_SOURCE)->delete();
	}
	
	/**
	 * 支付时验证库存
	 */
	const GOODS_ID_BY_USER = 'goods_id_by_user';
	
	/**
	 * 清理支付时验证库存SESSION
	 */
	public static function REMOVE_GOODS_ID_BY_USER() :void
	{
		SessionGet::getInstance(static::GOODS_ID_BY_USER)->delete();
	}
	
	/**
	 * 各个店铺的运费
	 * @var string
	 */
	const FREIGHT_MONEY = 'freight_money';
	
	/**
	 * 设置运费
	 */
	public function SET_FREIGHT_MONRY(array $freight) :void
	{
		SessionGet::getInstance(static::FREIGHT_MONEY, $freight)->set();
	}
	/**
	 * 获取各个店铺的运费SESSION
	 */
	public static function GET_FREIGHT_MONEY() :array
	{
		return SessionGet::getInstance(static::FREIGHT_MONEY)->get();
	}
	
	/**
	 * 清理各个店铺的运费SESSION
	 */
	public static function REMOVE_FREIGHT_MONEY() :void
	{
		SessionGet::getInstance(static::FREIGHT_MONEY)->delete();
	}
	
	/**
	 * 商品支付类型（ 普通订单 0 套餐订单 1）
	 * @var string
	 */
	const ORDER_TYPE_BY_USER = 'order_type_by_user';
	
	/**
	 * 清理 商品支付类型SESSION（ 普通订单 0 套餐订单 1）
	 */
	public static function REMOVE_ORDER_TYPE_BY_USER() :void
	{
		SessionGet::getInstance(static::ORDER_TYPE_BY_USER)->delete();
	}
	
	/**
	 * 设置 商品支付类型SESSION（ 普通订单 0 套餐订单 1）
	 */
	public static function SET_ORDER_TYPE_BY_USER(int $orderType) :void
	{
		SessionGet::getInstance(static::ORDER_TYPE_BY_USER, $orderType)->set();
	}
	
	
	/**
	 * 购物车及其商品数据
	 * @var array
	 */
	private $data = [];
	
	/**
	 * 总金额
	 * @var float
	 */
	private $totalPrice = 0.0;
	
	/**
	 * 各个店铺的商品数量
	 * @var array
	 */
	private $totalNumber = [];
	
	/**
	 * 获取总数量
	 * @return int
	 */
	public function getTotalNumber():array
	{
		return $this->totalNumber;
	}
	
	/**
	 * 获取总金额
	 * @return number
	 */
	public function getTotalPrice():float
	{
		return $this->totalPrice;
	}
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * session处理
	 */
	protected function sessionInit() :void
	{
		
		
	}
	
	/**
	 * 处理session
	 */
	public function sessionParse() :void
	{
		$data = $this->data;
		
		$this->sessionInit();
		
		//价格
		$money = [ ];
		
		//运费模板信息
		$express = [];
		
		//店铺编号字符串
		$storeId = '';
		
		//商品编号及其购买数量
		$goodsCacheData = [];
		
		//重量
		$weight = [];
		
		//数量
		$number = [];
		
		$totalMoney = 0.0;
		
		$orderGoodsData = [];
		
		$goodsData = [];
		
		$i = 0;
		
		foreach ($data as $key => $value) {
			
			$money[$value['store_id']] += $value['price_member'] * $value['goods_num'];
			
			$express[$value['express_id']] = $value['store_id'];
			
			$storeId .= ','.$value['store_id'];
			
			$goodsCacheData[$value['goods_id']] += $value['goods_num'];
			
			$number [$value ['store_id']] += $value ['goods_num'];
			
			$weight [$value ['store_id']] += $value ['weight'] * $value ['goods_num'];
			
			$totalMoney += $value['price_member'] * $value['goods_num'];
			
			$orderGoodsData[$i] = [
					'goods_num' => $value['goods_num'],
					'goods_price' => $value['price_member'],
					'goods_id' => $value['goods_id']
			];
			
			$goodsData[$i] = [
					'id' => $value['id'],
					'goods_id' => $value['goods_id'],
					'goods_num' => $value['goods_num'],
					'goods_price' => $value['price_member'],
					'price_sum' => $value['price_member'] *$value['goods_num'],
					'store_id' => $value['store_id'],
					'express_id' => $value['express_id']
			];
			$i ++;
		}
		
		SessionGet::getInstance(static::GOODS_DATA_SOURCE,$goodsData)->set();
		
		SessionGet::getInstance(static::STORE_ID_BY_STATION, substr ( $storeId, 1 ))->set();
		
		SessionGet::getInstance(static::FREIGHT_MODE_DATA, [
				$number,
				$weight
		])->set();
		
		SessionGet::getInstance(static::EXPRESS_ID, $express)->set();
		
		SessionGet::getInstance(static::COUPON_MONEY, $money)->set();
		
		SessionGet::getInstance(static::GOODS_ID_BY_USER, $goodsCacheData)->set();
		
		SessionGet::getInstance(static::ORDER_GOODS_DATA, $orderGoodsData)->set();
		
		$this->totalPrice = $totalMoney;
		
		$this->totalNumber = $number;
	}
	
	/**
	 * 优惠套餐立即购买session 处理
	 */
	public function discountPackagePurchaseImmediately(array $parentPackageData) :void
	{
		$data = $this->data;
		$this->sessionInit();
		
		//价格
		$money = [ ];
		
		//运费模板信息
		$express = [];
		
		//店铺编号字符串
		$storeId = '';
		
		//商品编号及其购买数量
		$goodsCacheData = [];
		
		//重量
		$weight = [];
		
		//数量
		$number = [];
		
		$totalMoney = 0.0;
		
		$orderGoodsData = [];
		
		$goodsData = [];
		
		$i = 0;
		
		foreach ($data as $key => $value) {
			
			$money[$value['store_id']] += $value['goods_discount'] * $value['goods_num'];
			
			$express[$value['express_id']] = $value['store_id'];
			
			$storeId .= ','.$value['store_id'];
			
			$goodsCacheData[$value['goods_id']] += $value['goods_num'];
			
			$number [$value ['store_id']] += $value ['goods_num'];
			
			$weight [$value ['store_id']] += $value ['weight'] * $value ['goods_num'];
			
			$totalMoney += $value['goods_discount'] * $value['goods_num'];
			
			$orderGoodsData[$i] = [
					'goods_num' => $value['goods_num'],
					'goods_price' => $value['goods_discount'],
					'goods_id' => $value['goods_id']
			];
			
			$goodsData[$i] = [
					'id' => $parentPackageData[$value['package_id']]['id'],
					'goods_id' => $value['goods_id'],
					'goods_num' => $value['goods_num'],
					'goods_price' => $value['goods_discount'],
					'price_sum' => $value['goods_discount'] *$value['goods_num'],
					'store_id' => $value['store_id'],
					'express_id' => $value['express_id'],
					'package_total' => $parentPackageData[$value['package_id']]['total'],
					'package_discount' => $parentPackageData[$value['package_id']]['discount'],
					'package_id' => $value['package_id']
			];
			$i ++;
		}
		
		SessionGet::getInstance(static::GOODS_DATA_SOURCE, $goodsData)->set();
		
		SessionGet::getInstance(static::STORE_ID_BY_STATION, substr ( $storeId, 1 ))->set();
		
		SessionGet::getInstance(static::FREIGHT_MODE_DATA, [
				$number,
				$weight
		])->set();
		
		
		SessionGet::getInstance(static::EXPRESS_ID, $express)->set();
		
		SessionGet::getInstance(static::COUPON_MONEY, $money)->set();
		
		SessionGet::getInstance(static::GOODS_ID_BY_USER, $goodsCacheData)->set();
		
		SessionGet::getInstance(static::ORDER_GOODS_DATA, $orderGoodsData)->set();
		
		$this->totalPrice = $totalMoney;
		
		$this->totalNumber = $number;
	}
	
	/**
	 * 普通商品立即购买
	 */
	public  function sessionBuyNow() :void
	{
		$data = $this->data;
		
		$this->sessionInit();
		
		$money = [];
		
		$goodsMoney = $data['price_member'] * $data['goods_num'];
		
		$money[$data['store_id']] = $goodsMoney;
		//0:按件 1:按重量 2:按体积
		$weight = [];
		
		$weight[$data['store_id']] = $data['weight'];
		
		$number = [];
		
		$number[$data['store_id']] = $data['goods_num'];
		
		$stock = [];
		
		$stock[$data['id']] = $data['goods_num'];
		
		
		$goodsData = [];
		
		$goodsData['goods_id'] = $data['id'];
		
		$goodsData['goods_num'] = $data['goods_num'];
		
		$goodsData['goods_price'] = $data['price_member'];
		
		$goodsData['price_sum'] = $goodsMoney;
		
		$goodsData['store_id'] = $data['store_id'];
		
		$goodsData['express_id'] = $data['express_id'];
		
		SessionGet::getInstance(static::GOODS_DATA_SOURCE, $goodsData)->set();
		
		
		//订单相关
		SessionGet::getInstance(static::ORDER_GOODS_DATA, [[
				'goods_num' => $data['goods_num'],
				'goods_price' => $data['price_member'],
				'goods_id' => $data['id']
		]])->set();
		
		
		//优惠券用
		SessionGet::getInstance(static::STORE_ID_BY_STATION, $data['store_id'])->set();
		
		//运费用
		
		SessionGet::getInstance(static::FREIGHT_MODE_DATA, [
				$number,
				$weight
		])->set();
		
		
		SessionGet::getInstance(static::EXPRESS_ID, [
				$data['express_id'] => $data['store_id']
		])->set();
		
		
		SessionGet::getInstance(static::COUPON_MONEY, $money)->set();
		
		
		//支付时验证库存
		SessionGet::getInstance(static::GOODS_ID_BY_USER, $stock)->set();
		
		$this->totalPrice = $goodsMoney;
		
		$this->totalNumber = $number;
	}
	
	/**
	 * 积分商品立即购买
	 */
	public  function sessionBuyNowByIntegralGoods() :void
	{
		$data = $this->data;
		
		$this->sessionInit();
		
		$money = [];
		
		$goodsMoney = $data['price_member'] * $data['goods_num'];
		
		$money[$data['store_id']] = $goodsMoney;
		//0:按件 1:按重量 2:按体积
		$weight = [];
		
		$weight[$data['store_id']] = $data['weight'];
		
		$number = [];
		
		$number[$data['store_id']] = $data['goods_num'];
		
		$stock = [];
		
		$stock[$data['id']] = $data['goods_num'];
		
		
		$goodsData = [];
		
		$goodsData['goods_id'] = $data['id'];
		
		$goodsData['goods_num'] = $data['goods_num'];
		
		$goodsData['goods_price'] = $data['price_member'];
		
		$goodsData['price_sum'] = $goodsMoney;
		
		$goodsData['store_id'] = $data['store_id'];
		
		$goodsData['express_id'] = $data['express_id'];
		
		$goodsData['integral'] = $data['integral'] * $data['goods_num'];
		
		$goodsData['every_integral'] = $data['integral'];
		
		SessionGet::getInstance(static::GOODS_DATA_SOURCE, $goodsData)->set();
		
		//订单相关
		SessionGet::getInstance(static::ORDER_GOODS_DATA, [
				[
						'goods_num' => $data['goods_num'],
						'goods_price' => $data['price_member'],
						'goods_id' => $data['id']
				]
		])->set();
		
		//优惠券用
		SessionGet::getInstance(static::STORE_ID_BY_STATION, $data['store_id'])->set();
		
		//运费用
		SessionGet::getInstance(static::FREIGHT_MODE_DATA, [
				$number,
				$weight,
		])->set();
		
		SessionGet::getInstance(static::EXPRESS_ID,  [
				$data['express_id']  => $data['store_id']
		])->set();
		
		SessionGet::getInstance(static::COUPON_MONEY, $money)->set();
		
		//支付时验证库存
		SessionGet::getInstance(static::GOODS_ID_BY_USER, $stock)->set();
		
		$this->totalPrice = $goodsMoney;
		
		$this->totalNumber = $number;
	}
	
	/**
	 * 移除全部SESSION
	 */
	public static function REMOVE_ALL() :void
	{
		static::REMOVE_COUPON_MONEY();
		
		static::REMOVE_EXPRESS_ID();
		
		static::REMOVE_FREIGHT_MODE_DATA();
		
		static::REMOVE_FREIGHT_MONEY();
		
		static::REMOVE_GOODS_DATA_SOURCE();
		
		static::REMOVE_GOODS_ID_BY_USER();
		
		static::REMOVE_ORDER_DATA();
		
		static::REMOVE_OWN_MY_COUPON();
		
		static::REMOVE_STORE_ID_BY_STATION();
	}
	
	/**
	 * 析构方法
	 */
	public function __destruct()
	{
		unset($this->data, $this->totalNumber, $this->totalPrice);
	}
}