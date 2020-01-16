<?php

namespace Common\Logic;

use Common\Model\UserModel;
use Common\Model\UserAddressModel;
use Common\Model\GoodsCartModel;
use Common\Model\GoodsModel;
use Common\Model\StoreModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;
use Think\SessionGet;

/**
 * 加入购物车
 */
class GoodsCartLogic extends AbstractGetDataLogic {
	
	/**
	 * 订单商品
	 * 
	 * @var array
	 */
	private $orderGoods = [ ];
	/**
	 * 购物车临时数据
	 *
	 * @var array
	 */
	private $resultTmp = [ ];
	
	/**
	 * 要更新的购物车数据
	 * 
	 * @var array
	 */
	private $alreadyGoods = [ ];
	
	/**
	 * 要添加的购物车数据
	 * 
	 * @var array
	 */
	private $addGoodsCart = [ ];
	
	/**
	 * 构造方法
	 *
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new GoodsCartModel ();
		$this->storeModel = new StoreModel ();
		$this->goodsModel = new GoodsModel ();
		$this->addressModel = new UserAddressModel ();
	}
	
	/**
	 * 返回验证数据
	 */
	public function getMessageNotice() :array {
		$message = [ 
						'goods_id' => [ 
										'number' => '必须是数字' 
						],
						'goods_num' => [ 
										'number' => '商品数量必须是数字' 
						],
						'price_new' => [ 
										'number' => '商品价格必须是数字' 
						],
						
						'store_id' => [ 
										'required' => '必须传入商铺ID',
										'number' => '商铺ID必须是数字' 
						] 
		];
		return $message;
	}
	/**
	 * 返回验证数据
	 */
	public function getMessageByAll() {
		$message = [ 
						'goods' => [ 
										'required' => '必须传商品信息' 
						] 
		];
		return $message;
	}
	public function getCartIdValidateByLogin() {
		$message = [ 
						'id' => [ 
										'required' => '购物车Id参数必须' 
						] 
		];
		return $message;
	}
	
