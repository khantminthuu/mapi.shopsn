<?php
declare(strict_types = 1);
namespace Common\Logic;

use Common\Model\GoodsModel;
use Common\Model\StoreModel;
use Common\Model\GoodsPackageCartModel;
use Think\Cache;
use Common\SessionParse\SessionManager;
use Think\SessionGet;

/**
 * 加入购物车
 *
 *
 */
class GoodsPackageCartLogic extends AbstractGetDataLogic {
	
	/**
	 * 构造方法
	 *
	 * @param array $data        	
	 */
	public function __construct(array $data = [], $split = '') {
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new GoodsPackageCartModel ();
		
		
		
	}
	/**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'id' => [
            	'required' => '购物车ID必填',
            	'checkStringIsNumber' => '购物车编号必须是以,拼接的字符串'
            ]
        ];
        return $message;
    }
    /**
     * 返回验证数据
     */
    public function getValidateByAdd()
    {
        $message = [
            'id' => [
            	'required' => '套餐ID必填',
            ]
        ];
        return $message;
    }
    /**
     * 返回验证数据
     */
    public function getValidateByNumModify()
    {
        $message = [
            'id' => [
            	'required' => '购物车ID必填',
                'number' => '必须是数字',
            ],
            'package_num' => [
            	'required' => '购物车套餐数量必填',
                'number' => '必须是数字',
            ],
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
		return GoodsPackageCartModel::class;
	}
	
     //套餐购物车列表
     public function getCartGoodsList(){
     	$this->storeModel = new StoreModel ();
     	$this->goodsModel = new GoodsModel ();
     	$post = $this->data;
     	$user_id = SessionGet::getInstance('user_id')->get();
     	$packageCart = $this->modelObj->field("id,package_id,package_num")->where(['user_id'=>$user_id])->order("create_time DESC")->select();
     	if (empty($packageCart)) {
     		return array("status"=>0,"message"=>"暂无数据","data"=>"");
     	}
     	foreach ($packageCart as $key => $value) {
     		$where['id'] = $value['package_id'];
     		$package = M("GoodsPackage")->field("total,discount,store_id,title")->where($where)->find();
     		$packageCart[$key]['total'] = $package['total'];
     		$packageCart[$key]['discount'] = $package['discount'];
     		$packageCart[$key]['store_id'] = $package['store_id'];
     		$packageCart[$key]['title'] = $package['title'];
     		$goods_id = M("goods_package_sub")->field("goods_id,discount")->where(['package_id'=>$value['package_id']])->select();
     		$packageCart[$key]['goods'] = $this->goodsModel->getTitleByTwo($goods_id);
     	}
     	$result = [];
     	foreach ($packageCart as $k => $info) {
     		$store = $this->storeModel->field("shop_name,store_logo")->where(['id'=>$info['store_id']])->find();
            $result[$info['store_id']]['cart'][] = $info;
            $result[$info['store_id']]['store_name'] = $store['shop_name'];
            $result[$info['store_id']]['store_logo']=$store['store_logo'];

        }
        $cart = array_values($result);
     	return array("status"=>1,"message"=>"获取成功","data"=>$cart);
    }
	//删除套餐购物车
	public function getCartGoodsDel(){
		$post = $this->data;
		$res = $this->modelObj->where(['id'=>$post['id']])->delete();
		if (!$res) {
			return array("status"=>0,"message"=>"删除失败","data"=>"");
		}
		return array("status"=>1,"message"=>"删除成功","data"=>"");
	}
    // 购物车商品数量加
	public function getCartNumPlus() {
		$post = $this->data;
		$num = $this->modelObj->where(['id'=>$post['id']])->getField("package_num");
		if ($num == 99) {
			return array("status"=>0,"message"=>"套餐数量不能大于100","data"=>"");
		}
		$res = $this->modelObj->where(['id'=>$post['id']])->setInc("package_num");
		if (!$res) {
			return array("status"=>0,"message"=>"操作失败","data"=>"");
		}
		return array("status"=>1,"message"=>"操作成功","data"=>"");
	}
	// 购物车商品数量减
	public function getCartNumReduce() {
		$post = $this->data;
		$num = $this->modelObj->where(['id'=>$post['id']])->getField("package_num");
		if ($num == 1) {
			return array("status"=>0,"message"=>"套餐数量不能低于1","data"=>"");
		}
		$res = $this->modelObj->where(['id'=>$post['id']])->setDec("package_num");
		if (!$res) {
			return array("status"=>0,"message"=>"操作失败","data"=>"");
		}
		return array("status"=>1,"message"=>"操作成功","data"=>"");
	}
	// 购物车商品数量(修改))
	public function getCartNumModify() {
		$post = $this->data;
		$where['id'] = $post['id'];
		$data['package_num'] = $post['package_num'];
		if ($data['package_num'] > 100) {
			return array("status"=>0,"message"=>"套餐数量不能大于100","data"=>"");
		}
		$data['update_time'] = time();
		$res = $this->modelObj->where($where)->save($data);
		if ($res===false) {
			return array("status"=>0,"message"=>"操作失败","data"=>"");
		}
		return array("status"=>1,"message"=>"操作成功","data"=>"");
	}
	
	/**
	 * 获取套餐购物车信息并缓存
	 * @return array
	 */
	public function getPackageInfoByCartCache():array
	{
		$userId = SessionGet::getInstance('user_id')->get();
		
		$key = md5($this->data['id'].'what_happend_package_cart'.$userId);
		
		$cache = Cache::getInstance('', ['expire' => 60]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data;
		}
		
		$data = $this->getPackageInfoByCart();
		
		if (count($data) === 0) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
	}
	
	
	/**
	 * 获取套餐购物车信息
	 * @return array
	 */
	public function getPackageInfoByCart():array
	{
		$userId = SessionGet::getInstance('user_id')->get();
		$data = $this->modelObj
			->field($this->getTableColum())
			->where(GoodsPackageCartModel::$id_d .' in (%s) and '. GoodsPackageCartModel::$userId_d.'=%d', [$this->data['id'], $userId])
			->select();
		
		if (count($data) === 0) {
			
			$this->errorMessage = '获取购物车信息错误';
			
			return [];
		}
		
		return $data;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum() :array
	{
		return [
			GoodsPackageCartModel::$id_d,
			GoodsPackageCartModel::$packageId_d,
			GoodsPackageCartModel::$packageNum_d,
			GoodsPackageCartModel::$storeId_d,
			GoodsPackageCartModel::$userId_d
		];
	}
	
	
     /**
     * 组合套餐加入购物车
     */
    public function addPackageToCart(){ 
    	$bid = $this->data['id'];
        $cart = explode ( ",", $bid);
        
        $userId = SessionGet::getInstance('user_id')->get();
        //优惠套装加入购物车(单套)
        if (!$userId) {
            M()->rollback();
            return array("status"=>0,"message"=>"添加失败","data"=>"");
        }
        $model_bl = M('goods_package');
        foreach ($cart as $key => $value) {
            $bl_info = $model_bl->where(['id'=>$value,'status'=>1])->find(); //通过组合套餐ID把组合套餐的信息查出来,活动是否处于开启状态
            if (empty($bl_info)) {
                M()->rollback();
                return array("status"=>0,"message"=>"添加失败1","data"=>"");
            }
            $packgeInfo=M('goods_package_cart')->where(['package_id'=>$bl_info['id'],'user_id'=>session('user_id')])->find();
            if(!empty($packgeInfo)){
                $date['package_num'] = $packgeInfo['package_num']+1;
                $date['create_time'] = time();
                $res = M('goods_package_cart')->where(['id'=>$packgeInfo['id']])->save($date); 
                if (!$res) {
                    M()->rollback();
                    return array("status"=>0,"message"=>"添加失败2","data"=>"");
                }    
            }else{
                $data['package_id']=$value;
                $data['store_id']=$bl_info['store_id'];
                $data['create_time']=time();
                $data['user_id'] = $userId;
                $result=M('goods_package_cart')->add($data);
                if (!$result) {
                    M()->rollback();
                    return array("status"=>0,"message"=>"添加失败3","data"=>"");
                }
            }
        }
        M()->commit();
        return array("status"=>1,"message"=>"添加成功","data"=>$result);
    }
    
    /**
     * 获取关联字段key
     * @return string
     */
    public function getSplitKeyByPackage() :string
    {
    	return GoodsPackageCartModel::$packageId_d;
    }
    
    /**
     * 获取店铺id
     * @return string
     */
    public function getSplitKeyByStore() :string
    {
    	return GoodsPackageCartModel::$storeId_d;
    }
    
    /**
     * 创建订单删除购物车
     * @return bool
     */
    public function deletePackageCart() :bool
    {
    	$packageCart = SessionManager::GET_GOODS_DATA_SOURCE();
    	
    	$idString = implode(',', array_column($packageCart, 'id'));
    	
    	try {
    		$status = $this->modelObj->where(GoodsPackageCartModel::$id_d.' in (%s)', $idString)->delete();
    		
    		if (!$this->traceStation($status)) {
    			$this->errorMessage = '删除购物车失败';
    			return false;
    		}
    		
    		$this->modelObj->commit();
    		
    		return true;
    		
    	} catch (\Exception $e) {
    		$this->modelObj->rollback();
    		$this->errorMessage = '删除购物车失败';
    		return false;
    	}
    }
}
