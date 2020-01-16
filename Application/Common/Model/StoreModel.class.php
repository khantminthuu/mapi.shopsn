<?php
namespace Common\Model;


/**
 * 店铺模型
 */
class StoreModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//主键编号

	public static $shopName_d;	//店铺名称

	public static $classId_d;	//店铺分类【编号】

	public static $gradeId_d;	//店铺等级

	public static $storeAddress_d;	//地址编号

	public static $userId_d;	//店主【编号】

	public static $storeState_d;	//店铺状态【0关闭，1开启，2审核中】

	public static $storeSort_d;	//店铺排序

	public static $startTime_d;	//店铺营业开始时间

	public static $endTime_d;	//店铺营业结束时间

	public static $status_d;	//推荐【0为否，1为是，默认为0】

	public static $themeId_d;	//店铺当前主题

	public static $storeCollect_d;	//店铺收藏数量

	public static $printDesc_d;	//打印订单页面下方说明文字

	public static $storeSales_d;	//店铺销量

	public static $freePrice_d;	//超出该金额免运费【大于0才表示该值有效】

	public static $decorationSwitch_d;	//店铺装修开关【0-关闭 装修编号-开启】

	public static $decorationOnly_d;	//开启店铺装修【仅显示店铺装修(1-是 0-否】

	public static $imageCount_d;	//店铺装修相册图片数量

	public static $isOwn_d;	//是否自营店铺 【1是 0否】

	public static $buildAll_d;	//自营店是否绑定全部分类【 0否1是】

	public static $barType_d;	//店铺商品页面左侧显示类型【 0默认1商城相关分类品牌商品推荐】

	public static $isDistribution_d;	//是否分销店铺【0-否，1-是】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $type_d;	//店铺类型【0个人入驻 1企业入驻】

	public static $storeLogo_d;	//店铺logo

	public static $announcement_d;	//店铺公告

	public static $keyWords_d;	//店铺关键词

	public static $description_d;	//店铺简介

	public static $alipayAccount_d;	//支付宝账号

	public static $bankAccount_d;	//银行卡号

	public static $credibility_d;	//信誉
	
	public static $mobile_d;	//手机号

	public static $personName_d;    //联系人姓名
	

	public static $shopLong_d;	//开店时长

	
    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;

    }

    /**
     * @name 获取首页展示店铺
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取首页展示店铺
     * @updated 2017-12-19
     */
    public function getAllstore(){
        $where = [
            "store_state" => '1',
            "status"      =>  '1',
        ];
        $list = $this
            ->field('id,shop_name')
            ->where($where)
            ->limit(12)
            ->select();
        if( false === $list){
            return false;
        }
        return $list;
    }
    /**
     * @name 获取商城公告详情
     * @author 山东重友汽车科技有限公司 刘嘉强 < QQ:547999455 >
     * @des 获取商城公告
     * @updated 2017-12-18
     */
    public function getOneShopAnnouncement($id){
        $where['id'] = $id;
        $list = $this
            ->field( 'id,title' )
            ->where($where)
            ->find();
        if( false === $list){
            return false;
        }
        return $list;
    }
    public function getbanner(){
        return $this
            ->field( 'id,title,pic_url' )
            ->where( array( 'ad_space_id' => 1 ) )
            ->limit( 3 )
            ->select();
    }
    public function getShopId($shopName){
        $where = [
            "shop_name" => ['like', '%' . $shopName . '%'],
        ];
        $field = 'id';
        $shopid = $this->where($where)->field($field)->select();
        if (!empty($shopid)){
            $id = "";
            foreach ($shopid as $key=>$value){
                $id.= $value['id'].',';
            }
            $id=trim($id,',');
            $idArray = explode(',',$id);
            return $idArray;
        }
    }

    /**
     * 获取店铺的基本信息
     *
     */
    public function getstoreInfo($storeId){
        $where['id'] = $storeId;
        $field = 'shop_name,store_logo,type,user_id,description,store_address,FROM_UNIXTIME(create_time,\'%Y-%m-%d\') as update_time,mobile';
        $data = $this->where($where)->field($field)->find();
        return $data;
    }
    
    public function setCollect($id){
        $where['id'] = $id;
        $result = $this->where($where)->setInc("store_collect");
        return $result;
    }
    public function getAddress($id){
        return CommonModel::store_address()->get_address($id);

    }

     //获取企业信息
    public function getStoreByWhere($where,$field,$page,$order){
        $count = $this->where($where)->count();
        $data = $this->field($field)->where($where)->order($order)->page($page.",10")->select(); 
        $Page = new \Think\Page($count,10);
        $page = $Page->show();
        $totalPages =$Page->totalPages;
        return array('totalPages'=>$totalPages,"data"=>$data); 
    }
    //获取店铺名(二维数组)
    public function getStoreName($store){
        foreach ($store as $key => $value) {
            $where['id'] = $value['store_id'];
            $store[$key]['shop_name'] = $this->where($where)->getField('shop_name');
        }
        return $store;
    }
    //获取店铺名(一维数组)
    public function getStoreNameByStoreID($store){
       
        $where['id'] = $store['store_id'];
        $store['shop_name'] = $this->where($where)->getField('shop_name');
       
        return $store;
    }
}