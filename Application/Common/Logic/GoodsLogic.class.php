<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\CommonModel;
use Common\Model\GoodsModel;
use Common\Model\OrderGoodsModel;
use Common\Model\GoodsImagesModel;
use Think\Cache;
use Think\Log;
use Think\SessionGet;
use Common\Model\StoreModel;
/**
 * 逻辑处理层
 *
 */
class GoodsLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法 
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new GoodsModel();
        $this->goodsImagesModel = new GoodsImagesModel();
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
        return GoodsModel::class;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment() :array
    {
        return [

        ];
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    public function likeSerachArray() :array
    {
        return [
            GoodsModel::$title_d,
        ];
    }

    /**
     * 验证 参数获取一级分类下面的商品
     */
    public function getValidateByGoods() :array
    {
    	return [
    		'id' => [
    			'number' => '编号必须是数字',
    		],
    		'page' => [
    			'number' => '页码必须是数字'
    		],
    	];
    }
    
     /**
     * 验证
     */
     public function getAttrValidateByLogin()
     {
        $message = [
            'id' => [
                'required' => '商品ID必传',
                'number'   => '商品id必须是数字'
            ],
        ];
        return $message;
    }
    public function getValidateByDetail() {
        $message = [ 
            'id' => [ 
                'number' => '商品Id参数必须' 
            ],
            'goods_num' => [ 
                'number' => '商品数量参数必须' 
            ],
        ];
        return $message;
    }
    /**
     * 返回验证数据
     * author 刘嘉强
     */
    public function getValidateByLogin()
    {
        $message = [
            'class_three' => [
                'required' => '必须传入商品分类ID',
            ],
        ];
        return $message;
    }

    public function getValidateByShop()
    {
        $message = [
            'store_id' => [
                'required' => '必须传入店铺ID',
            ]
        ];

        return $message;
    }

    /**
     * 得到分类下所有的商品
     * author 刘嘉强
     */
    public function getClassGoods(){
        //#TODO 这里是查询条件
        if (!empty($this->data['class_two'])){
            $where['class_two'] = $this->data['class_two'];
        }
        if (!empty($this->data['class_three'])){
            $where['class_three'] = $this->data['class_three'];
        }
        $where['approval_status'] = '1';
        $where['p_id'] = '0';
        $where['status'] = '0';
        $where['shelves'] = '1';
        $field = 'id,id as goods_id,title,price_member,comment_member,sales_sum,store_id,goods_type,p_id';
        if (!empty($this->data['title'])) {
            $g_where['title']  =['like', '%' . $this->data["title"] . '%'];
        }
        if (!empty($this->data['sort_field'])){
            $fields = $this->data['sort_field'];
            if ($this->data['sort_type'] == "desc"){
                $Order = "$fields  DESC";
            }else{
                $Order = "$fields  ASC";
            }
        } 
        $page = empty($this->data['page'])?0:$this->data['page']; 
        $goods = $this->modelObj->field($field)->where($where)->page($page.",10")->order($Order)->select();
        if (empty($goods)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $retData = $this->goodsImagesModel->getgoodImageByGoods($goods);
        $data['records'] =  $retData;
        $data['count'] =  $count;
        $data['page_size'] =  10;
        $data['totalPages'] =  $totalPages;
        return array("status"=>1,"message"=>"获取成功!","data"=>$data);
    }

    /**
     * 搜索店铺或者商品
     */
    public function getSerchGoods(){

        if (!empty($this->data['title'])){
            $this->searchField = 'id, title, price_member, comment_member, sales_sum,store_id,goods_type';
            //#TODO 这里是查询条件
            $this->searchTemporary = [
                GoodsModel::$title_d => ['like', '%' . $this->data[GoodsModel::$title_d] . '%'],
                GoodsModel::$approvalStatus_d => [ 'eq' , '1'],
            ];
            $data =  parent::getDataList();
            if (empty($data)){
                $this->errorMessage = '暂无数据';
                return [];
            }
            return $data;
        }
    }

    /**
     * 获取子类数据
     */
    public function getGoodsChildrenById()
    {
    	
    	$cache = Cache::getInstance('', ['expire' => 100]);
    	
    	$key = $this->data['id'].'_edf';
    	
    	$pId = $cache->get($key);
    	
    	if (empty($data)) {
    		$pId = $this->modelObj->where(GoodsModel::$id_d.'=:id')->bind([':id' => $this->data['id'] ])->getField( GoodsModel::$pId_d);
    	} else {
    		return $pId;
    	}
    	
    	if (empty($pId)) {
    		return null;
    	}

    	$cache->set($key, $pId);

        return $pId;

    }



     /**
     * 获取子类商品【上架的】
     * @param int $pId 商品父级编号
     * @return array
     */
     public function getChildrenGoods()
     {  
         $pId = $this->data['id'];

         if( !is_numeric( $pId ) ){
          return array();
      }

      $cache = Cache::getInstance('', ['expire' => 30]);
//    	$pId = $this->getGoodsChildrenById();
      $key = $this->data['id'].'_kjd';
      $data = $cache->get($key);
      if(empty($data)) {
          $field = [
            GoodsModel::$id_d,
            GoodsModel::$title_d,
            GoodsModel::$brandId_d,
            GoodsModel::$pId_d,
            GoodsModel::$description_d,
            GoodsModel::$classId_d,
            GoodsModel::$priceMarket_d,
            GoodsModel::$priceMember_d,
        ];
        $data = $this->modelObj->field( $field )->where( GoodsModel::$pId_d . '= %d and ' . GoodsModel::$shelves_d . '= 1 and '.GoodsModel::$approvalStatus_d.' = 1', $pId )->select();

    } else {
      return $data;
  }
  $cache->set($key, $data);
  return $data;
}

    /**
     * 获取子类商品【上架的】
     * @param int $pId 商品父级编号
     * @return array
     */
    public function getChildrenGoodsByAttr()
    {
    	$post = $this->data;
    	$goods=$this->modelObj->field("id as goods_id,title,code,weight,p_id")->where(['id'=>$post['id']])->find();
    	if($goods['p_id'] == 0){
    		$where['goods_id'] = $goods['goods_id'];
    	}else{
    		$where['goods_id'] = $goods['p_id'];
    	}
    	$attr = M("GoodsAttr")->field("attribute_id,attr_value")->where($where)->select();
    	if(!empty($attr)){
    		foreach ($attr as $key => $value) {
    			$attr[$key]['attribute_id'] = M("GoodsAttribute")->where(['id'=>$value['attribute_id']])->getField("attr_name");
    		}
    	}
    	$goods['attr'] = $attr;
    	return array("status"=>1,"message"=>"获取成功","data"=>$goods);
    	
    }
    

    public function getGoodSpeInfo(){
        $this->searchTemporary = [
            GoodsModel::$id_d => $this->data[GoodsModel::$id_d],
        ];
        $this->searchField ='id,brand_id,store_id,title,price_market,price_member,stock,selling,class_id,recommend,
        code,top,season_hot,description,goods_type,status,p_id,store_id';
        $retData = parent::getFindOne();
        if (empty($retData)){
            $this->errorMessage = '暂无数据';
            return [];
        }
        // 获取商品图片
        $retData['images'] = $this->goodsImagesModel->getgoodOneImage($this->data[GoodsModel::$id_d])['pic_url'];
        $retData['good_key'] = CommonModel::get_modle("SpecGoodsPrice")->getGoodKey($this->data[GoodsModel::$id_d]);
        return $retData;
    }

    /**
     * 得到商品的详情
     */
    public function getGoodsInfoByGoodsId() :array
    {
    	
    	if (empty($this->data[$this->splitKey])) {
    		$this->errorMessage = '商品错误';
    		return [];
    	}

    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$tmp = $userId ? $userId : '0' ;

    	$key = $this->splitKey.'_'.$this->data[$this->splitKey].'_'.$tmp;
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$price = ($tmp === '0' ? GoodsModel::$priceMarket_d :  GoodsModel::$priceMember_d).' as price';
    	
    	$field = [
    		GoodsModel::$id_d,
    		GoodsModel::$title_d,
    		GoodsModel::$pId_d,
    		$price,
    		GoodsModel::$stock_d,
    		GoodsModel::$storeId_d,
    		GoodsModel::$weight_d,
    		GoodsModel::$expressId_d,
    	];
    	
    	$data = $this->modelObj
      ->field($field)
      ->where(GoodsModel::$id_d.'=:id')
      ->bind([':id' => $this->data[$this->splitKey]])
      ->find();

      if (empty($data)) {
          $this->errorMessage = '没有该商品';
          return [];
      }

      $cache->set($key, $data);

      return $data;

  }

    /**
     * 支付处理
     */
    public function payParaseByGoods() :array
    {
    	$data = $this->getGoodsInfoByGoodsId();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	
    	return $data;
    }

    //商品搜索
    public function searchGoods(){
        $post = $this->data;
        if (!empty($post['sort'])){
            $flag = $post['sort'];
            switch ($flag) {
                case 1:$order = 'sales_sum DESC';break;//销量由高到低
                case 2:$order = 'sales_sum ASC';break;//销量由低到高
                case 4:$order = 'price_member ASC';break;//价格由高到低
                case 3:$order = 'price_member DESC';break;//价格由低到高
                case 5:$order = 'sales_sum DESC';break;
                case 6:$order = 'sales_sum ASC';break;
            }
        }else{
            $flag = "";
            $order = 'create_time DESC';
        }
        $keyword = $post['keyword']; 
        $page = empty($post['page'])?0:$post['page'];
        $where['title'] = array('like','%'.$keyword.'%');
        $where['p_id']  = array( 'EQ', '0' );
        $where[GoodsModel::$shelves_d] = '1';
        $where[GoodsModel::$approvalStatus_d] = '1';

        $field = "id,id as goods_id,p_id,price_member as goods_price,title,sales_sum,comment_member";

        
        $goods = $this->modelObj->getGoodsByWhere($field,$where,$page,$order); // 实例化User对象
        if (!empty($goods['data'])) {
            $goods['data'] = $this->goodsImagesModel->getgoodImageByGoods($goods['data']);
            $goods['flag'] = "goods";
            return array("status"=>1,"message"=>"获取成功","data"=>$goods);
        }else{
            if (!empty($post['sort'])){
                $flag = $post['sort'];
                switch ($flag) {
                    case 1:$s_order = 'store_sales DESC';break;//销量由高到低
                    case 2:$s_order = 'store_sales ASC';break;//销量由低到高
                    case 5:$s_order = 'store_sales DESC';break;
                    case 6:$s_order = 'store_sales ASC';break;
                }
            }else{
                $flag = "";
                $s_order = 'store_sort';
            }
            $s_where['shop_name'] = array('like','%'.$keyword.'%');
            $s_where['store_state'] = 1;
            $s_field = "id,shop_name,store_sales,store_logo";
            
            $storeModel = new StoreModel();
            
            $store = $storeModel->getStoreByWhere($s_where,$s_field,$page,$s_order);
            if (empty($store['data'])) {
                return array("status"=>0,"message"=>"获取失败","data"=>$store);
            }else{
                $store['data'] = $this->modelObj->getGoodsByStore($store['data']);
                $store['flag'] = "store";
                return array("status"=>1,"message"=>"获取成功","data"=>$store);
            }   
        }
    }
    
    /**
     * 减少库存
     */
    public function delStock ()
    {
    	if (empty($this->data)) {
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	$sql = $this->buildUpdateSql();
    	
    	try {
    		$status = $this->modelObj->execute($sql);
    	} catch (\Exception $e) {
    		Log::write('sql -- '.$sql, Log::ERR, '', './Log/order/goods_sql_'.date('y_m_d').'.txt');
    		$this->errorMessage = $e->getMessage();
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	return $status;
    }
    /**
     * 减少库存
     */
    public function delStocks() :bool
    {

        if (empty($this->data)) {
            $this->modelObj->rollback();
            return false;
        }
        $orderModel = new OrderGoodsModel();
        $where['order_id'] = array("IN",$this->data);
        $order = $orderModel->field('goods_id,goods_num')->where($where)->select();
        $this->data = $order;
        $sql = $this->buildUpdateSql();
        try {
            $status = $this->modelObj->execute($sql);
            if (!$this->traceStation($status)) {
                return false;
            }

        } catch (\Exception $e) {
            Log::write('sql -- '.$sql, Log::ERR, '', './Log/order/goods_sql_'.date('y_m_d').'.txt');
            $this->errorMessage = $e->getMessage();
            $this->modelObj->rollback();
            return false;
        }

        return true;
    }
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated() :array
    {
    	//批量更新
    	$pasrseData = array();
    	foreach ($this->data as $key => $value)
    	{
    		if (empty($value['goods_num'])) {
    			$this->modelObj->rollback();
    			return [];
    		}
    		$pasrseData[$value['goods_id']][] = GoodsModel::$stock_d.'-'. $value['goods_num'];
    		
    		$pasrseData[$value['goods_id']][] = GoodsModel::$salesSum_d.'+'.$value['goods_num'];
    	}
    	
    	return $pasrseData;
    }
    
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated() :array
    {
    	return [
    		GoodsModel::$stock_d,
    		GoodsModel::$salesSum_d
    	];
    }
    
    /**
     * 验证库存是否足够
     */
    public function checkStock()
    {
    	$data = $this->getStock();

    	if (empty($data)) {
    		return [];
    	}
    	
    	
    	foreach ($data as $key => $value) {
    		
    		if ($value[GoodsModel::$stock_d] >= $this->data[$key]) {
    			continue;
    		}
    		
    		$this->errorMessage = $value[GoodsModel::$title_d].'库存不足';
    		
    		return false;
    	}
    	return true;
    }
    
    /**
     * 再次购买验证库存
     */
    public function orderBuyAgainCheckStock()
    {
        $data = $this->getStock();

        if (empty($data)) {
         return [];
     }

     foreach ($data as $key => $value) {

         if ($value[GoodsModel::$stock_d] >= $this->data[$key]['goods_num']) {
          continue;
      }

      $this->errorMessage = $value[GoodsModel::$title_d].'库存不足';

      return false;
  }
  return true;
}

    /**
     * 获取库存
     */
    public function getStock() :array
    {
    	$data = $this->data;
    	
    	$goodsId = implode(',', array_keys($data));
    	
    	$goods = $this->modelObj
      ->where(GoodsModel::$id_d.' in (%s)', $goodsId)
      ->getField(GoodsModel::$id_d.','.GoodsModel::$stock_d.','.GoodsModel::$title_d);

      if (empty($goods)) {
          return [];
      }

      return $goods;
  }

    /**
     * 验证库存是否足够
     */
    public function checkStockByGoodsDetail()
    {
    	$data = $this->getStockByGoodsDetail();
    	
    	if (empty($data)) {
    		$this->errorMessage = '商品异常';
    		return false;
    	}

    	if ($data[$this->data['goods_id']] >= $this->data['goods_num']) {
    		return true;
    	}

    	$this->errorMessage = '库存不足';

    	return false;
    }
    
    /**
     * 商品详情获取库存
     */
    public function getStockByGoodsDetail()
    {
    	$goodsId = $this->data['goods_id'];
    	
    	$goods = $this->modelObj
      ->where(GoodsModel::$id_d.' = %d', $goodsId)
      ->getField(GoodsModel::$id_d.','.GoodsModel::$stock_d);
      if (empty($goods)) {
          return [];
      }
      return $goods;
  }

    /**
     * 得到一级分类下所有的商品
     * author 王波
     */
    public function getClassGoodsByOneClass(){
        //#TODO 这里是查询条件
        // 获取福父id
        $where = [
            GoodsModel::$classId_d => $this->data["id"],
            GoodsModel::$approvalStatus_d => '1',
            GoodsModel::$pId_d => '0',
            GoodsModel::$shelves_d => '1',
            GoodsModel::$status_d => '0',
        ];
//        $p_id = $this->modelObj->where($where)->field("id")->select();
//        if(empty($p_id)){
//            return array('status'=>0,'message'=>'暂无数据','data'=>'');
//        }
//        $p_where = array_column($p_id, 'id');
        
        $field = 'id,id as goods_id,title,price_member,comment_member,sales_sum,store_id,goods_type,p_id';
        
//        $g_where = [];
//
//        $g_where['p_id']  =array("IN",$p_where);
//        $g_where['approval_status']  = '1';
//
//        $g_where['status']  = '0';
//
//        $g_where[GoodsModel::$shelves_d]  ='1';
        
        if (!empty($this->data['title'])) {
            $where['title']  =['like', '%' . $this->data["title"] . '%'];
        }
        if (!empty($this->data['sort_field'])){
            $fields = $this->data['sort_field'];
            if ($this->data['sort_type'] == "desc"){
                $Order = "$fields  DESC";
            }else{
                $Order = "$fields  ASC";
            }
        }
        $page = empty($this->data['page'])?0:$this->data['page'];
        $goods = $this->modelObj->field($field)->where($where)->page($page.",10")->order($Order)->select();
        if(empty($goods)){
            return array('status'=>0,'message'=>'暂无数据','data'=>'');
        }
        $count =  $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $retData = $this->goodsImagesModel->getgoodImageByGoods($goods);
        $data['records'] =  $retData;
        $data['count'] =  $count;
        $data['page_size'] =  10;
        $data['totalPages'] =  $totalPages;
        return array('status'=>1,'message'=>'获取成功','data'=>$data);;
    }
    
    /**
     * 获取要查询的字段
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	
    	return [
    		GoodsModel::$id_d,
    		GoodsModel::$title_d,
    		GoodsModel::$pId_d,
    		GoodsModel::$storeId_d,
    		GoodsModel::$stock_d,
    		GoodsModel::$expressId_d,
    		GoodsModel::$weight_d,
    		GoodsModel::$description_d,
    		GoodsModel::$priceMarket_d,
    		GoodsModel::$priceMember_d,
    		GoodsModel::$commentMember_d,
    		GoodsModel::$salesSum_d,
    		GoodsModel::$brandId_d,
    		GoodsModel::$classTwo_d,
    		GoodsModel::$shelves_d,            //直接访问下架商品需要参数     meng
            GoodsModel::$picUrl_d
        ];
    }
    
    /**
     * 获取商品详情（无缓存）
     * @return array
     */
    public function getGoodsDetailById() :array
    {
    	$data = $this->modelObj
      ->field($this->getTableColum())
      ->where(GoodsModel::$id_d.'=:id and '.GoodsModel::$shelves_d.' = 1 and '.GoodsModel::$approvalStatus_d.' = 1')
//    		->where(GoodsModel::$id_d.'=:id and '.GoodsModel::$approvalStatus_d.' = 1')
      ->bind([':id' => [$this->data['id'], \PDO::PARAM_INT]])
      ->find();

      if (empty($data)) {
          $this->errorMessage = '商品信息错误';
          return [];
      }
      return $data;
  }

    /**
     * 获取商品详情
     * @return array
     */
    public function getGoodsDetailCache1() :array
    {
    	$key = 'goods_detail'.$this->data['id'];
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getGoodsDetailById();
    	if (empty($data)) {
    		$this->errorMessage = '没有找到这个商品任何信息';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }


    /**
     Tweltar

     */

      public function getGoodsDetailCache():array
      {
        $key = 'goods_detail'.$this->data['id'];

        $cache = Cache::getInstance('',['expire'=>60]);

        $data = $cache->get($key);

        if(!empty($data)){
            return $data;
        }

        $data = $this->getGoodsDetailById();
        if(empty($data)){
            $this->errorMessage = 'No information found for this product';
            return [];
        }

        $cache->set($key,$data);

        return $data;

      }








    /*
        khantminthu
    */
        public function getGoodDetail()    :array
        {
           $key = 'goods_detail'.$this->data['id'];

           $cache = Cache::getInstance('',['exprie'=>60]);

           $data = $cache->get($key);       //bool

           if(!empty($data)){
            return $data;
        }

        // $data = $this->getGoodsDetail();
        $data = $this->modelObj->getGoodsDetail($this->data);
        if(empty($data)){
            $this->errorMessage = '没有找到这个商品任何信息';
            return [];
        }
        $cache->set($key,$data);
        return $data;
    }


    /*
       ttpw
   */

    public function getGoodImgDetail() :array
    {
        $key = 'goods_detail'.$this->data['id'] ;

        $cache = Cache::getInstance( '', ['expire'=>60]);

        $data = $cache->get($key);

        if(!empty($data)){
            return $data;

        }

        $data = $this->modelObj->getGoodImgDetail($this->data);
        if(empty($data))
        {
            $this->errorMessage='没有找到这个商品任何信息';
            return[];
        }

        $cache->set($key,$data);
        return $data;

    }

























    public function getGoodsDetail()    :array
    {
        $data = $this->modelObj
        ->field($this->getTableColum())
        ->where(GoodsModel::$id_d.'=:id and '.GoodsModel::$shelves_d.' = 1 and '.GoodsModel::$approvalStatus_d.' = 1')
        ->bind([':id' => [$this->data['id'] , \PDO::PARAM_INT]])
        ->find();
        
        if(empty($data)){
            $this->errorMessage = '商品信息错误';
            return [];
        }
        // $cache->set($key, $data);
        
        return $data;
    }

    //获取商品详情
    public function getGoodsDetailByOrder() :array
    { 
        $data = $this->getGoodsDetailCache();

        if (empty($data)) {
         return [];
     }

     unset($data[GoodsModel::$priceMarket_d]);

     return $data;
 }

    /**
     * 推荐分类的商品
     * @return []
     */
    public function getRecommend()
    {
    	$classId = $this->data['id'];
    	
//    	$cache = Cache::getInstance('', ['expire' => 90]);
    	
//    	$userId = SessionGet::getInstance('user_id')->get();
//
//    	$userKey = $userId ? $userId : '';
    	
//    	$key = 'goods_class_id_page'.'_'.$this->data['page'].'_'.$classId.$userKey;
    	
//    	$data = $cache->get($key);
    	
//    	if (!empty($data)) {
//    		return $data;
//    	}
    	
    	$priceColunm = GoodsModel::$priceMember_d ;

    	$priceColunm = $priceColunm.' as price';
    	
    	$field = GoodsModel::$title_d.','.GoodsModel::$id_d.','.$priceColunm.','.GoodsModel::$classId_d .','.GoodsModel::$pId_d;
    	//分组查询 查询当前分组的其中一个商品
//    	$sql = <<<aaa
//    	SELECT {$field} FROM db_goods a1
//    	INNER JOIN (
//    		SELECT max(a.id) as id FROM db_goods a
//    		LEFT JOIN db_goods b
//    		ON a.id = b.p_id
//    		GROUP BY a.p_id
//    		HAVING a.p_id > 0
//    	) b1
//    	ON a1.id = b1.id
//    	where   a1.class_id ={$classId} and a1.shelves = 1 and a1.status=0 and a1.approval_status = 1
//    	ORDER BY a1.id , b1.id  DESC ;
//aaa;
//    	$goods = $this->modelObj->query($sql);
//    	if (empty($goods)) {
//    		return [];
//    	}
        $where['class_id'] = $classId;
        $where['shelves'] = 1;
        $where['status'] = 0;
        $where['approval_status'] = 1;
        $where['p_id'] = 0;
        $goods = $this->modelObj->field($field)->where($where)->limit(6)->select();
        if(empty($goods)){
            return [];
        }

//    	$cache->set($key, $goods);

        return $goods;
    }
    
    public function getSplitKeyByPId():string
    {
    	return GoodsModel::$id_d;
    }
    
    /**
     * 获取商铺关联字段
     * @return string
     */
    public function getSplitKeyByStore()
    {
    	return GoodsModel::$storeId_d;
    }
    
    /**
     * 获取消息
     */
    public function getMessageByStoreRecommendGoods()
    {
    	return [
    		GoodsModel::$storeId_d => [
    			'number' => '必须是数字'
    		]
    	];	
    }
    
    /**
     * 验证商品配件
     */
    public function getMessageByAccessories()
    {
    	return [
    		'goods_id' => [
    			'required' => '商品编号必传',
    			'checkStringIsNumber' => '必须是数字'
    		]
    	];
    }
    
    /**
     * 获取商铺的4个推荐商品
     * @return array
     */
    public function getStoreGoodsByRecommend()
    {
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$userKey = $userId ? $userId : '';
    	
    	$key = $this->data['store_id'].'_'.$userKey.'_recommend_store';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$priceColunm = GoodsModel::$priceMember_d ;
    	
    	$priceColunm = $priceColunm.' as price';
    	
    	$field = 'a1.'.GoodsModel::$title_d.','.'a1.'.GoodsModel::$id_d.','.'a1.'.$priceColunm.','.'a1.'.GoodsModel::$classId_d .','.'a1.'.GoodsModel::$pId_d;
    	//分组查询 查询当前分组的其中一个商品
    	
//    	$sql = <<<aaa
//		SELECT {$field} FROM db_goods a1
//    	INNER JOIN (
//    		SELECT max(a.id) as id FROM db_goods a
//    		LEFT JOIN db_goods b
//    		ON a.id = b.p_id
//    		GROUP BY a.p_id
//    		HAVING a.p_id > 0
//    	) b1
//    	ON a1.id = b1.id
//    	where a1.shelves = 1 and a1.status=0 and a1.approval_status = 1 and a1.recommend = 1 and a1.store_id={$this->data['store_id']}
//    	ORDER BY a1.id , b1.id  DESC
//		Limit 4;
//aaa;
      $data = $this->modelObj->alias('a1')
      ->join('left join db_goods_images as b on b.goods_id = a1.id')
      ->field($field)
      ->where(['a1.shelves'=>1,'a1.approval_status'=>1,'a1.store_id'=>$this->data['store_id'],'is_thumb'=>1])
      ->limit(4)
      ->select();

      if (empty($data)) {
          $this->errorMessage = '没有数据';
          return [];
      }

      $cache->set($key, $data);

      return $data;
  }
    /**
     * 获取 商品数据
     * @return array
     */
    public function getGoodsForData()
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$key = base64_encode(implode(',', array_keys($this->data)).'_ddk'.$userId);
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getGoodsByOtherData();

    	if (empty($data)) {
    		$this->errorMessage = '找不到对应的商品';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 根据其他数据获取商品数据
     */
    public function getGoodsByOtherData()
    {
    	$field = [
    		GoodsModel::$id_d,
    		GoodsModel::$priceMember_d,
    		GoodsModel::$pId_d,
    		GoodsModel::$storeId_d,
    		GoodsModel::$title_d,
    		GoodsModel::$weight_d,
    		GoodsModel::$expressId_d,
    	];
    	$data = $this->getDataByOtherModel($field, GoodsModel::$id_d);
    	return $data;
    }
    
    
    /**
     * 获取商品数据并缓存（购物车立即购买用）
     * @return array
     */
    public function getGoodsByOtherDataCache() :array
    {
    	$userId = SessionGet::getInstance('user_id')->get();
    	
    	$key = md5(implode(',', array_keys($this->data)).'goods_cart'.$userId);
    	
    	$cache = Cache::getInstance('', ['expire' => 45]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getGoodsByOtherData();
    	
    	if (empty($data)) {
    		$this->errorMessage = '找不到对应的商品';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 
     * @return array
     */
    public function getParseDataByOrder() :array
    {
    	$goodsData = $this->getGoodsForData();
    	
    	if (empty($goodsData)) {
    		return [];
    	}
    	
    	foreach ($goodsData as & $value) {
    		$value['goods_num'] = '1';
    	}
    	
    	return $goodsData;
    }
    
   /**
    * 套餐购物车购买兼容支付数据
    * @return array
    */
   public function getParseDataCartByOrder() :array
   {
     $goodsData = $this->getGoodsForData();

     if (empty($goodsData)) {
      return [];
  }

  foreach ($goodsData as & $value) {
      $value['goods_num'] = $value['package_num'];
  }

  return $goodsData;
}


    /**
     * 获取猜你喜欢商品（未登录时猜你喜欢）
     * @return array
     */
    public function getGuessLikeGoods() :array
    {
    	if (!isset($_COOKIE['brand_id'])) {
    		return [];
    	}

    	$brandIdArray = json_decode($_COOKIE['brand_id'], true);
    	
    	$classIdArray = json_decode($_COOKIE['class_id'], true);
    	
    	$minBrandId = min($brandIdArray);
    	
    	$maxBrandId = max($brandIdArray);
    	
    	$minClassId = min($classIdArray);
    	
    	$maxClassId = max($classIdArray);
    	
    	$field = implode(',', $this->getTableColum());
    	
    	$pageSize = 9;
    	
    	$tableName = $this->modelObj->getTableName();
    	
    	//当前页之前还有多少 
    	
    	$pageNumber = ($this->data['page'] - 1) * $pageSize;
    	
    	$data = $this->modelObj
      ->field($field)
      ->where('('.GoodsModel::$brandId_d.' between :min_g and :max_g) or ('.GoodsModel::$classTwo_d.' between :min_c and :max_c)')
      ->bind([':min_g' => $minBrandId, ':max_g' => $maxBrandId, ':min_c' => $minClassId, ':max_c' => $maxClassId])
      ->order(GoodsModel::$salesSum_d. ' DESC ')
      ->limit($pageNumber, $pageSize)
      ->select();

      if (empty($data)) {
          return [];
      }

      foreach ($data as $key => $value) {
          if ($value[GoodsModel::$pId_d] == 0) {
           unset($data[$key]);
       }
   }

   if (empty($data)) {
      return [];
  }
  $goods = [];


  $compare = false;

    	//只显示 同类商品销量最高的一个
  foreach ($data as $key => $value) {

      if (isset($goods[$value[GoodsModel::$pId_d]])) {

       $compare = $goods[$value[GoodsModel::$pId_d]][GoodsModel::$salesSum_d] < $value[GoodsModel::$salesSum_d];

    			//大于
       if ($compare === true) {
        $goods[$value[GoodsModel::$pId_d]] = $value;
    }

}else {
   $goods[$value[GoodsModel::$pId_d]] = $value;
}
}
return array_values($goods);
}

    /**
     * 根据评分获取商品
     */
    public function getGoodsByScore() :array
    {
    	$goods = $this->data['goods'];
    	
    	$goodsIdArray = explode('_', implode('_', array_keys($goods)));
    	
    	
    	$goodsIdString = implode(',', array_unique($goodsIdArray));
    	
    	$pageSize = 9;
    	
    	$args = $this->data['args'];
    	
    	//当前页之前还有多少
    	$pageNumber = ($args['page'] - 1) * $pageSize;
    	
    	$data = $this->modelObj
      ->field($this->getTableColum())
      ->where(GoodsModel::$id_d .' in (%s) and '.GoodsModel::$approvalStatus_d.' = 1 and '.GoodsModel::$shelves_d.' = 1', $goodsIdString)
      ->order(GoodsModel::$salesSum_d.' DESC ')
      ->limit($pageNumber, $pageSize)
      ->select();
      return $data;
  }

    /**
     * 获取推荐商品并缓存
     */
    public function getGoodsByScoreCache() :array
    {
    	$key = $this->data['page'].md5(json_encode($this->data['goods'], JSON_UNESCAPED_UNICODE));
    	
    	$cache = Cache::getInstance('', ['expire' => 800]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getGoodsByScore();
    	if (empty($data)) {
    		return [];
    	}
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 最佳配件商品立即购买
     */
    public function bestAccessoriesImmediatePurchase() :array
    {
    	$id = $this->data['goods_id'];
    	
    	$field = $this->getTableColum();
    	
    	$data = $this->modelObj
      ->field($field)
      ->where(GoodsModel::$id_d.' in (%s) and '.GoodsModel::$approvalStatus_d.' = 1 and '.GoodsModel::$shelves_d.' = 1', $id)
      ->select();

      if (empty($data)) {
          $this->errorMessage = '商品数据错误';
          return [];
      }

      foreach ($data as & $value) {
          $value['goods_num'] = '1';

    		//兼容订单商品添加
          $value['goods_id'] = $value[GoodsModel::$id_d];
      }

      return $data;

  }

    /**
     * 最佳配件商品立即购买缓存
     */
    public function bestAccessoriesImmediatePurchaseCache() :array
    {
    	$key = md5($this->data['goods_id'].'_goods_id');
    	
    	$cache = Cache::getInstance('', ['expire' => 90]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->bestAccessoriesImmediatePurchase();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
}