	public function getUserBuyCarGoodsInfo() {
		$message = [ 
			'cartId' => [ 
				'checkStringIsNumber' => '购物车必须是数字及其英文逗号的组合' 
			] 
		];
		return $message;
	}
	/**
	 * 获取结果
	 */
	public function getResult() {
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string {
		return GoodsCartModel::class;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array {
		return [ ];
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array {
		return [ 
						UserModel::$userName_d 
		];
	}
	public function getConfigs() {
		$this->getConfig ( "integrat_price_exchange" );
	}
	public function getGoodsCount() {
		$userId = session ( 'user_id' );
		$count ['cartGoodsCount'] = $this->modelObj->getCounts ( $userId );
		return $count;
	}
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		
		return [
			GoodsCartModel::$id_d,
			GoodsCartModel::$goodsId_d,
			GoodsCartModel::$goodsNum_d,
			GoodsCartModel::$storeId_d
		];
	}
	
	/**
	 * 用于购物车生成订单
	 */
	public function getGoodsCartInfo()
	{
		$carryId = $this->data ['cartId'];
		
		$data = $this->modelObj->field($this->getTableColum())->where(GoodsCartModel::$id_d. ' in (%s)', $carryId)->select();
		
		return $data;
	}
	
	/**
	 * 获取购物车信息并缓存（用于购物车生成订单0）
	 */
	public function getGoodsCartInfoCache()
	{
		$cache = Cache::getInstance('', ['expire' => 45]);
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$key = md5($this->data['cartId'].$userId);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getGoodsCartInfo();
		
		if (empty($data)) {
			$this->errorMessage = '购物车异常';
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	
	
	/**
	 * 获取购物车列表
	 */
	public function getCartGoodsList() {
		$page = empty($this->data['page'])?0:$this->data['page']; 
		$where ['user_id'] = SessionGet::getInstance('user_id')->get();
		$where ['is_del'] = '0';
		$field = 'id,goods_id,goods_num,attribute_id,price_new,store_id';
        $retData = $this->modelObj->field($field)->where($where)->page($page.",10")->order("create_time DESC")->select();
        $count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        if (!empty($data)) {
        	return [];
         }
        foreach ($retData as $k => $v) {
            $res[$v['store_id']]['store_id'] = $v['store_id'];
            $res[$v['store_id']]['goods'][] = $v;
        }
        $date = array_values($res);

		
		$store = $this->storeModel->getStoreName ( $date );
			
		$goods = $this->goodsModel->getTitleByArray ( $store );
			
		$data['goods']=$goods;
		$data['count']=$count;
		$data['totalPages']=$totalPages;
		$data['page_size']=10;	
		return $data;
	}
	/**
	 * 获取购物车列表
	 */
	public function getCartGoodsListByUser() {
		$userId = SessionGet::getInstance('user_id')->get();
		$where ['user_id'] = $userId;
		$where ['is_del'] = 0;
		$this->searchField = 'id,goods_id,goods_num,attribute_id,price_new,store_id';
		$retData = parent::getNoPageList ();
		return $retData;
	}
	
	/**
	 * 商品加入购物车
	 *
	 * @author 王强
	 */
	public function addCar() {
		$post = $this->data;
		// 判断用户是否添加过这个商品 如果添加过则修改商品的数量 没有添加过则新增一条数据
		$result = $this->modelObj->addCart ( $post );
		return $result;
	}
	/**
	 * 多个商品加入购物车
	 *
	 * @author 王强
	 */
	public function addCarAll() {
		$post = $this->data;
		// 判断用户是否添加过这个商品 如果添加过则修改商品的数量 没有添加过则新增一条数据
		$result = $this->modelObj->addCartAll ( $post );
		return $result;
	}
	/**
	 * 组合套餐加入购物车
	 */
	public function addPackageToCart() {
		// 优惠套装加入购物车(单套)
		if (! SessionGet::getInstance('user_id')->get()) {
			return false;
		}
		$bid = intval ( $this->data ['bid'] ); // 套餐id
		if ($bid <= 0)
			return false;
		$model_bl = M ( 'goods_package' );
		$bl_info = $model_bl->where ( [ 
						'id' => $bid,
						'status' => 1 
		] )->find (); // 通过组合套餐ID把组合套餐的信息查出来,活动是否处于开启状态
		if (empty ( $bl_info )) {
			return false;
		}
		$packgeInfo = M ( 'goods_package_cart' )->where ( [ 
						'package_id' => $bid,
						'user_id' => session ( 'user_id' ) 
		] )->find ();
		if ($packgeInfo) {
			return false;
		}
		$data ['package_id'] = $bid;
		$data ['money'] = $bl_info ['money'];
		$data ['discount'] = $bl_info ['discount'];
		$data ['title'] = $bl_info ['title'];
		$data ['store_id'] = $bl_info ['store_id'];
		$data ['create_time'] = time ();
		$result = M ( 'goods_package_cart' )->add ( $data );
		
		return $result ? $result : false;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResult()
	 */
	protected function getParseResultByAdd() :array {
		$data = [ ];
		
		$data = $this->data;
		
		$bili = $this->getConfig ( 'integral_proportion' );
		
		$data [GoodsCartModel::$integralRebate_d] = floor ( $data ['price_new'] * $bili );
		
		$data [GoodsCartModel::$userId_d] = SessionGet::getInstance('user_id')->get();
		
		return $data;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultBySave() :array {
		$result = $this->resultTmp;
		
		$data = $this->data;
		
		$data [GoodsCartModel::$goodsNum_d] += $result [GoodsCartModel::$goodsNum_d];
		
		
		$data [GoodsCartModel::$id_d] = $result [GoodsCartModel::$id_d];
		
		
		return $data;
	}
	
	
	
	/**
	 *
	 * @param $data
	 * @return array|string 得到商品信息
	 */
	public function get_good_info($data) {
		$goods_model = M ( 'goods' );
		$total_money = 0;
		$storeid = 0;
		$weight = 0;
		$good_number = 0;
		foreach ( $data as $key => $value ) {
			$goods_data = $goods_model->where ( array (
							'id' => $value ['goods_id'] 
			) )->field ( 'id as goods_id,stock,price_member,p_id,title,weight,express_id,store_id' )->find ();
			if ($value ['goods_num'] > $goods_data ['stock']) {
				return "商品" . $goods_data ['title'] . "库存不足";
			}
			$total_money += $value ['goods_num'] * $goods_data ['price_member'];
			$weight += $goods_data ['weight'];
			$storeid = $value ['store_id'];
			$good_number += $value ['goods_num'];
			
			$data [$key] ['price_member'] = $goods_data ['price_member'];
		}
		$allData = [ ];
		$allData ['store_info'] ['totai_money'] = $total_money;
		$allData ['store_info'] ['store_id'] = $storeid;
		$allData ['store_info'] ['weight'] = $weight;
		$allData ['store_info'] ['goods_num'] = $good_number;
		$allData ['good_info'] = $data;
		return $allData;
	}
	

	
	// 购物车商品数量加
	public function getCartNumPlus() {
		$post = $this->data;
		$res = $this->modelObj->plusNumber ( $post ['cart_id'] );
		return $res;
	}
	// 购物车商品数量减
	public function getCartNumReduce() {
		$post = $this->data;
		$res = $this->modelObj->reduceNumber ( $post ['cart_id'] );
		return $res;
	}
	// 购物车商品数量(修改))
	public function getCartNumModify() {
		$post = $this->data;
		$res = $this->modelObj->modifyNumber ( $post ['cart_id'], $post ['num'] );
		return $res;
	}
	
	// 购物车移入收藏夹
	public function AddCollection() {
		$post = $this->data;
		$user_id = SessionGet::getInstance('user_id')->get();
		$post ['goods'] = explode ( ",", $post ['goods'] );
		M ()->startTrans ();
		foreach ( $post ['goods'] as $key => $value ) {
			$where ['id'] = $value;
			$goods = M ( "GoodsCart" )->where ( $where )->getField ( "goods_id" );
			$collection = M ( "collection" )->field ( "id" )->where ( [ 
							'user_id' => $user_id,
							"goods_id" => $goods 
			] )->find ();
			if (empty ( $collection )) {
				$data ['user_id'] = $user_id;
				$data ['goods_id'] = $goods;
				$data ['add_time'] = time ();
				$res = M ( "collection" )->add ( $data );
				if (! $res) {
					M ()->rollback ();
					return array (
									"status" => 0,
									"message" => "收藏失败",
									"data" => "" 
					);
				}
			}
			$rest = M ( "GoodsCart" )->where ( $where )->delete ();
			if (! $rest) {
				M ()->rollback ();
				return array (
								"status" => 0,
								"message" => "收藏失败",
								"data" => "" 
				);
			}
		}
		M ()->commit ();
		return array (
						"status" => 1,
						"message" => "收藏成功",
						"data" => "" 
		);
	}
	
	/**
	 * 添加购物车（再次购买）
	 */
	public function addCartByOrder() {
		$alreadyGoodsByUser = $this->getCartByGoodsIdAndUserId ();
		
		// 要更新的
		$cartByUpdate = [ ];
		
		// 要添加的
		$cartAdd = [ ];
		
		$alreadyGoods = [ ];
		
		$alreadyGoods = (new ArrayChildren ( $alreadyGoodsByUser ))->convertIdByData ( GoodsCartModel::$goodsId_d );
		
		foreach ( $this->data as $key => $value ) {
			if (empty ( $alreadyGoods [$key] )) {
				$cartAdd [$key] = $value;
			} else {
				$cartByUpdate [$key] = $alreadyGoods [$key];
				$cartByUpdate [$key] [GoodsCartModel::$goodsNum_d] = $alreadyGoods [$key] [GoodsCartModel::$goodsNum_d] + $value ['goods_num'];
			}
		}
		
		$this->modelObj->startTrans ();
		
		// 更新数据
		if (! empty ( $cartByUpdate )) {
			
			$this->alreadyGoods = $cartByUpdate;
			
			$sql = $this->buildUpdateSql ();
			try {
				$status = $this->modelObj->execute ( $sql );
				
				if (! $this->traceStation ( $status )) {
					return false;
				}
			} catch ( \Exception $e ) {
				$this->errorMessage = $e->getMessage ();
				$this->modelObj->rollback ();
				return false;
			}
		}
		
		$addNum = 0;
		// 添加数据
		if (! empty ( $cartAdd )) {
			$this->addGoodsCart = $cartAdd;
			
			$addNum = $this->addAll ();
		}
		if (! $this->traceStation ( $addNum )) {
			return false;
		}
		
		$this->modelObj->commit ();
		
		return true;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAddAll()
	 */
	protected function getParseResultByAddAll() :array {
		$result = [ ];
		
		$i = 0;
		
		$time = time ();
		
		foreach ( $this->addGoodsCart as $key => $value ) {
			$result [$i] [GoodsCartModel::$goodsId_d] = $key;
			$result [$i] [GoodsCartModel::$goodsNum_d] = $value ['goods_num'];
			$result [$i] [GoodsCartModel::$userId_d] = SessionGet::getInstance('user_id')->get();
			$result [$i] [GoodsCartModel::$updateTime_d] = $time;
			$result [$i] [GoodsCartModel::$createTime_d] = $time;
			$result [$i] [GoodsCartModel::$storeId_d] = $value ['store_id'];
			$i ++;
		}
		return $result;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated()
	 */
	protected function getColumToBeUpdated() :array {
		return [ 
						GoodsCartModel::$goodsNum_d,
						GoodsCartModel::$updateTime_d 
		];
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getDataToBeUpdated()
	 */
	protected function getDataToBeUpdated() :array {
		$result = [ ];
		
		$time = time ();
		
		foreach ( $this->alreadyGoods as $key => $value ) {
			$result [$value [GoodsCartModel::$id_d]] [] = $value [GoodsCartModel::$goodsNum_d];
			$result [$value [GoodsCartModel::$id_d]] [] = $time;
		}
		return $result;
	}
	
	/**
	 * 根据商品和用户获取购物车数据
	 */
	public function getCartByGoodsIdAndUserId() {
		$idString = implode ( ',', array_keys ( $this->data ) );
		
		$userId = SessionGet::getInstance('user_id')->get();
		
		$key = base64_encode ( $idString . $userId . '_' . 'goods_cart' );
		
		$cache = Cache::getInstance ( '', [ 
						'expire' => 60 
		] );
		
		$data = $cache->get ( $key );
		
		if (! empty ( $data )) {
			return $data;
		}
		
		$field = GoodsCartModel::$id_d . ',' . GoodsCartModel::$goodsId_d . ',' . GoodsCartModel::$goodsNum_d;
		
		$data = $this->modelObj->where ( GoodsCartModel::$goodsId_d . ' in(%s) and ' . GoodsCartModel::$userId_d . '=:u_id', $idString )->bind ( [ 
				':u_id' => $userId
		] )->getField ( $field );
		
		if (empty ( $data )) {
			return [ ];
		}
		
		return $data;
	}
	// 删除购物车
	public function goodsCartDelete() {
		$post = $this->data;
		$where ['id'] = $post ['id'];
		$res = $this->modelObj->where ( $where )->delete ();
		if (! $res) {
			return array (
							"status" => 0,
							"message" => "删除失败",
							"data" => "" 
			);
		}
		return array (
						"status" => 1,
						"message" => "删除成功",
						"data" => "" 
		);
	}
	
	/**
	 * 获取关联字段
	 * @return string
	 */
	public function getSplitKeyByGoodsId()
	{
		return GoodsCartModel::$goodsId_d;
	}
	
	/**
	 * 获取关联字段
	 * @return string
	 */
	public function getSplitKeyByStoreId()
	{
		return GoodsCartModel::$storeId_d;
	}
	
	/**
	 * 删除购物车数据
	 */
	public function deleteCartByTrans() 
	{
		$status = $this->deleteCart();
		
		if (!$this->traceStation($status)) {
			$this->errorMessage .= '、删除购物车失败';
			return false;
		}
		
		$this->modelObj->commit();
		
		return true;
	}
	
	/**
	 * 删除购物车
	 * @return boolean
	 */
	public function deleteCart() 
	{
		try {
			$status = $this->modelObj->where(GoodsCartModel::$id_d.' in (%s)', implode(',', $this->data))->delete();
		} catch (\Exception $e) {
			$this->errorMessage .= $e->getMessage();
			return false;
		}
		
		if ($status === false) {
			$this->errorMessage .= '、删除购物车失败';
			return false;
		}
		return true;
	}
	
}
