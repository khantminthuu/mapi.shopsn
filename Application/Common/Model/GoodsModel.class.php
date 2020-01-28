<?php
namespace Common\Model;
use Common\Model\GoodsImagesModel;
use Common\Model\SpecGoodsPriceModel;
use Common\Model\CommonModel;
use Think\SessionGet;

/**
 * 模型
 */
class GoodsModel extends BaseModel
{

    private static $obj;

	public static $id_d;	//主键编号

	public static $brandId_d;	//品牌【编号】

	public static $title_d;	//商品标题

	public static $priceMarket_d;	//市场价

	public static $priceMember_d;	//会员价

	public static $stock_d;	//库存

	public static $selling_d;	//是否是热销   0 不是   1 是

	public static $shelves_d;	//是否上架【0下架，1表示选择上架】

	public static $classId_d;	//商品分类ID

	public static $recommend_d;	//是否推荐【1推荐 0不推荐】

	public static $code_d;	//商品货号

	public static $top_d;	//顶部推荐

	public static $seasonHot_d;	//当季热卖

	public static $description_d;	//商品简介

	public static $updateTime_d;	//最后一次编辑时间

	public static $createTime_d;	//创建时间

	public static $goodsType_d;	//商品类型

	public static $sort_d;	//排序

	public static $pId_d;	//父级产品 SPU

	public static $status_d;	//促销活动【0没有活动，1尾货清仓，3积分商城,5抢购, 6团购】

	public static $commentMember_d;	//评论次数

	public static $salesSum_d;	//商品销量

	public static $attrType_d;	//商品属性编号【为goods_type表中数据】

	public static $extend_d;	//扩展分类

	public static $advanceDate_d;	//预售日期

	public static $weight_d;	//重量

	public static $storeId_d;	//店铺【编号】

	public static $type_d;	//店铺商品类型【0个人，1公司，2自营】

	public static $approvalStatus_d;	//审核状态【0未审核， 1审核通过， 2审核失败】

	public static $classTwo_d;	//二级分类【编号】

	public static $classThree_d;	//三级分类【编号】


	public static $expressId_d;	//运费模板编号


	public static $picUrl_d;	//商品规格预览图

	public static $storeClass_one_d;	//店内一级分类

	public static $storeClass_two_d;	//店内二级分类

	public static $storeClass_three_d;	//店内三级分类

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     * 获取店铺下商品个数
     *
     */
    public function getShopGoodNumber($storeId ){
    	$where['p_id'] = ['GT', 0];
        $where['store_id'] = $storeId;
        $where['shelves'] = 1;
        $where['approval_status'] = 1;
        return $this->where($where)->count();
    }

    public function getShopDiscountNumber($storeId ){
        $where['p_id'] = ['GT', 0];
        $where['store_id'] = $storeId;
        $where['price_market'] = 100;
        return $this->where($where)->count();
    }




    /**
     * Get recommended product details
     *
     */
    public function getCopmboGoodInfo($goodId){
        $where['id'] = $goodId;
        $field = 'title,price_member';
        return $this->where($where)->field($field)->find();
    }

    /**
     * 获取可能喜欢的产品
     *
     */
    public function getMaybeLoveGoods(){
        $goods_images_model=new GoodsImagesModel();
        $field = "db_goods.id,title,price_market,p_id";
        $maybe_love=$this
            ->where('`p_id`!=0 and shelves = 1 and approval_status = 1 and status = 0')
            ->field($field)
            ->order('rand()')
            ->limit(3)
            ->group('p_id')
            ->select();
        foreach($maybe_love as $k=>$vo)
        {
            $fatherId=$vo['p_id'];
            $fatherImg=$goods_images_model->getgoodOneImage($fatherId);
            $maybe_love[$k]['pic_url']=$fatherImg['pic_url'];
        }
        return $maybe_love;
    }

    /**
     * 更改商品的销售数量-在支付完成以后
     *
     */
    public function changeGoodSalesSum($goodId,$goodNumber){
        $where['id'] = $goodId;
        $result = $this->where()->setInc('sales_sum',$goodNumber);
        if ($result){
            return true;
        }
        return false;
    }

