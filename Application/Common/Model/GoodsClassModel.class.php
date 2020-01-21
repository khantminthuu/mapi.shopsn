<?php
namespace Common\Model;
use Common\Model\GoodsModel;
use Common\Model\BrandModel;
/**
 * 商品模型
 */
class GoodsClassModel extends BaseModel
{

    private static $obj;


	public static $id_d;	//id

	public static $className_d;	//分类名字

	public static $createTime_d;	//创建时间

	public static $sortNum_d;	//排序

	public static $updateTime_d;	//更新时间 

	public static $hideStatus_d;	//是否显示【1 显示  0隐藏】

	public static $picUrl_d;	//图片

	public static $fid_d;	//父id

	public static $type_d;	//1为商品 2旅游 3合伙人 4会员

	public static $shoutui_d;	//是否推荐【1 为推荐   0为不推荐】

	public static $isShow_nav_d;	//是否显示在导航栏0：是；1：否

	public static $description_d;	//分类介绍

	public static $cssClass_d;	//css样式

	public static $hotSingle_d;	//热卖单品【1表示是，2表示否】

	public static $isPrinting_d;	//是否推荐打印耗材【1表示是，0表示否】

	public static $isHardware_d;	//是否办公硬件推荐【1表示是，0表示否】

	public static $pcUrl_d;	//pc 广告分类图


    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !( self::$obj instanceof $class ) ? new self() : self::$obj;
    }

    /**
     *  获取首页推荐商品
     *
     */

    public function getRecommendGoods($className){
        return  $this
        ->where(
            array( 'class_name' => "$className" )
        )
        ->getField( 'id' );
    }

    /**
     *  得到所有的商品分类
     *
     */
    public function getAllClass($fid){
        $firstfield = array('id', 'class_name');
        $field = array('id','class_name','pic_url');
            //得到二级
        $secondWhere['fid'] = $fid;
        $secondWhere['hide_status'] = 1;
        $second = $this->where($secondWhere)->field($firstfield)->select();
        foreach ($second as $keys =>$values){
            $threeWhere['fid'] = $values['id'];
            $threeWhere['hide_status'] = 1;
            $three = $this->where($threeWhere)->field($field)->select();
            $second[$keys]['three'] = $three;
        }
        return $second;
    }
    /*
    *khantminthu
    */
    public function getClass1($data){
        $field = ['id','class_name'];
        $secondField = ['id','class_name','pic_url'];
        $where['fid'] = $data['fid'];
        $where['p_id'] = $data['p_id'];
        $where['hide_status'] = 1;
        $class = $this->where($where)->field($field)->select();
        foreach ($class as $key => $value) {
            $secondWhere['fid'] = $value['id'];
            $secondWhere['hide_status'] = 1;
            $secondClass = $this->where($secondWhere)->field($secondField)->select();
            $brand = M('Brand')->where(['goods_class_id'=> $value['id'] , 'status'=>1])->field('brand_name,brand_logo')->select();
            $class[$key]['detail'] = $secondClass;
            $class[$key]['brand'] = $brand;

        }
        return $class;
    }

    public function getClass($data){

        $img = M('nav_category')->where(['id'=>$data['pid']])->field('pic_url')->find();
        $arr['img'] = $img['pic_url'];
        $where['fid'] = $data['fid'];
        $where['pid'] = $data['pid'];
        $field = ['id','class_name'];
        $secondField = ['id','class_name','pic_url'];
        $classname = $this->where($where)->field('class_name')->find();
        $arr['classname'] = $classname['class_name'];
        $class = $this->where($where)->field('id')->select();
        foreach ($class as $key => $value) {
            $secondWhere['fid'] = $value['id'];
            $secondWhere['hide_status'] = 1;
            $secondClass = $this->where($secondWhere)->field($secondField)->select();
            $brand = M('Brand')->where(['status'=>1])->field('brand_name,brand_logo')->select();
        }
        $arr['detail'] = $secondClass;
        $arr['brand_name'] = $brand[0]['brand_name'];
        $arr['brand'] = $brand;
        return $arr;
    }

    /**
     * 获取所有的一级分类ID
     *
     */
    public function getAllClassId(){
        $firstwhere['fid'] = 0;
        $firstwhere['hide_status'] = 1;
        $firstfield = array('id', 'class_name');
        $firstClass = $this->where($firstwhere)->field($firstfield)->select();
        if (empty($firstClass)){
            return false;
        }
        return $firstClass;
    }

    /**
     * 获取店铺的所有商品分类
     *
     */
    public function getStoreAllClass($fid,$store_id){

        $fids = $this->handleIdArray($fid);
        $field = array('id','class_name');
        //得到二级
        $secondWhere['fid'] = array("in",$fids);
        $secondWhere['hide_status'] = 1;
        $secondWhere['store_id'] = $store_id;
        $second = $this->where($secondWhere)->field($field)->select();
        $three = $this->getLastClass($second,$store_id);
        return $three;
    }
    public function getLastClass($fid,$store_id){
        $fids = $this->handleIdArray($fid);
        $field = array('id','class_name');
        //得到三级
        $secondWhere['fid'] = array("in",$fids);
        $secondWhere['hide_status'] = 1;
        $secondWhere['store_id'] = $store_id;
        $second = $this->where($secondWhere)->field($field)->select();
        return $second;
    }

    /**
     * 得到所有的需要查询的ID
     *
     */
    public function handleIdArray($firstClassId){
        $ids='';
        foreach($firstClassId as $key => $v){
            $ids.=$v['id'].',';
        }
        $ids=trim($ids,',');
        $class_id=explode(',',$ids);
        return $class_id;
    }

    /**
     *  顶级商品分类Id  用顶级ID获取这个分类下面第三级的商品Id
     *
     */
    public function  handleChild($fids){
        // 得到第二级
        $secondfield = array('id');
        $where['fid'] = $fids['id'];
        $where['hide_status'] = 1;
        $secondids= $this->where($where)->field($secondfield)->select();
        $ids = '';
        foreach ($secondids as $key=>$value){
            $lastWhere['fid'] = $value['id'];
            $lastWhere['hide_status'] = 1;
            $lastClass = $this->where($lastWhere)->field($secondfield)->select();
            foreach ($lastClass as $key =>$value){
                $ids.=$value['id'].',';
            }
        }
        return $ids;
    }
    /**
     * 得到首页推荐的商品类和4个商品
     *
     */
    public function getRecommendClasssAndGoods(){
        // 得到推荐的品类的名称
        $goods = new GoodsModel();
        $where = [
            'hide_status'  => '1',
            'fid'           => '0'
        ];
        $filed = 'id,class_name,pic_url'; 
        $first = $this->where($where)->field($filed)->order("sort_num")->select();
        foreach ($first as $key => $value){
            $first[$key]['goods'] = $goods ->getIndexGoods($value['id']);
        }
        return $first;
    }
    public function getGoods($first){

        foreach ($first as $key => $value ){
            //为每个以及获一个推荐的二级和三级以及获取4个商品
            $third    =  $this->getNext($this->getNext($value['id']));



        }

    }

    public function getNext($id){
        $secondwhere = [
            'fid' => $id,
            'shoutui'       => '1',
            'hide_status'  => '1'
        ];
        $field = 'id';
        $id = $this->where($secondwhere)->field($field)->find()['id'];
        return $id;
    }

    public function get_show_nav(){
        $where = [
            'is_show_nav' => 1,
            'fid'          => ["NEQ",0],
        ];
        $field = 'id,class_name';
        $data  = $this->where($where)->field($field)->select();
        return $data;
    }





}