<?php
namespace Common\Logic;
use Common\Model\OrderGoodsModel;
use Think\Cache;
use Common\SessionParse\SessionManager;
use Think\SessionGet;
/**
 * 逻辑处理层
 *
 */
class OrderGoodsLogic extends AbstractGetDataLogic
{
	/**
	 * 购物车编号
	 * @var string
	 */
	private $cartId = [];
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new OrderGoodsModel();
    }
    
    /**
     * 
     * @return string
     */
    public function getCartId()
    {
    	return $this->cartId;
    }
    
    /**
     * 修改订单商品状态(确认收货)
     */
    public function getResult()
    {
    	$status = $this->modelObj
    		->where(OrderGoodsModel::$orderId_d.'= :o_id and '.OrderGoodsModel::$userId_d.'=:u_id and '.OrderGoodsModel::$status_d.'=:s')
    		->bind([
    		    ':o_id' => $this->data['id'], 
    		    ':u_id' => SessionGet::getInstance('user_id')->get(),
    		    ':s' => '3'
    		])
    		->save([
    			OrderGoodsModel::$status_d => 4,
    		]);
    	
    	if (!$this->traceStation($status)) {
    	    $this->errorMessage = '确认收货失败';
    		return false;
    	}
    	$this->modelObj->commit();
    	return true;
    		
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
    	return OrderGoodsModel::class;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    protected function hideenComment() :array
    {
        return [

        ];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
    }
    
    /**
     * 根据订单号获取商品数据
     */
    public function getGoodsDataByOrderId ()
    {
    	if ( empty($this->data['order_id']) || !is_numeric(str_replace(',', '', $this->data['order_id']))) {
    		$this->modelObj->rollback();
    		return [];
    	}
    	
    	$field = OrderGoodsModel::$orderId_d.','.OrderGoodsModel::$goodsNum_d.','.OrderGoodsModel::$goodsId_d.','.OrderGoodsModel::$goodsPrice_d.','.OrderGoodsModel::$userId_d.','.OrderGoodsModel::$storeId_d;//新增几个查询字段
    	
    	
    	$data = $this->modelObj->field($field)->where(OrderGoodsModel::$orderId_d.' in(%s)', $this->data['order_id'])->select();
    	
    	return $data;
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
    	
    	$status = $this->modelObj->where(OrderGoodsModel::$orderId_d.' in (%s)', $this->data['order_id'])->save([
    			OrderGoodsModel::$status_d => 1
    	]);
    	if (!$this->traceStation($status)) {
    		return false;
    	}
    	return $status;
    }
    
    /**
     * 根据订单号查询 商品数据
     */
    public function getGoodsByOrderId()
    {
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = 'user_'.SessionGet::getInstance('user_id')->get().'d_order'.'_d'.$this->data['id'];
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$field = OrderGoodsModel::$goodsId_d.','.OrderGoodsModel::$goodsNum_d.','.OrderGoodsModel::$storeId_d;
    	
    	$data = $this->modelObj
    		->where(OrderGoodsModel::$orderId_d.'=:id')
    		->bind([':id' => $this->data['id']])
    		->getField($field);
    	if (empty($data)) {
    		return [];
    	}
    	$cache->set($key, $data);
    	return $data;
    }
 	
    /**
     * 获取 商品编号字段
     */
    public function getSplitKeyByGoods()
    {
    	return OrderGoodsModel::$goodsId_d;
    }
    
    /**
     * 生成订单商品
     */
    public function buildOrderGoods()
    {
    	$status = $this->addAll();
    	
    	if (!$this->traceStation($status)) {
    		$this->errorMessage .= '、由于长时间没有购买，缓存时间过期，请刷新重新购买。生成订单失败';
    		return [];
    	}
    	return true;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAddAll()
     */
    protected function getParseResultByAddAll() :array
    {
    	$cartInfo = SessionManager::GET_GOODS_DATA_SOURCE();
    	
    	if (empty($cartInfo)) {
    		$this->modelObj->rollback();
    		return [];
    	}
    	
    	$orderId = $this->data;
    	
    	$orderGoods = [];
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$i = 0;
    	$cartIds = [];
    	foreach ($cartInfo as $key => $value) {
    		
    		$orderGoods[$i] = [];
    		
    		$orderGoods[$i][OrderGoodsModel::$orderId_d] = $orderId[$value['store_id']]['order_id'];
    		
    		$orderGoods[$i][OrderGoodsModel::$storeId_d] = $orderId[$value['store_id']]['store_id'];
    		
    		$orderGoods[$i][OrderGoodsModel::$goodsNum_d] = $value['goods_num'];
    		
    		$orderGoods[$i][OrderGoodsModel::$freightId_d] = $value ['express_id'];
    		
    		$orderGoods[$i][OrderGoodsModel::$goodsPrice_d] = $value ['goods_price'];
    		
    		$orderGoods[$i][OrderGoodsModel::$userId_d] = $userId;
    		
    		$orderGoods[$i][OrderGoodsModel::$goodsId_d] = $value['goods_id'];
    		
    		$cartIds[$i] = $value['id'];
    		
    		$i++;
    	}
    	$this->cartId = $cartIds;
    	
    	return $orderGoods;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd()
     */
    protected function getParseResultByAdd():array
    {
    	$goods = SessionManager::GET_GOODS_DATA_SOURCE();
    	
    	$orderGoods = [];
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$orderGoods[OrderGoodsModel::$orderId_d] = $this->data['order_id'];
    	
    	$orderGoods[OrderGoodsModel::$storeId_d] = $goods['store_id'];
    	
    	$orderGoods[OrderGoodsModel::$goodsNum_d] = $goods['goods_num'];
    	
    	$orderGoods[OrderGoodsModel::$freightId_d] = $goods['express_id'];
    	
    	$orderGoods[OrderGoodsModel::$goodsPrice_d] = $goods['goods_price'];
    	
    	$orderGoods[OrderGoodsModel::$userId_d] = $userId;
    	
    	$orderGoods[OrderGoodsModel::$status_d] = '0';
    	
    	$orderGoods[OrderGoodsModel::$goodsId_d] = $goods['goods_id'];
    	
    	return $orderGoods;
    }
    
    /**
     * 生成订单商品->立即购买
     */
    public function placeTheOrderGoods() :bool
    {
    	$status = $this->addData();
    	
    	if (!$this->traceStation($status)) {
    		$this->errorMessage .= '、由于长时间没有购买，缓存时间过期，请刷新重新购买。生成订单失败';
    		return false;
    	}
    	
    	return true;
    }
    
   /**
    * 取消订单
    */
    public function cancelOrderGoods() :bool
    {
    	$status = $this->modelObj->where(OrderGoodsModel::$orderId_d.'=:o_id and '.OrderGoodsModel::$userId_d.'=:u_id')
    		->bind([':o_id' => $this->data['id'], ':u_id' => SessionGet::getInstance('user_id')->get()])
    		->save([OrderGoodsModel::$status_d => -1]);
    	if (!$this->traceStation($status)) {
    		$this->errorMessage .= '、订单商品取消订单失败';
    		return false;
    	}
    	$this->modelObj->commit();
    	
    	return true;
    }
    
    /**
     * 订单商品签收
     * @return array
     */
    public function setOrderOverTime() :bool
    {
        $status = $this->modelObj->where(OrderGoodsModel::$orderId_d.'=:o_id and '.OrderGoodsModel::$userId_d.'=:u_id')
            ->bind([':o_id' => $this->data['id'], ':u_id' => SessionGet::getInstance('user_id')->get()])
            ->save([OrderGoodsModel::$status_d => 4]);
        
        if (!$this->traceStation($status)) {
            return false;
        }
        
        return true;
    }
}