    /**
     * 得到店铺的全部宝贝数量和其中的三个商品
     *
     */
    public function getStoreGoods($storeId,$number,$classId = 0){
        $data = [];
        if (empty($classId)){
            $where = [
                'approval_status' => '1',
                'shelves' => '1',
                'store_id' => $storeId,
                'top' =>'1',
                'p_id' => ['NEQ', '0'],
            ];
        }else{
            $where = [
                'approval_status' => '1',
                'shelves' => '1',
                'store_id' => $storeId,
                'class_id' => $classId,
                'top' =>'1',
                'p_id' => ['NEQ', '0'],
            ];
        }
        
        $userId = SessionGet::getInstance('user_id')->get();
        
        if (!$userId) {
            $field = 'id,price_market as goods_price,p_id';
        }else{
            $field = 'id,price_member as goods_price,p_id';
        }
        $goodImage = new GoodsImagesModel();
        $data['good_number'] = $this->where($where)->count('id');
        $data['store_goods'] = $this->where($where)->field($field)->limit($number)->select();
        foreach ($data['store_goods'] as $key=>$value){
            $data['store_goods'][$key]['pic_url'] = M('GoodsImages')->where(['goods_id'=>$value['p_id'],"is_thumb"=>1])->getField('pic_url');
        }
        return $data;
    }

