<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\StoreModel;
use Common\Model\GoodsModel;
use Common\Model\GoodsImagesModel;
use Common\Model\UserModel;
use Common\Model\AlbumClassModel;
use Common\Model\StoreFollowModel;
use Common\Model\CommonModel;
use Common\Model\StoreEvaluateModel;
use Common\Model\StoreAddressModel;
use Common\Model\StoreJoinCompanyModel;
use Common\Model\StorePersonModel;
use Common\Model\StoreAdvModel;
use Think\Log;
use Think\Cache;
use Think\SessionGet;

/**
 * 商铺逻辑处理层
 *
 */
class StoreLogic extends AbstractGetDataLogic
{
	/**
	 * 店铺Id
	 * @var string
	 */
	private $storeId = '';
	
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
        $this->modelObj = new StoreModel();
        $this->goodModel = new GoodsModel();
        $this->storeFollowModel = new StoreFollowModel();
        $this->storeEvaluateModel = new StoreEvaluateModel();
        $this->storeAddressModel = new StoreAddressModel();
        $this->storeJoinCompanyModel = new StoreJoinCompanyModel();
        $this->storePerson = new StorePersonModel();
        $this->goodsImagesModel = new GoodsImagesModel();
    }
    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'id' => ['number' => '必须传入店铺ID']
        ];
        return $message;
    }

    public function getValidateByClassId()
    {
        $message = [
            'classId' => [
                'required' => '商品分类ID必传',
                'number' => '商品分类ID必须是数字',
            ]
        ];
        return $message;
    }
    /**
     * 获取结果
     */
    public function getResult()
    {
    	$data = $this->data;
    	
    	$cache = Cache::getInstance('', ['expire' => 160]);
    	
    	$key = md5(implode(',', array_keys($data)).'storeinfo'.SessionGet::getInstance('user_id')->get());
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getStoreInforByOtherData();
    	
    	if (empty($data)) {
    		$this->errorMessage = '商户信息错误';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
	
    /**
     * 根据其他数据获取店铺数据
     * @return array
     */
    public function getStoreInforByOtherData()
    {
		
    	$idString = implode(',', array_column($this->data, $this->splitKey));
    	
    	$field = StoreModel::$id_d.','.StoreModel::$shopName_d.','.StoreModel::$storeLogo_d;
    	
    	$data = $this->modelObj->field($field)->where(StoreModel::$id_d. ' in (%s)', $idString)->select();
    	
    	return $data;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName() :string
    {
        return StoreModel::class;
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
            UserModel::$userName_d,
        ];
    }
    /**
     * 获取商品归属店铺基本信息
     *
     */
    public function getShopInfo(){
        $this->searchTemporary = [
            StoreModel::$id_d => $this->data['id'],
        ];
        $retData = parent::getFindOne();
        //获取店铺所有宝贝数量
        $retData['goodsNumber'] = $this->goodModel->getShopGoodNumber($this->data['id']);
        if (empty($retData)){
            $this->errorMessage = '暂无数据';
            return [];
        }
        return $retData;
    }
	
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	return [
    		'id', 'shop_name', 'store_collect', 'store_logo','description'
    	];
    }
	
    /**
     * 得到首页店铺列表
     *
     */
    public function getStoreList(){

        if($this->data["sort_types"] == "store_sales"){

            $Order = 'store_sales  DESC';

        }elseif ($this->data["sort_types"] == "grade_id"){

            $Order = 'grade_id  DESC';

        }else{

            $Order = 'store_sort  DESC';
        }
        if (empty($this->data["class_id"])) {
            $searchTemporary = [

            'store_state' => 1

        ];
        }else{
            $searchTemporary = [

                'store_state' => 1,
                'class_id' =>$this->data["class_id"]

            ];
        }
        

        $searchField = 'id,shop_name,store_sales,store_logo';
        $page = empty($this->data['page'])?0:$this->data['page'];
        $reData['records'] = $this->modelObj->field($searchField)->where($searchTemporary)->page($page.',10')->order($Order)->select();
        $count =  $this->modelObj->where($searchTemporary)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        // showData($reData);exit;
        //得到店铺下的三个商品
        foreach ($reData['records'] as $key => $value){
            //得到店铺全部宝贝和其中的三个商品
            $goods = $this->goodModel->getStoreGoods($value['id'],3);
            $reData['records'][$key]['good_number'] = $goods['good_number'];
            $reData['records'][$key]['store_goods'] = $goods['store_goods'];
        }
        // $datas = $this->handelData($reData);
        $reData['count'] = $count;
        $reData['totalPages'] = $totalPages;
        $reData['page_size'] = 10;
        return $reData;
    }

    /**
     * 得到商品界面店铺列表
     *
     */
    public function getGoodsStoreList(){

        $this->searchTemporary = [

            'store_state' => 1

        ];

        $this->searchField = 'id,shop_name,store_sales';

        $reData = parent::getDataList();
        //得到店铺下的三个商品
        foreach ($reData['records'] as $key => $value){
            //得到店铺全部宝贝和其中的三个商品
            $goods = $this->goodModel->getStoreGoods($value['id'],3,$this->data['goodClass_id']);
            $reData['records'][$key]['good_number'] = $goods['good_number'];
            $reData['records'][$key]['store_goods'] = $goods['store_goods'];
        }
        $datas = $this->handelData($reData);

        return $datas;
    }

    /**
     * 获取店铺首页
     *
     */
    function getStoreInfo(){
        $storeId = $this->data['id'];
        $data = [];
        //店铺ID
        $data['store_id'] = $this->data['id'];
        //获取店铺的基本信息   logo图  店铺名称
        $storeInfo = $this->modelObj->getstoreInfo($this->data['id']);
        //获取用户是否关注了店铺
        $if_atten = CommonModel::store_follow()->ifAttenStore(session("user_id"),$this->data['id']);
        if($if_atten == false){
            $data['if_atten'] = 0;
        }else{
            $data['if_atten'] = 1;
        }
        $data['storeInfo'] = $storeInfo;
        //获取店铺粉丝数
        $data['storeFans'] = $this->storeFollowModel->getFansNumber($this->data['id']);
        
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    /**
     * 获取店铺详情
     *
     */
    function getShopDetails(){
        $data['store_id'] = $this->data['id'];
        //获取店铺的基本信息   logo图  店铺名称 根据用户来查询联系方式   店铺简介  开店时间 所在区域
        $storeInfo = $this->modelObj->getstoreInfo($this->data['id']);
        if (empty($storeInfo)) {
            return array("status"=>1,"message"=>'暂无店铺数据',"data"=>'');
        }
        $data['shop_name'] = $storeInfo['shop_name'];
        $data['store_logo'] = $storeInfo['store_logo'];
        $data['description'] = $storeInfo['description'];
        $data['update_time'] = $storeInfo['update_time'];//开店时间 
        // 获取地址
        $data['address'] = $this->storeAddressModel->getAddress($storeInfo['store_address']);
        
        //是否关注了店铺
        $if_atten = $this->storeFollowModel->ifAttenStore(SessionGet::getInstance('user_id')->get(),$this->data['id']);
        
        if($if_atten == false){
            $data['if_atten'] = 0;
        }else{
            $data['if_atten'] = 1;
        }

        //获取店铺粉丝数
        $data['storeFans'] = $this->storeFollowModel->getFansNumber($this->data['id']);

        //店铺评分    描述相符评分 desccredit   服务态度评分servicecredit  
        ////描述相符评分 
        $data['desccredit'] = $this->storeEvaluateModel->getDesccredit($this->data['id'],'desccredit');
        $desccredit = $this->storeEvaluateModel->getDesccreditAll('desccredit');
        $data['desccredit_discraption'] = $this->calculationScoreAll($data['desccredit'],$desccredit);
        //服务态度评分
        $data['servicecredit'] = $this->storeEvaluateModel->getDesccredit($this->data['id'],'servicecredit');
        $servicecredit = $this->storeEvaluateModel->getDesccreditAll('servicecredit');
        $data['servicecredit_discraption'] = $this->calculationScoreAll($data['servicecredit'],$servicecredit);
        //发货速度评分
        $data['deliverycredit'] = $this->storeEvaluateModel->getDesccredit($this->data['id'],'deliverycredit');
        $deliverycredit = $this->storeEvaluateModel->getDesccreditAll('deliverycredit');
        $data['deliverycredit_discraption'] = $this->calculationScoreAll($data['deliverycredit'],$deliverycredit);
        // 获取店铺的基本信息  0 代表个人入住  1 代表企业入住  获取联系方式  店铺二维码 证照信息
        if ($storeInfo['type'] == 1){
            // 获取企业入驻信息
            $data['mobile'] = $this->storeJoinCompanyModel->getSoreJionCompany($storeInfo['user_id'])['mobile'];
        }else{
            $data['mobile'] = $this->storePerson->getSoreJionPerson($storeInfo['user_id'])['mobile'];
        }
        return  array('status'=>1,"message"=>"获取成功","data"=>$data);
    }
    /**
     * 获取店铺的粉丝数和会员是否已经关注店铺
     *
     */
    public function get_store_fans(){
        $store_id  = $this->data['id'];
        $user_id  = session('user_id');
        $if_atten = CommonModel::store_follow()->ifAttenStore($user_id,$store_id);
        if($if_atten == false){
            $data['if_atten'] = 0;
        }else{
            $data['if_atten'] = 1;
        }
        $data['storeFans'] = $this->storeFollowModel->getFansNumber($store_id);
        return $data;
    }

    public function calculationScore($data){
        $score = explode('.',$data);
        return  "高于同行".  round($score[1]/2/$score[0],2)."%";
    }
    public function calculationScoreAll($score,$scoreAll){
        if ($scoreAll == 0) {
            return  "高于同行0.00%";
        }
        $a_score = $score-$scoreAll;
        if ($a_score<=0) {
           return  "高于同行0.00%";
        }
        return  "高于同行".  round($a_score/$scoreAll,2)."%";
    }
    

    /**
     * 处理返回数据
     *
     */
    function handelData($data){
        $i = 0;
        $allData = $data['records'];
        unset($data['records']);
        foreach ($allData as $key => $value){
            if (!empty($value['store_goods'])){
                $data['records'][$i] = $allData[$key];
                $i ++;
            }
        }
        $data['countTotal'] = $i;
        return $data;
    }

    /**
     * 获取店铺最新动态最近一个月的
     *
     */
    public function getStoreDynamic(){
        //查询店铺当前有没有新上的商品
        $retData = CommonModel::good_model()->getNewGoodsOfStore($this->data['id']);
        return $retData;
    }
    /**
     * 获取店铺最新动态最近一个月的
     *
     */
    public function detaliTime($datas){
        foreach($datas['records'] as $k=>$v){
            $datas['records'][$v['create_time']][]=$v;
            unset($datas['records'][$k]);
        }
        $i = 0;
        foreach ($datas['records'] as $key => $value){
            $datas['records'][$i] = $datas['records'][$key];
            unset($datas['records'][$key]);
            $i ++;
        }
        return $datas;
    }
    /**
     * 获取具有某个商品分类的店铺
     *
     */
    public function class_store(){
        $classId = $this->data["classId"];
        $storeId = CommonModel::get_modle("Goods")->get_class_store($classId);
        $storeIdArray = [];
        foreach ($storeId as $key => $value){
            array_push($storeIdArray,$value['store_id']);
        }

        if($this->data["sort_types"] == "store_sales"){

            $this->searchOrder = 'store_sales  DESC';

        }elseif ($this->data["sort_types"] == "grade_id"){

            $this->searchOrder = 'grade_id  DESC';

        }else{
            $this->searchOrder = 'store_sort  DESC';
        }
        $this->searchTemporary = [
            'store_state' => 1,
            'id' => ['in',$storeIdArray],
        ];

        $this->searchField = 'id,shop_name,store_sales';

        $reData = parent::getDataList();
        //得到店铺下的三个商品
        foreach ($reData['records'] as $key => $value){
            //得到店铺全部宝贝和其中的三个商品
            $goods = $this->goodModel->getStoreGoods($value['id'],3);
            $reData['records'][$key]['good_number'] = $goods['good_number'];
            $reData['records'][$key]['store_goods'] = $goods['store_goods'];
        }
        $datas = $this->handelData($reData);

        return $datas;
    }

    //获取店铺所有商品
    public function getStoreGoodsAll(){
         $post = $this->data;
        if (!empty($post['sort'])){
            $flag = $post['sort'];
            switch ($flag) {
                case 1:$order = 'sales_sum DESC';break;//销量由高到低
                case 2:$order = 'sales_sum ASC';break;//销量由低到高
                case 3:$order = 'price_market DESC';break;//价格由高到低
                case 4:$order = 'price_market ASC';break;//价格由低到高
                case 5:$order = 'sales_sum DESC';break;
                case 6:$order = 'sales_sum ASC';break;
            }
        }
        $page = empty($post['page'])?0:$post['page'];
        if (!empty($post['title'])) {
            $title = $post['title'];
            $where['title'] = array('like','%'.$title.'%');
        }
        if (!empty($post['class_id'])) {
            $where['class_three'] = $post['class_id'];
        }
        $where['store_id'] = $post['id'];
        $where['p_id']  = 0;
        
        $where['approval_status']  = '1';
        
        $where['status'] = '0';
        
        $where['shelves'] = '1';
        
        if (empty(SessionGet::getInstance('user_id')->get())) {
            $field = "id,p_id,price_market as goods_price,title,sales_sum,comment_member";
        }else{
            $field = "id,p_id,price_member as goods_price,title,sales_sum,comment_member";
        }
        $goods = $this->goodModel->getGoodsByWhere($field,$where,$page,$order);
        if (!empty($goods['data'])) {
            $goods['data'] = $this->goodsImagesModel->getgoodImageByGoods($goods['data']);
            return array("status"=>1,"message"=>"获取成功","data"=>$goods);
        }else{
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
    }
    /**
     * 更新店铺销量
     */
    public function updateSale()
    {
    	if (empty($this->data)) {
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	$sql = $this->buildUpdateSql();
    	try {
    		$status = $this->modelObj->execute($sql);
    		
    		return $status;
    	} catch (\Exception $e) {
    		$this->errorMessage = $e->getMessage();
    		
    		$day = date('y_m_d');
    		
    		Log::write($sql.'--'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/goods_del_stock_'.$day.'.txt');
    		
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	return false;
    }
    /**
     * 更新店铺销量
     */
    public function updateSales()
    {
        if (empty($this->data)) {
            $this->modelObj->rollback();
            return false;
        }
        $orderModel = new OrderModel();
        $where['id'] = array("IN",$this->data);
        $order = $orderModel->field('store_id')->where($where)->select();
        $this->data = $order;
        $sql = $this->buildUpdateSql();
        try {
            $status = $this->modelObj->execute($sql);

            return $status;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();

            $day = date('y_m_d');

            Log::write($sql.'--'.date('Y-m-d H:i:s'), Log::ERR, '', './Log/order/goods_del_stock_'.$day.'.txt');

            $this->modelObj->rollback();
            return false;
        }

        return false;
    }
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated() :array
    {
    	return [
    		StoreModel::$storeSales_d
    	];
    }
    
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated() :array
    {
    	$data = $this->data;
    	
    	$tmp = [];
    	
    	foreach ($data as $store => $value) {
    		$tmp[$value['store_id']][] = StoreModel::$storeSales_d. ' + 1';
    	}
    	
    	
    	return $tmp;
    }
    //获取店铺证照信息
    public function getShopLicense(){
        $post = $this->data;
        $store = $this->modelObj->field("shop_name,user_id,type")->where(['id'=>$post['id']])->find();

        if ($store['type'] == 0) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $where['user_id'] = $store['user_id'];
        $where['store_name'] = $store['shop_name'];
        $data = $this->storeJoinCompanyModel->field("company_name,license_number,name,registered_capital,store_address,scope_of_operation,validity_start,validity_end,mobile")->where($where)->find();
        
        if (empty($data)) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $address = $this->storeAddressModel->field("prov_id,city,dist,address")->where(['id'=>$data['store_address']])->find();
        $prov = M("Region")->where(['id'=>$address['prov_id']])->getField("name");
        $city = M("Region")->where(['id'=>$address['city']])->getField("name");
        $dist = M("Region")->where(['id'=>$address['dist']])->getField("name");
        $data['address'] = $prov.'-'.$city.'-'.$dist.'-'.$address['address'];
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    
    /**
     * 获取单个店铺的信息
     * @return array
     */
    public function getInfo() :array
    {
    	$cackeKey = 'store_'.$this->data[$this->splitKey];
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($cackeKey);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj
    		->field(StoreModel::$shopName_d.','.StoreModel::$storeLogo_d.', '.StoreModel::$id_d)
	    	->where(StoreModel::$id_d.'=:id')
	    	->bind([':id' => $this->data[$this->splitKey]])
	    	->find();
    	
    	if (empty($data)) {
    		$this->errorMessage = '商铺信息错误';
    		return [];
    	}
    	
    	$ss = $cache->set($cackeKey, $data);
    	
    	return $data;
    }
    
    /**
     * 获取店铺信息
     */
    protected function getStoreIdString() :string
    {
    	if ($this->storeId !== '') {
    		return $this->storeId;
    	}
    	
    	$this->storeId = implode(',', array_unique(array_column($this->data, $this->splitKey)));
    	
    	return $this->storeId;
    }
    
    /**
     * 获取店铺信息
     */
    public function getStoreInfoByStoreIdString() :array
    {
    	
    	$idString = $this->getStoreIdString();
    
    	$data = $this->modelObj
	    	->field(StoreModel::$shopName_d.','.StoreModel::$storeLogo_d.', '.StoreModel::$id_d)
	    	->where(StoreModel::$id_d.' in (%s)', $idString)
	    	->select();
    	
    	return $data;
    }
    
    /**
     * 获取店铺信息
     */
    public function getStoreInfoByStoreIdStringCache() :array
    {
    	$key = $this->getStoreIdString();
    	
    	$key = md5($key.'_what_store');
    	
    	$cache = Cache::getInstance('', ['expire' => 90]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getStoreInfoByStoreIdString();
    	
    	if (empty($data)) {
    		$this->errorMessage = '没有店铺信息';
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
}
