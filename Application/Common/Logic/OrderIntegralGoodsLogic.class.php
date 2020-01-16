<?php
namespace Common\Logic;

use Common\Model\OrderIntegralGoodsModel;
use Common\SessionParse\SessionManager;
use Think\SessionGet;

class OrderIntegralGoodsLogic extends AbstractGetDataLogic
{
	/**
	 * 返回客户端数据
	 * @var array
	 */
	private $clientDataReturn = [];
	
	/**
	 * 
	 */
	public function getClientDataReturn()
	{
		return $this->clientDataReturn;
	}
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new OrderIntegralGoodsModel();
		
	}
	
	
	/**
	 * 获取结果
	 */
	public function getResult()
	{
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return OrderIntegralGoodsModel::class;
	}
	
	/**
	 * add 积分商品
	 */
	public function addOrderIntegral()
	{
		if (empty($this->data)) {
			$this->modelObj->rollback();
			return false;
		}
		
		$status = $this->addData();
		
		if (!$this->traceStation($status)) {
			return false;
		}
		
		$this->modelObj->commit();
		
		
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
	 */
	protected function getParseResultByAdd() :array
	{
		
		$goods = SessionManager::GET_GOODS_DATA_SOURCE();
		
		$goodData = [];
		//创建订单成功以后 将订单商品信息加入订单商品表
		$goodData[OrderIntegralGoodsModel::$orderId_d] =  $this->data['integral_order_id'];
		$goodData[OrderIntegralGoodsModel::$goodsId_d] =  $goods['goods_id'];
		$goodData[OrderIntegralGoodsModel::$goodsNum_d] = $goods['goods_num'];
		$goodData[OrderIntegralGoodsModel::$integral_d] = $goods['every_integral'];
		$goodData[OrderIntegralGoodsModel::$money_d] = 	  $goods['goods_price'];
		$goodData[OrderIntegralGoodsModel::$status_d] = 0;
		$goodData[OrderIntegralGoodsModel::$storeId_d] = $goods['store_id'];
		$goodData[OrderIntegralGoodsModel::$comment_d] = 0;
        $goodData[OrderIntegralGoodsModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		$goodData[OrderIntegralGoodsModel::$freightId_d] = $goods['express_id'];
		
		
		return $goodData;
	}
	
	/**
	 * 更新订单商品状态
	 */
	public function updateOrderGoodsStatus ()
	{
		if ( empty($this->data['order_id'])) {
			$this->modelObj->rollback();
			return false;
		}
		
		$status = $this->modelObj->where(OrderIntegralGoodsModel::$orderId_d.' in (%s)', $this->data['order_id'])->save([
			OrderIntegralGoodsModel::$status_d => 1
		]);
		if (!$this->traceStation($status)) {
			return false;
		}
		return $status;
	}
}