    /**
     * 得到首页的推荐商品
     *
     */
    public function getIndexGoods($classId){
        $goodsImage = new GoodsImagesModel();
        $where['p_id'] = ['NEQ', '0'];
        $where['class_id'] = $classId;
        $where['approval_status'] = 1;
        $userId = SessionGet::getInstance('user_id')->get();
        if (!$userId) {
            $field = 'id,price_market as goods_price,title,p_id';
        }else{
            $field = 'id,price_member as goods_price,title,p_id';
        }
        $data =  $this->where($where)->field($field)->limit(4)->order("
            sort")->select();
        foreach ($data as $key => $value){
            $data[$key]['image'] = $goodsImage->getgoodOneImage($value['p_id'])['pic_url'];
        }
        return $data;
    }
    /**
     * 得到商品的标题
     *
     */
    public function getGoodTitle($goodsId){
        $goodsImage = new GoodsImagesModel();
        $where = [
            'id' => $goodsId,
        ];
        $field = 'title,price_market,store_id,p_id';
        $info = $this->where($where)->field($field)->find();
        $data['title'] = $info['title'];
        $data['store_id'] = $info['store_id'];
        $data['goods_id'] = $goodsId;
        $data['image'] = $goodsImage->getgoodOneImage($info['p_id'])['pic_url'];
        return $data;
    }
    /**
     * 得到店铺新上的商品
     *
     */
    public function getNewGoodsOfStore($id){
        $goodsImage = new GoodsImagesModel();
        $start=strtotime('today');
        $new_end = strtotime(date("Y-m-d H:i:s", $start)."+1 day");
        $new_start = strtotime(date("Y-m-d H:i:s", $start)."-1 month");
        $where = [
            'create_time' =>['between',[$new_start, $new_end]],
            'approval_status' =>'1',
            'shelves' =>'1',
            'store_id' =>$id,
            'p_id' =>0,
        ];
        $field = 'id,store_id,price_member,FROM_UNIXTIME(create_time,\'%Y-%m-%d\') as create_time,title,p_id';
        $data =  $this->where($where)->field($field)->limit(6)->select();
        if (empty($data)) {
            $c_where = [
                'approval_status' =>'1',
                'shelves' =>'1',
                'store_id' =>$id,
                'p_id' =>0,
            ];
            $data =  $this->where($c_where)->field($field)->limit(6)->select();
        }
        foreach ($data as $key => $value){
            $data[$key]['pic_url'] = $goodsImage->getgoodOneImage($value['id'])['pic_url'];
        }
        // 得到所有的新上的商品
        $data_times = [];
        $data_time = [];
        foreach ($data as $k => $v){
            $data_time[$v['create_time']]['create_time'] = $v['create_time'];
            $data_time[$v['create_time']]['goods'][] = $v;
            
        }
        $data_times = array_values($data_time);
        
        $datas['allNewsGoods'] = $data_times;
        $n_where = [
            'create_time' =>['between',[$new_start, $new_end]],
            'approval_status' =>'1',
            'shelves' =>'1',
            'recommend'=>1,
            'store_id' =>$id,
            'p_id' =>0,
        ];
        $newOne = $this->where($n_where)->field($field)->find();
        if (empty($newOne)) {
            $new_where = [
                'approval_status' =>'1',
                'shelves' =>'1',
                'recommend'=>1,
                'store_id' =>$id,
                'p_id' =>0,
            ];
            $newOne = $this->where($new_where)->field($field)->find();
        }
        $newOne['pic_url'] = $goodsImage->getgoodOneImage($newOne['id'])['pic_url'];
        $datas['newOne'] = $newOne;
        //得到店铺搞活动的商品
        $datas['acticityGoods'] = CommonModel::panic()->getStoreActivityGoods($id);
        return $datas;
    }
    /**
     * 得到具有某个商品的店铺ID
     *
     */
    public function get_class_store($classId){
        $where[self::$classId_d] = $classId;
        $where[self::$approvalStatus_d] = 1;
        $field = 'store_id';
        $data = $this->where($where)->field($field)->group('store_id')->select();
        return $data;

    }
    //获取商品
    public function getGoodsByWhere($field,$where,$page,$order){
        $count = $this->where($where)->count();
        $data = $this->field($field)->where($where)->order($order)->page($page.",10")->select();
              
        $Page = new \Think\Page($count,10);
        $page = $Page->show();
        $totalPages =$Page->totalPages;
        return array("data"=>$data,"count"=>$count,"page_size"=>10,'totalPages'=>$totalPages); 
    }
    /**
     * 得到店铺的全部宝贝数量和其中的三个商品
     * @author 王波
     */
    public function getGoodsByStore($store){
    	
    	$userId = SessionGet::getInstance('user_id')->get();
    	if (!$userId) {
            $field = "id as goods_id,p_id,price_market as goods_price,title,sales_sum,comment_member";
        }else{
            $field = "id as goods_id,p_id,price_member as goods_price,title,sales_sum,comment_member";
        }
        foreach ($store as $key => $value) {
            $where['store_id'] = $value['id'];
            $where['shelves']  = 1;
            $where['approval_status'] = 1;
            $where['p_id'] = array("NEQ",0);
            $store[$key]['good_number'] = $this->where($where)->count();
            $store[$key]['store_goods'] = $this->field($field)->where($where)->order("sort DESC")->limit(3)->select();
            foreach ($store[$key]['store_goods'] as $k => $v) {
                if ($v['p_id' ==0]) {
                   $img['goods_id'] = $v['goods_id'];
                }else{
                   $img['goods_id'] = $v['p_id'];
                }
                $img['is_thumb'] = '1';
                $store[$key]['store_goods'][$k]['pic_url'] = M('GoodsImages')->where($img)->getField('pic_url');
            }
        }
        return $store;
    }
    //获取商品名(三维数组)
    public function getTitleByArray($goods){
        if (empty($goods)) {
            return "";
        }
        $goodsImage = new GoodsImagesModel();
        $spec = new SpecGoodsPriceModel();
        $result = [];
        foreach ($goods as $k => $v) {
            foreach ($v['goods'] as $key => $value) {
                $where['id'] = $value['goods_id'];
                $result = $this->field('id,title,p_id,price_member')->where($where)->find();
                $shelves = $this->where(['id'=>$result['p_id']])->getField('shelves');
                $goods[$k]['goods'][$key]['title'] = $result['title'];
                $goods[$k]['goods'][$key]['price_new'] = $result['price_member'];
                $goods[$k]['goods'][$key]['shelves'] = $shelves;
                if ($result['p_id'] == 0) {
                     $goods[$k]['goods'][$key]['puc_url'] = $goodsImage->getgoodOneImage($value['goods_id'])['pic_url'];
                }else{
                	$goods[$k]['goods'][$key]['puc_url'] = $goodsImage->getgoodOneImage($result['p_id'])['pic_url'];
                } 
                $goods[$k]['goods'][$key]['space'] = $spec->getGoodSpe($value['goods_id']); 
            }
        }
        return $goods;
    }
    //获取商品名(二维数组)
    public function getTitleByTwo($goods){
        if (empty($goods)) {
            return "";
        }
        $goodsImage = new GoodsImagesModel();
        $spec = new SpecGoodsPriceModel();
        
        foreach ($goods as $key => $value) {
            $where['id'] = $value['goods_id'];
            $Goods = $this->field('id,title,p_id,price_member')->where($where)->find();
            $goods[$key]['title'] = $Goods['title'];
            $goods[$key]['price_member'] = $Goods['price_member'];
            if ($Goods['p_id'] == 0) { 
                 $goods[$key]['puc_url'] = $goodsImage->getgoodOneImage($value['goods_id'])['pic_url'];
            }else{
                $goods[$key]['puc_url'] = $goodsImage->getgoodOneImage($Goods['p_id'])['pic_url'];
            } 
            $goods[$key]['space'] = $spec->getGoodSpe($value['goods_id']); 
        }
       
        return $goods;
    }
    //获取商品名(一维数组)
    public function getTitleByOne($data){
        if (empty($data)) {
            return "";
        }
        $goodsImage = new GoodsImagesModel();
        $spec = new SpecGoodsPriceModel();
        $userId = SessionGet::getInstance('user_id')->get();
        
        if (!$userId) {
            $field  = 'id,title,p_id,store_id,price_market as goods_price, express_id, weight,store_id';
        }else{
            $field  = 'id,title,p_id,store_id,price_member as goods_price, express_id, weight,store_id';
        }
        $where['id'] = $data['goods_id'];
        $goods = $this->field($field)->where($where)->find();
        
        if (empty($goods)) {
        	return [];
        }
        
        if ($goods['p_id'] == 0) {
             $goods['pic_url'] = $goodsImage->getgoodOneImage($goods['id'])['pic_url'];
        }else{
        	$goods['pic_url'] = $goodsImage->getgoodOneImage($goods['p_id'])['pic_url'];
        } 
        $goods['space'] = $spec->getGoodSpe($goods['id']); 
       
       
        return $goods;
    }
    //获取商品名(一维数组)
    public function getTitleByOneData($data){
        if (empty($data)) {
            return "";
        }
        $goodsImage = new GoodsImagesModel();
        $spec = new SpecGoodsPriceModel();
        $userId = SessionGet::getInstance('user_id')->get();
        if (!$userId) {
            $field  = 'id,title,p_id,store_id,price_market as goods_price, express_id, weight,store_id';
        }else{
            $field  = 'id,title,p_id,store_id,price_member as goods_price, express_id, weight,store_id';
        }
        $where['id'] = $data['goods_id'];
        $goods = $this->field($field)->where($where)->find();
        
        if (empty($goods)) {
            return $data;
        }
        
        if ($goods['p_id'] == 0) {
             $data['pic_url'] = $goodsImage->getgoodOneImage($goods['id'])['pic_url'];
        }else{
            $data['pic_url'] = $goodsImage->getgoodOneImage($goods['p_id'])['pic_url'];
        } 
        $data['space'] = $spec->getGoodSpe($goods['id']); 
        $data['title'] = $goods['title'];
        return $data;
    }
    /*
        khantminthu
    */
    public function getGoodsDetail($id){
        // $field = 'id,title,pId,storeId,stock,expressId,weight,description,priceMarket,priceMember,commentMember,salesSum,brandId,classTwo,shelves,picUrl';
        $field = 'id,title,p_id,store_id,stock,express_id,weight,description,price_market,price_member,comment_member,sales_sum,brand_id,class_two,shelves,pic_url';
        $where['id'] = $id['id'];
        $where['shelves'] = 1;
        $where['approval_status'] = 1;
        $getGoodsArr = $this->where($where)->field($field)->find();
        return $getGoodsArr;
    }
}