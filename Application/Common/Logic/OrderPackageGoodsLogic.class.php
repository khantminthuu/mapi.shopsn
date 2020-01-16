<?php
namespace Common\Logic;

use Common\Model\OrderPackageGoodsModel;
use Think\SessionGet;
use Common\SessionParse\SessionManager;

/**
 * 优惠套餐逻辑处理类
 * @author Administrator
 *
 */
class OrderPackageGoodsLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		
		$this->splitKey = $split;
		
		$this->modelObj = new OrderPackageGoodsModel();
		
	}
	
	/**
	 * 获取结果（订单商品）
	 */
	public function getResult()
	{
		
		$status = $this->addAll();
		
		if (!$this->traceStation($status)) {
			return false;
		}
		
		$this->modelObj->commit();
			
		return true;
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAddAll()
	 */
	protected function getParseResultByAddAll() :array
	{
		$packageSub = SessionManager::GET_GOODS_DATA_SOURCE();
		$addData = [];
		
		$i = 0;
		$time = time();
		
		
		$orderIdArray = $this->data;
		
		$orderGoodsData = [];
		
		foreach ($packageSub as $key =>  $value) {
			
			$addData[$i] = [];
			
			$addData[$i][OrderPackageGoodsModel::$orderId_d] = $orderIdArray[$value['store_id']]['order_id'];
			$addData[$i][OrderPackageGoodsModel::$packageId_d] = $value['package_id'];
			$addData[$i][OrderPackageGoodsModel::$packageNum_d] = $value['goods_num'];
			$addData[$i][OrderPackageGoodsModel::$packageTotal_d] = $value['package_total'];
			$addData[$i][OrderPackageGoodsModel::$packageDiscount_d] = $value['package_discount'];
			$addData[$i][OrderPackageGoodsModel::$goodsId_d] = $value['goods_id'];
			$addData[$i][OrderPackageGoodsModel::$goodsDiscount_d] = $value['goods_price'];
			$addData[$i][OrderPackageGoodsModel::$status_d] = '0';
			$addData[$i][OrderPackageGoodsModel::$createTime_d] = $time;
            $addData[$i][OrderPackageGoodsModel::$userId_d] = SessionGet::getInstance('user_id')->get();
			$addData[$i][OrderPackageGoodsModel::$updateTime_d] = $time;
			$addData[$i][OrderPackageGoodsModel::$freightId_d] = $value ['express_id'];
			$addData[$i][OrderPackageGoodsModel::$storeId_d] = $value['store_id'];
			
			$i++;
		}
		
		return $addData;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return OrderPackageGoodsModel::class;
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
		
		$status = $this->modelObj->where(OrderPackageGoodsModel::$orderId_d.' in (%s)', $this->data['order_id'])->save([
			OrderPackageGoodsModel::$status_d => 1
		]);
		if (!$this->traceStation($status)) {
			return false;
		}
		return $status;
	}
	
}